<?php

namespace PFBC\Element;

class YesNo extends Radio
{

    public function __construct($label, $name, array $properties = null, $objeto = null)
    {
        $options = [
            "S" => "Sim",
            "N" => "Não",
        ];

        if (!is_array($properties)) {
            $properties = ["inline" => 1];
        } elseif (!array_key_exists("inline", $properties)) {
            $properties["inline"] = 1;
        }

        parent::__construct($label, $name, $options, $properties, $objeto);
    }

}
