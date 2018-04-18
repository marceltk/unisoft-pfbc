<?php

namespace PFBC\View;

class Agrupador extends \PFBC\View
{

    public function render()
    {
        echo '<div ' . $this->_form->getAttributes() . ' class="bd-6" style=\'background:#f5f5f5;padding:6px;display:none;\'>';
        $this->_form->getErrorView()->render();

        $elements = $this->_form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for ($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];

            /**
             * Renderizando elementos ocultos no formulário.
             */
            if ($element instanceof \PFBC\Element\Hidden) {
                $element->render();
                continue;
            }

            if ($element instanceof \PFBC\Element\Button) {
                if ($e == 0 || !$elements[($e - 1)] instanceof \PFBC\Element\Button) {
                    echo '<div class="form-actions2" style="clear:both;margin:5px 0;">';
                } else {
                    echo ' ';
                }
                $element->render();
                if (($e + 1) == $elementSize || !$elements[($e + 1)] instanceof \PFBC\Element\Button) {
                    echo '</div>';
                }
            } else {
                if (!$element instanceof \PFBC\Element\Hidden) {
                    if ($elements[$e]) {
                        if (!$elements[$e]->getAttribute('nova_linha')) {
                            $divIni = "<div class='' style='float:left;margin-right:10px'>";
                        } else {
                            echo "<div style='display:block;clear:left;'></div>";
                            $divIni = "<div class='' style='float:left;margin-right:10px'>";
                        }
                    }
                }

                // Quando for o tipo agrupador e estiver em nova linha
                if ($element instanceof \PFBC\Element\Agrupador) {
                    $divIni = "<div class='' style='display:block;clear:left;'>";
                }

                echo $divIni;
                //$this->renderLabel($element);

                // colocando a classe no elemento
                if ($element->isRequired()) {
                    $element->appendAttribute("class", 'required');
                }

                /**
                 * Bootstrap 3 Adaptação. 23/01/2015
                 */
                if ($element instanceof \PFBC\Element\Checkbox) {
                    $this->renderLabel($element);
                    if (!$element->isInline()) {
                        echo "<div class='checkbox'>";
                        $element->render();
                        echo "</div>";
                    } else {
                        echo "<div>";
                        $element->render();
                        echo "</div>";
                    }
                } elseif ($element instanceof \PFBC\Element\Radio) {
                    $this->renderLabel($element);
                    echo "<div>";
                    if (!$element->isInline()) {
                        echo "<div class='radio'>";
                    }
                    $element->render();
                    echo "</div>";
                } elseif ($element instanceof \PFBC\Element\Select || $el instanceof \PFBC\Element\UF) {
                    $this->renderLabel($element);
                    echo "<div>";
                    $element->render();
                    echo "</div>";
                } elseif ($element instanceof \PFBC\Element\HTML) {
                    $this->renderLabel($element);
                    $element->render();
                } else {
                    echo "<div class=\"form-group form-group-sm\">";
                    $element->appendAttribute("class", "form-control input-sm");
                    $this->renderLabel($element);
                    $element->render();
                    echo "</div>";
                }

                // $element->render();
                if (!$element instanceof \PFBC\Element\Hidden) {
                    echo "</div>";
                }
                ++$elementCount;
            }
        }

        echo '</div>';
    }

    protected function renderLabel(\PFBC\Element $element)
    {
        $label = $element->getLabel();
        echo '<label for="', $element->getAttribute("id"), '">';
        // alterado o tipo para renderizar as descrições dentro do label.
        $this->renderDescriptions($element);
        if (!empty($label)) {
            if ($element->isRequired()) {
                echo '<span class="required" title="Campo de preenchimento obrigatório.">*</i></span> ';
            }
            echo "<strong>" . $label . "</strong>";
        }
        echo '</label>';
    }
}
