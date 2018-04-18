<?php

namespace PFBC\View;

class Consulta extends \PFBC\View
{

    protected $class = "";

    public function render()
    {
        $this->_form->appendAttribute("class", $this->class);

        echo '<form', $this->_form->getAttributes(), ' style="font-size:12px;">';
        $this->_form->getErrorView()->render();

        $elements = $this->_form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for ($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];

            if ($element instanceof \PFBC\Element\Hidden || $element instanceof \PFBC\Element\HTML) {
                $element->render();
            } elseif ($element instanceof \PFBC\Element\Button) {
                if ($e == 0 || !$elements[($e - 1)] instanceof \PFBC\Element\Button) {
                    echo '<div class="form-actions">';
                } else {
                    echo ' ';
                }

                $element->render();

                if (($e + 1) == $elementSize || !$elements[($e + 1)] instanceof \PFBC\Element\Button) {
                    echo '</div>';
                }
            } else {
                // Para casos de Periodos (entre datas)
                $element_name = $element->getAttribute("name");
                if ($elements[($e + 1)]) {
                    $prox_element_name = $elements[($e + 1)]->getAttribute("name");
                }
                if ($elements[($e + 1)] instanceof \PFBC\Element\Date && $element instanceof \PFBC\Element\Date && ($element_name . "_fim") == $prox_element_name) {
                    echo '<div class="form-group" style="float:left;margin-right:15px;">';
                    $this->renderLabel($element);
                    $element->render();
                    $this->renderDescriptions($element);
                    echo '</div>';
                } else {
                    echo '<div class="form-group">';
                    $this->renderLabel($element);
                    $element->render();
                    $this->renderDescriptions($element);
                    echo '</div>';
                }

                ++$elementCount;
            }
        }

        echo '</form>';
    }

    protected function renderLabel($element)
    {
        $label = $element->getLabel();
        if (!empty($label)) {
            echo '<label class="control-label" for="' . $element->getAttribute("id") . '">';
            echo $label . '</label>';
        }
    }
}
