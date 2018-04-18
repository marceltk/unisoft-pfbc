<?php

namespace PFBC\Element;

class Painel extends \PFBC\Element
{

    public $dd = [];

    public function __construct($label, $name, array $properties = null)
    {
        parent::__construct($label, $name, $properties);
    }

    public function jQueryDocumentReady()
    {
    }

    public function render()
    {
    }

    public function addElement($Element, $Form = null)
    {
        $this->_elements[] = $Element;
    }

    public function getElements()
    {
        return $this->_elements;
    }
}
