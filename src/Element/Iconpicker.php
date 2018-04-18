<?php

namespace PFBC\Element;

class Iconpicker extends \PFBC\Element
{

    protected $_attributes = ["type" => "text"];

    public function render()
    {
        // Bootstrap 3
        if (!$this->hasClass("form-control")) {
            $this->appendAttribute("class", "form-control");
        }

        // Bootstrap 3
        if (!$this->hasClass("input-sm")) {
            $this->appendAttribute("class", "input-sm");
        }

        $name = $this->getAttribute("name");
        //$this->setAttribute("name",$name."_texto");

        $value = $this->getAttribute("value");
        $bt = "<div>";
        $bt .= '<button class="btn btn-default "  name="' . $name . '" data-iconset="glyphicon" data-icon="' . $value
               . '" data-placement="right" role="iconpicker"></button>';
        $bt .= "</div>";
        echo $bt;
//        parent::render();
    }

    public function getJSFiles()
    {
        $arr[] = ('../module/Application/media/js/iconset-glyphicon.js');
        $arr[] = ('../module/Application/media/js/iconset-fontawesome.js');
        $arr[] = ('../module/Application/media/js/bootstrap-iconpicker.js');

        return $arr;
    }

}
