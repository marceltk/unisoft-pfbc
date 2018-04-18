<?php

namespace PFBC\Element;

class Email extends Textbox
{
    protected $_attributes = ["type" => "text"];

    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Email;
        parent::render();
    }
}
