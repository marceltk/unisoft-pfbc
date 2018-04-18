<?php

namespace PFBC\Element;

class HTML extends \PFBC\Element
{
    public function __construct($value, $dd)
    {
        if ($dd['complementos']['entities']) {
            $value = htmlspecialchars($value);
        }
        if ($dd['complementos']['pre-fixado']) {
            $value = "<pre>" . $value . "</pre>";
        }
        $properties = ["value" => $value];
        parent::__construct("", "", $properties);
    }

    public function render()
    {
        echo $this->_attributes["value"];
    }
}
