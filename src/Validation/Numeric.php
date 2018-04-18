<?php

namespace PFBC\Validation;

class Numeric extends \PFBC\Validation
{
    protected $message = "Erro: %element% não é um número válido.";

    public function isValid($value)
    {
        if ($this->isNotApplicable($value) || is_numeric($value)) {
            return true;
        }

        return false;
    }
}
