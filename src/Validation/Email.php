<?php

namespace PFBC\Validation;

class Email extends \PFBC\Validation
{
    protected $message = "Erro: <strong>%element%</strong> não é um e-mail válido.";

    public function isValid($value)
    {
        if ($this->isNotApplicable($value) || filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }
}
