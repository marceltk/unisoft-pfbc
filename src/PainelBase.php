<?php

namespace PFBC;

abstract class PainelBase extends Element
{

    protected $_errors = [];
    protected $_attributes = [];
    protected $_form;
    protected $label;
    protected $shortDesc;
    protected $longDesc;
    public $dd;

    public function __construct($label, $name, array $properties = null)
    {
        $configuration = [
            "label" => $label,
            "name" => $name,
        ];
        if (is_array($properties)) {
            $configuration = array_merge($configuration, $properties);
        }

        $this->configure($configuration);
    }

    public function setDD()
    {
        /**
         * Nothing Here
         */
    }

    public function __sleep()
    {
        return ["_attributes", "label", "validation"];
    }

    public function getCSSFiles()
    {
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getJSFiles()
    {
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getLongDesc()
    {
        return $this->longDesc;
    }

    public function getShortDesc()
    {
        return $this->shortDesc;
    }

    public function jQueryDocumentReady()
    {
    }

    public function render()
    {
    }

    public function renderCSS()
    {
    }

    public function renderJS()
    {
    }

    public function _setForm(Form $form)
    {
        $this->_form = $form;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }
}
