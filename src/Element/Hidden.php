<?php

namespace PFBC\Element;

class Hidden extends \PFBC\Element
{
    protected $_attributes = ["type" => "hidden"];

    public function __construct($name, $value = "", array $properties = null)
    {
        if (!is_array($properties)) {
            $properties = [];
        }

        if (!empty($value)) {
            $properties["value"] = $value;
        }

        $this->setAttribute("id", $name);

        parent::__construct("", $name, $properties);
    }
}
