<?php

namespace PFBC\View;

class SideBySide extends \PFBC\View
{
    protected $class = "";

    public function render()
    {
        $this->_form->appendAttribute("class", $this->class);

        echo '<form', $this->_form->getAttributes(), '>';
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
                echo '<div class="form-group">';
                $this->renderLabel($element);
                //echo "<div class='col-sm-10'>";
                $element->render();
                //echo "</div>";
                $this->renderDescriptions($element);
                echo '</div>';
                ++$elementCount;
            }
        }

        echo '</form>';
    }

    protected function renderLabel($element)
    {
        $label = $element->getLabel();
        if (!empty($label)) {
            echo '<label class="control-label" for="', $element->getAttribute("id"), '">';
            if ($element->isRequired()) {
                echo '<span class="required">* </span>';
            }
            echo $label, '</label>';
        }
    }
}
