<?php

namespace PFBC\Element;

class Select extends \PFBC\OptionElement
{
    protected $_attributes = [];

    public function render()
    {
        /**
         * Removendo a caracteristica de Lupa caso tenha.
         */
        if ($this->_dd['caracteristica_lupa']) {
            unset($this->_dd['caracteristica_lupa']);
        }

        if (isset($this->_attributes["value"])) {
            if (!is_array($this->_attributes["value"])) {
                $this->_attributes["value"] = [
                    $this->_attributes["value"],
                ];
            }
        } else {
            $this->_attributes["value"] = [];
        }

        if (!empty($this->_attributes["multiple"]) && substr($this->_attributes["name"], -2) != "[]") {
            $this->_attributes["name"] .= "[]";
        }

        echo "<div style='margin-bottom:5px;'>";
        echo '<select' . $this->getAttributes([
                                                  "value",
                                                  "selected",
                                              ]) . '>';
        $selected = false;
        foreach ($this->options as $value => $text) {
            $value = $this->getOptionValue($value);
            echo '<option value="' . $this->filter($value) . '"';
            if (!$selected && in_array($value, $this->_attributes["value"], true)) {
                echo ' selected="selected"';
                $selected = true;
                $texto_selecionado = $text;
            }
            echo '>' . $text . '</option>';
        }
        echo '</select>';
        echo "<input type='hidden' name='" . $this->_attributes['name'] . "_texto' id='" . $this->_attributes['name'] . "_texto' value='" . $texto_selecionado . "' />";
        echo "</div>";
    }

    public function renderJs()
    {
        echo "$('#" . $this->_attributes["id"] . "').select2({
                placeholder: \"-- Selecione --\",
                allowClear: true
        });";

        /**
         * 20/04/2015 - Marcel Silva
         * Populando o campo hidden com o valor Texto selecionado.
         */
        echo "$('#" . $this->_attributes["id"] . "').change(function(){
            $('#" . $this->_attributes["id"] . "_texto').val($(this).children('option:selected').text());
         });";

        if ($this->_attributes['readonly']) {
            echo "setTimeout(function(){\$('#" . $this->_attributes["id"] . "').select2('readonly',true);},100);";
        }
    }
}
