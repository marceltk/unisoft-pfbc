<?php

namespace PFBC\Element;

class Url extends Textbox
{
    protected $_attributes = ["type" => "text"];

    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Url;
        parent::render();
    }
}
