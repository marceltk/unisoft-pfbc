<?php

namespace PFBC\Element;

class Autocomplete extends \PFBC\Element
{

    protected $_attributes = ["type" => "hidden"];

    public function __construct($label, $name, $value = "", array $properties = null, $dd = null)
    {
        if (!is_array($properties)) {
            $properties = [];
        }

        if (!empty($value)) {
            $properties["value"] = $value;
        }
        $this->setAttribute("id", $name);
        $this->_dd = $dd;

        // Encriptando os parametros.
        $ZCrypt = new \Msfw\ZCrypt;
        $result = $ZCrypt->encrypt(json_encode($this->_dd));
        $this->_dd_crypt = urlencode($result);

        parent::__construct($label, $name, $properties);
    }

    public function render()
    {
        echo "<div class=''>";
        parent::render();
        echo "</div>";
    }

    public function renderJs()
    {
        $complementos = $this->_dd['complementos'];

        if (!$complementos['campoChave']) {
            $complementos['campoChave'] = "id";
        }

        if (!$complementos['dataType']) {
            $complementos['dataType'] = "json";
        }

        if (!$complementos['url']) {
            $complementos['url'] = "/application/ajax/autocomplete";
        }

        echo 'var dd_crypt = \'' . $this->_dd_crypt . '\';';
        echo "$('#" . $this->_attributes["id"] . "').select2({
            placeholder: \"-- Selecione --\",
            allowClear:true,
            minimumInputLength: 2,
            ajax: {
            url: \"" . $complementos['url'] . "\",
            dataType: '" . $complementos['dataType'] . "',
            quietMillis: 250,
            type: 'POST',
            data: function (term, page) {
                return {
                    q: term,
                    config: dd_crypt,
                };
            },
            results: function (data, page) {
                return { results: data };
            },
            },
            initSelection: function(element, callback) {
                var id = $(element).val();
                if (id !== '' && id != 0) {
                    $.ajax('" . $complementos['url'] . "', {
                    method : 'post',
                    traditional : true,
                    context: document.body,
                    data : '" . $complementos['campoChave'] . "='+id+'&config='+dd_crypt,
                    dataType: '" . $complementos['dataType'] . "',
                    }).done(function(data) { callback(data[0]); });
                } else {
                    $(element).val('');
                }
            },
        });";

        if ($this->_attributes['readonly']) {
            echo "setTimeout(function(){\$('#" . $this->_attributes["id"] . "').select2('readonly',true);},100);";
        }
    }
}
