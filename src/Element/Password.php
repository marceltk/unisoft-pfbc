<?php

namespace PFBC\Element;

class Password extends Textbox
{
    protected $_attributes = ["type" => "password"];
    protected $prefillAfterValidation = 0;
}
