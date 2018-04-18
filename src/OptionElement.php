<?php

namespace PFBC;

abstract class OptionElement extends Element
{

    protected $options;

    public function __construct($label, $name, array $options, array $properties = null)
    {
        $this->options = $options;
        /*if (!empty($this->options) && array_values($this->options) === $this->options){
        $this->options = array_combine($this->options, $this->options);
        }*/
        parent::__construct($label, $name, $properties);
    }

    protected function getOptionValue($value)
    {
        $position = strpos($value, ":pfbc");
        if ($position !== false) {
            if ($position == 0) {
                $value = "";
            } else {
                $value = substr($value, 0, $position);
            }
        }

        return $value;
    }

    public function getOptionText($value)
    {
        if (strstr($value, ',')) {
            $retorno = [];
            $values = explode(",", $value);
            foreach ($values as $value) {
                $retorno[] = $this->options[$value];
            }

            return implode(",", $retorno);
        }

        if (!trim($value) && (string) $value !== '0') {
            return "--";
        }

        return $this->options[$value];
    }

    /**
     * Bootstrap 3 - 23/01/2015
     */
    public function isInline()
    {
        return $this->inline;
    }
}