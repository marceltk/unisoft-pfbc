<?php

namespace PFBC\Element;

class TipoPessoa extends \PFBC\Element\Radio
{

    public function __construct($label, $name, array $properties = null, $objeto = null)
    {
        $options = [
            "F" => "Física",
            "J" => "Jurídica",
        ];

        if (!is_array($properties)) {
            $properties = ["inline" => 1];
        } elseif (!array_key_exists("inline", $properties)) {
            $properties["inline"] = 1;
        }

        parent::__construct($label, $name, $options, $properties, $objeto);
    }

}
