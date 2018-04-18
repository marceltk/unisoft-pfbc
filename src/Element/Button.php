<?php

namespace PFBC\Element;

class Button extends \PFBC\Element
{

    protected $_attributes = ["type" => "submit", "value" => "Salvar"];
    protected $icon;

    public function __construct($label = "Salvar", $type = "", array $properties = null)
    {
        if (!is_array($properties)) {
            $properties = [];
        }

        if (!empty($type)) {
            $properties["type"] = $type;
        }

        $class = "btn btn-sm";
        if ((empty($type) || $type == "submit") && empty($properties["class"])) {
            $class .= " btn-primary";
        } else {
            $class .= " btn-default";
        }

        if (!empty($properties["class"])) {
            if (!strstr($properties["class"], "btn-xs")) {
                $properties["class"] .= " " . $class;
            }
        } else {
            $properties["class"] = $class;
        }

        if (empty($properties["value"])) {
            $properties["value"] = $label;
        }

        parent::__construct("", "", $properties);
    }

    public function renderJs()
    {
        echo "$('#" . $this->_attributes['id'] . "').click(function(){
            $('#btn_acionado').val('" . strtolower($this->_attributes['name']) . "');
        });";
    }
}
