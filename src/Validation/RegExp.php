<?php

namespace PFBC\Validation;

class RegExp extends \PFBC\Validation
{
    protected $message = "Erro: %element% contém carateres inválidos.";
    protected $pattern;

    public function __construct($pattern, $message = "")
    {
        $this->pattern = $pattern;
        parent::__construct($message);
    }

    public function isValid($value)
    {
        if ($this->isNotApplicable($value) || preg_match($this->pattern, $value)) {
            return true;
        }

        return false;
    }
}
