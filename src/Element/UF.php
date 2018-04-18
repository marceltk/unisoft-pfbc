<?php

namespace PFBC\Element;

class UF extends \PFBC\OptionElement
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

        $options = [
            "" => "- Selecione UF -",
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins',
        ];

        // somente as siglas
        if ($this->_attributes['sigla']) {
            $i = 0;
            foreach ($options as $key => $value) {
                $options[$key] = $key;
            }
            $options[''] = "--";
        }

        $this->options = $options;
        if (isset($this->_attributes["value"])) {
            if (!is_array($this->_attributes["value"])) {
                $this->_attributes["value"] = [$this->_attributes["value"]];
            }
        } else {
            $this->_attributes["value"] = [''];
        }

        if (!empty($this->_attributes["multiple"]) && substr($this->_attributes["name"], -2) != "[]") {
            $this->_attributes["name"] .= "[]";
        }

        echo "<div>";
        echo '<select', $this->getAttributes(["value", "selected"]), ' id=' . $this->_attributes["name"] . '>';
        $selected = false;
        foreach ($this->options as $value => $text) {
            $value = $this->getOptionValue($value);
            echo '<option value="', $this->filter($value), '"';
            if (!$selected && in_array($value, $this->_attributes["value"])) {
                echo ' selected="selected"';
                $selected = true;
            }
            echo '>', $text, '</option>';
        }
        echo '</select>';
        echo "</div>";
    }

    public function renderJs()
    {
        echo "$('#" . $this->_attributes["id"] . "').select2({
        allowClear: true
        });";

        if ($this->_attributes['readonly']) {
            echo "setTimeout(function(){\$('#" . $this->_attributes["id"] . "').select2('readonly',true);},100);";
        }
    }

}
