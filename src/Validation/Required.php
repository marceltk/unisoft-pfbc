<?php

namespace PFBC\Validation;

class Required extends \PFBC\Validation
{

    protected $message = "Erro: <strong>%element%</strong> é de preenchimento obrigatório.";

    public function isValid($value)
    {
        $valid = false;
        if (!is_null($value) && ((!is_array($value) && $value !== "") || (is_array($value) && !empty($value)))) {
            $valid = true;
        }

        return $valid;
    }

}
