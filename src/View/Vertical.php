<?php

namespace PFBC\View;

class Vertical extends \PFBC\View
{

    public function render()
    {
        echo '<form', $this->_form->getAttributes(), ' style="font-size:12px">';
        $this->_form->getErrorView()->render();

        $elements = $this->_form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for ($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];

            $element_name = $element->getAttribute("name");
            if (!$element_name) {
                $element_name = $e;
            }
            if ($element instanceof \PFBC\Element\Hidden) {
                $element->render();
                continue;
            }

            if ($element instanceof \PFBC\Element\Button) {
                if ($e == 0 || !$elements[($e - 1)] instanceof \PFBC\Element\Button) {
                    echo "<div style='display:block;clear:left;'></div>";
                    echo '<div class="form-actions" style="overflow:hidden;clear:both;display:block;">';
                } else {
                    echo ' ';
                }
                $element->render();
                if (($e + 1) == $elementSize || !$elements[($e + 1)] instanceof \PFBC\Element\Button) {
                    echo '</div>';
                }
            } else {
                if (!$element instanceof \PFBC\Element\Hidden && !$elements[$e] instanceof \PFBC\Element\Hidden) {
                    if ($elements[$e]) {
                        if (!$elements[$e]->getAttribute('nova_linha')) {
                            $divIni = "<div id='auto_cmp_" . $element_name . "' class='campo-" . $e . "' style='float:left;margin-right:10px'>";
                        } else {
                            echo "<div style='display:block;clear:left;'></div>";
                            $divIni = "<div id='auto_cmp_" . $element_name . "' class='campo-" . $e . "' style='float:left;margin-right:10px;overflow:hidden'>";
                        }
                    }
                }

                //Tipo Histórico sempre terá de ser em nova linha
                if ($element instanceof \PFBC\Element\Historico) {
                    $divIni = "<div id='auto_cmp_" . $element_name . "' class='campo-" . $e . "' style='width:100%;float:none;margin-left:0;margin-top:10px;display:block;clear:left;'>";
                }
                // Quando for o tipo agrupador e estiver em nova linha ou um campo do tipo HTML
                if ($element instanceof \PFBC\Element\Agrupador || $element instanceof \PFBC\Element\Painel) {
                    $divIni = "<div id='auto_cmp_" . $element_name . "' class='campo-" . $e . "' style='float:none;margin-left:0;margin-top:10px;display:block;clear:left;'>";
                }
                if ($element instanceof \PFBC\Element\HTML) {
                    $divIni = "<div id='auto_cmp_" . $element_name . "' class='campo-" . $e . " ' style='float:none;margin-left:0;margin-top:10px;display:block;clear:left;'>";
                }

                // Quando for o tipo editor de texto
                if ($element instanceof \PFBC\Element\CKEditor || $element instanceof \PFBC\Element\TinyMCE) {
                    $divIni = "<div id='auto_cmp_" . $element_name . "' class='campo-" . $e . " " . ($element->getAttribute('class') ? $element->getAttribute('class') : 'input-full') . "' style='margin-top:10px;margin-left:0;float:none;'>";
                }

                echo $divIni;

                /**
                 * Nova implementação para conter paineis.
                 */
                if ($element instanceof \PFBC\Element\Painel) {
                    echo "<div style='display:block;clear:left;' class='controls campo-" . $e . "'></div>";

                    $shortDesc = $element->getShortDesc();
                    $dica = null;
                    if ($shortDesc) {
                        $dica = '<span data-toggle="tooltip" data-placement="top" title="' . $shortDesc . '" style="font-size:12px;"><span class="glyphicon glyphicon-info-sign"></span></span>&nbsp;';
                    }
                    //echo "<fieldset class='fieldset-painel'><legend>" . $dica . $element->getLabel() . "</legend>";
                    echo "<div class='panel panel-default'>";
                    echo "<div class='panel-heading bg-dark-light'><strong>" . $dica . $element->getLabel() . "</strong></div>";
                    echo "<div class='panel-body fieldset-agrupador' style='margin: 10px;'>";

                    $Form = $element->_getForm();
                    if (count($element->_elements)) {
                        $i = 0;
                        foreach ($element->_elements as $el) {
                            if (!is_object($el)) {
                                continue;
                            }

                            $element_name = $el->getAttribute("name");

                            if ($el instanceof \PFBC\Element\Hidden) {
                                $el->render();
                                $i++;
                                continue;
                            }

                            if(method_exists($element->_elements[$i], "getAttribute")){
                                if (!$element->_elements[$i]->getAttribute('nova_linha')) {
                                    $divIni2 = "<div id='auto_cmp_" . $element_name . "' class='campo-" . $e . "-" . $i . "' style='float:left;margin-right:10px'>";
                                } else {
                                    echo "<div style='display:block;clear:left;'></div>";
                                    $divIni2 = "<div id='auto_cmp_" . $element_name . "' class='campo-" . $e . "-" . $i . "' style='float:left;margin-right:10px;overflow:hidden'>";
                                }
                            }

                            // Campos HTML dentro do Painel.
                            if ($element->_elements[$i] instanceof \PFBC\Element\HTML) {
                                $divIni2 = "<div id='auto_cmp_" . $element_name . "' class='campo-" . $e . " ' style='float:none;margin-left:0;margin-top:10px;display:block;clear:left;'>";
                            }

                            echo $divIni2;

                            /**
                             * Bootstrap 3 Adaptação. 23/01/2015
                             */
                            if ($el instanceof \PFBC\Element\Checkbox) {
                                $this->renderLabel($el);
                                if (!$el->isInline()) {
                                    echo "<div class='checkbox'>";
                                    $Form->addElement($el);
                                    $el->render();
                                    echo "</div>";
                                } else {
                                    echo "<div>";
                                    $Form->addElement($el);
                                    $el->render();
                                    echo "</div>";
                                }
                            }/* elseif ($el instanceof \PFBC\Element\Radio) {
                                $this->renderLabel($element);
                                echo "<div>";
                                if (!$element->isInline()) {
                                    echo "<div class='radio'>";
                                }
                                $Form = $element->_getForm();
                                $Form->addElement($el);
                                $el->render();
                                echo "</div>";
                            } elseif ($el instanceof \PFBC\Element\Select || $el instanceof \PFBC\Element\UF) {
                                $this->renderLabel($el);
                                echo "<div>";
                                $Form = $element->_getForm();
                                $Form->addElement($el);
                                $el->render();
                                echo "</div>";
                            }*/ elseif ($el instanceof \PFBC\Element\HTML) {
                                $this->renderLabel($el);
                                $Form = $element->_getForm();
                                $Form->addElement($el);
                                $el->render();
} else {
    echo "<div class=\"form-group form-group-sm\">";
    //$el->appendAttribute("class", "form-control input-sm");
    $this->renderLabel($el);
    $Form = $element->_getForm();
    $Form->addElement($el);
    $el->render();
    echo "</div>";
}

                            echo "</div>";
                            $i++;
                        }
                    }
                    echo "</div>";
                    echo "</div>";
                    echo "<div style='display:block;clear:left;'></div>";
                } else {
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
                    }/* elseif ($element instanceof \PFBC\Element\Radio) {
                        $this->renderLabel($element);
                        echo "<div>";
                        if (!$element->isInline()) {
                            echo "<div class='radio'>";
                        }
                        $element->render();
                        echo "</div>";
                    } elseif ($element instanceof \PFBC\Element\Select || $element instanceof \PFBC\Element\UF) {
                        $this->renderLabel($element);
                        echo "<div>";
                        $element->render();
                        echo "</div>";
                    }*/ elseif ($element instanceof \PFBC\Element\HTML) {
                        $this->renderLabel($element);
                        $element->render();
} else {
    echo "<div class=\"form-group form-group-sm\">";
    //$element->appendAttribute("class", "form-control input-sm");
    $this->renderLabel($element);
    $element->render();
    echo "</div>";
}
                }

                if (!$element instanceof \PFBC\Element\Hidden) {
                    echo "</div>";
                    //echo "</div>";
                }
                ++$elementCount;
            }
        }

        echo '</form>';
    }

    protected function renderLabel(\PFBC\Element $element)
    {
        if (!$element instanceof \PFBC\Element\Hidden && !$element instanceof \PFBC\Element\Agrupador && !$element instanceof \PFBC\Element\HTML) {
            $label = trim($element->getLabel());
            if (!$label) {
                return null;
            }
            echo '<label for="', $element->getAttribute("id"), '">';
            $this->renderDescriptions($element);
            if (!empty($label)) {
                if ($element->isRequired()) {
                    echo '<span class="required" title="Campo de preenchimento obrigatório.">*</span> ';
                }
                echo "<strong>" . $label . "</strong>";
            }
            // alterado o tipo para renderizar as descrições dentro do label.

            echo '</label>';
        }
    }
}
