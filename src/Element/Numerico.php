<?php

namespace PFBC\Element;

class Numerico extends \PFBC\Element\Textbox
{
    protected $_attributes = ["type" => "text", "alt" => "numeric"];

    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Numeric;
        parent::render();
    }
}
