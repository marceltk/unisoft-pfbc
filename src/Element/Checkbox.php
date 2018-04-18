<?php

namespace PFBC\Element;

class Checkbox extends \PFBC\OptionElement
{
    protected $_attributes = ["type" => "checkbox"];
    protected $inline;

    public function render()
    {
        $this->_attributes["value"] = explode(",", $this->_attributes["value"]);

        if (isset($this->_attributes["value"])) {
            if (!is_array($this->_attributes["value"])) {
                $this->_attributes["value"] = [$this->_attributes["value"]];
            }
        } else {
            $this->_attributes["value"] = [];
        }

        if (substr($this->_attributes["name"], -2) != "[]") {
            $this->_attributes["name"] .= "[]";
        }

        //$labelClass = $this->_attributes["type"];
        if (!empty($this->inline)) {
            $labelClass = $this->_attributes["type"] . "-inline";
        }

        $count = 0;
        foreach ($this->options as $value => $text) {
            $value = $this->getOptionValue($value);

            echo '<label class="', $labelClass, '"> <input id="', $this->_attributes["id"], '"', $this->getAttributes(["id", "value", "checked", "required"]), ' value="', $this->filter($value), '"';
            if (in_array($value, $this->_attributes["value"])) {
                echo ' checked="checked"';
            }
            echo '/> ', $text, ' </label> ';
            ++$count;
        }
    }
}
