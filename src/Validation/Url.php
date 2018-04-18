<?php

namespace PFBC\Validation;

class Url extends \PFBC\Validation
{

    protected $message = "Erro: %element% não contém uma URL válida (ex: http://www.google.com).";

    public function isValid($value)
    {
        if ($this->isNotApplicable($value) || filter_var($value, FILTER_VALIDATE_URL)) {
            return true;
        }

        return false;
    }

}
