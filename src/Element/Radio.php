<?php

namespace PFBC\Element;

class Radio extends \PFBC\OptionElement
{
    protected $_attributes = ["type" => "radio"];
    protected $inline = true;

    public function render()
    {
        if ($this->_attributes["inline"] === false) {
            $this->inline = false;
        }

        //$labelClass = $this->_attributes["type"];
        if (!empty($this->inline)) {
            $labelClass .= "radio-inline";
        }

        echo "<div>";

        $count = 0;
        foreach ($this->options as $value => $text) {
            $value = $this->getOptionValue($value);
            if (!$this->inline) {
                echo "<div class='radio no-margin-top'>";
            }
            echo '<label class="' . $labelClass . '" style=""> <input id="' . $this->_attributes["id"] . '-' . $count . '"' . $this->getAttributes([
                                                                                                                                                       "id",
                                                                                                                                                       "value",
                                                                                                                                                       "checked",
                                                                                                                                                   ]) . ' value="' . $this->filter($value) . '"';

            if (isset($this->_attributes["value"]) && $this->_attributes["value"] == $value) {
                echo ' checked="checked"';
            }
            echo '/> ', $text, ' </label> ';
            if (!$this->inline) {
                echo "</div>";
            }
            ++$count;
        }

        echo "</div>";
    }

}
