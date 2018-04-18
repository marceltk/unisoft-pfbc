<?php

namespace PFBC\Element;

class Cpf extends Textbox
{

    protected $_attributes = ["type" => "text", "alt" => "cpf"];

    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Cpf;
        parent::render();
    }
}
