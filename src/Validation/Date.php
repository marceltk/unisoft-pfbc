<?php

namespace PFBC\Validation;

class Date extends \PFBC\Validation
{
    protected $message = "Erro: %element% não contém uma data válida.";

    public function isValid($value)
    {
        try {
            $date = new \DateTime(str_replace("/", "-", $value));

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
