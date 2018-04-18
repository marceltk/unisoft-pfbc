<?php

namespace PFBC\Validation;

class Cep extends \PFBC\Validation
{

    protected $message = "Erro: o valor digitado no campo <strong>%element%</strong> não é válido!";

    public function isValid($value)
    {
        if ($value) {
            if (strlen(str_replace("-", "", $value)) < 8) {
                return false;
            }
        }

        return true;
    }
}
