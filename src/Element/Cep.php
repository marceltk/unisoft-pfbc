<?php

namespace PFBC\Element;

class Cep extends \PFBC\Element\Textbox
{

    protected $_attributes = ["type" => "text", "alt" => "cep"];

    public function render()
    {
        //if($this->_attributes['value']) {
        $this->validation[] = new \PFBC\Validation\Cep;
        //}
        parent::render();
    }
}
