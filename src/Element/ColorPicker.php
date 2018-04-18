<?php

namespace PFBC\Element;

class ColorPicker extends Textbox
{

    protected $_attributes = ["type" => "text"];

    public function render()
    {
        echo '<div class="input-medium">';
        echo '<div class="input-group ' . $this->_attributes["id"] . '">';
        parent::render();
        echo "<span class=\"input-group-addon input-sm\"><i></i></span>";
        echo '</div>';
        echo '</div>';
    }

    public function renderJs()
    {
        echo "$(function(){
                $('." . $this->_attributes["id"] . "').colorpicker();";
        if ($this->_attributes['readonly']) {
            echo "$('." . $this->_attributes["id"] . "').colorpicker('disable');";
        }
        echo "});";
    }
}
