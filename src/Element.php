<?php

namespace PFBC;

use Application\Util\ApplicationOptions;

abstract class Element extends Base
{

    const ENTITY_MANAGER_NAME = ApplicationOptions::ENTITY_MANAGER_NAME;

    protected $_errors = [];
    protected $_attributes = [];
    protected $_form;
    protected $label;
    protected $shortDesc;
    protected $longDesc;
    protected $validation = [];
    protected $agrupado_em; // marcel
    protected $complementos; // marcel
    protected $_dd; // marcel

    protected $properties; // marcel

    public function __construct($label, $name, array $properties = null, $objeto = null)
    {
        $configuration = [
            "label" => $label,
            "name" => $name,
        ];

        /* Merge any properties provided with an associative array containing the label
          and name properties. */
        if (is_array($properties)) {
            $configuration = array_merge($configuration, $properties);
        }

        $this->setProperties((array) $properties);
        $this->configure($configuration);
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * 23/01/2015 - Marcel Silva
     * Verifica a existência de uma classe no elemento.
     */
    public function hasClass($classe)
    {
        $classes = explode(" ", $this->getAttribute("class"));
        if (count($classes)) {
            if (in_array($classe, $classes)) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * Remove uma classe do elemento
     **/
    public function removeClass($string)
    {
        if (!$string) {
            return null;
        }
        $this->setAttribute("class", '');
        $classes = explode(" ", $this->getAttribute("class"));
        if (count($classes)) {
            foreach ($classes as $classe) {
                if ($classe != $string) {
                    $this->appendAttribute("class", $classe);
                }
            }
        }

        return 0;
    }

    public function setDD($dd)
    {
        $this->_dd = $dd;
    }

    public function getDd()
    {
        return $this->_dd;
    }

    // Marcel Silva - 06/08/2013
    public function getCaracteristicas()
    {
        return $this->caracteristicas;
    }

    /* When an element is serialized and stored in the session, this method prevents any non-essential
      information from being included. */

    public function __sleep()
    {
        return ["_attributes", "label", "validation"];
    }

    /* If an element requires external stylesheets, this method is used to return an
      array of entries that will be applied before the form is rendered. */

    public function getCSSFiles()
    {
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    /* If an element requires external javascript file, this method is used to return an
      array of entries that will be applied after the form is rendered. */

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

    /* This method provides a shortcut for checking if an element is required. */

    public function isRequired()
    {
        if (!empty($this->validation)) {
            foreach ($this->validation as $validation) {
                if ($validation instanceof \PFBC\Validation\Required) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getShortDesc()
    {
        return $this->shortDesc;
    }

    /* The isValid method ensures that the provided value satisfies each of the 
      element's validation rules. */

    public function isValid($value, $entityManager = null, $serviceManager = null)
    {
        $valid = true;
        if (!empty($this->validation)) {
            if (!empty($this->label)) {
                $element = $this->label;
            } elseif (!empty($this->_attributes["placeholder"])) {
                $element = $this->_attributes["placeholder"];
            } else {
                $element = $this->_attributes["name"];
            }

            if (substr($element, -1) == ":") {
                $element = substr($element, 0, -1);
            }

            $obj = $this;
            foreach ($this->validation as $validation) {
                if (!$validation->isValid($value, $entityManager, $serviceManager)) {
                    /* In the error message, %element% will be replaced by the element's label (or 
                      name if label is not provided). */
                    $this->_errors[] = str_replace("%element%", $element, $validation->getMessage());
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    /* If an element requires jQuery, this method is used to include a section of javascript
      that will be applied within the jQuery(document).ready(function() {}); section after the
      form has been rendered. */

    public function jQueryDocumentReady()
    {
    }

    /* Elements that have the jQueryOptions property included (Date, Sort, Checksort, and Color)
      can make use of this method to render out the element's appropriate jQuery options. */

    public function jQueryOptions()
    {
        if (!empty($this->jQueryOptions)) {
            $options = "";
            foreach ($this->jQueryOptions as $option => $value) {
                if (!empty($options)) {
                    $options .= ", ";
                }
                $options .= $option . ': ';
                /* When javascript needs to be applied as a jQuery option's value, no quotes are needed. */
                if (is_string($value) && substr($value, 0, 3) == "js:") {
                    $options .= substr($value, 3);
                } else {
                    $options .= var_export($value, true);
                }
            }
            echo "{ ", $options, " }";
        }
    }

    /* Many of the included elements make use of the <input> tag for display.  These include the Hidden, Textbox, 
      Password, Date, Color, Button, Email, and File element classes.  The project's other element classes will
      override this method with their own implementation. */

    public function render()
    {
        if ($this->getAttribute("type") != "button" && $this->getAttribute("type") != "submit") {
            unset($this->_attributes['nova_linha']);
            echo '<input' . $this->getAttributes() . '/>';
        } else {
            // Renderizando button ao invés de input type='button'
            $value = $this->getAttribute('value');
            $this->setAttribute('value', $value);
            $this->setAttribute('name', $this->getAttribute('name'));
            $this->setAttribute('id', $this->getAttribute('id'));
            echo '<button' . $this->getAttributes() . '>' . ($this->icon ? $this->icon : null) . $value . '</button>';
        }
    }

    /* If an element requires inline stylesheet definitions, this method is used send them to the browser before
      the form is rendered. */

    public function renderCSS()
    {
    }

    /* If an element requires javascript to be loaded, this method is used send them to the browser after
      the form is rendered. */

    public function renderJS()
    {
    }

    public function _setForm(Form $form)
    {
        $this->_form = $form;
    }

    public function _getForm()
    {
        return $this->_form;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    /* This method provides a shortcut for applying the Required validation class to an element. */

    public function setRequired($required)
    {
        if (!empty($required)) {
            $this->validation[] = new \PFBC\Validation\Required;
        }
        //$this->_attributes["required"] = "";

        /**
         * Removendo a obrigação de preenchimento do campo.
         */
        if (!$required) {
            foreach ($this->validation as $key => $object) {
                if ($object instanceof \PFBC\Validation\Required) {
                    unset($this->validation[$key]);
                }
            }
        }
    }

    /* This method applies one or more validation rules to an element.  If can accept a single concrete 
      validation class or an array of entries. */

    public function unsetAllValidation()
    {
        unset($this->validation);
    }

    public function setValidation($validation)
    {
        /* If a single validation class is provided, an array is created in order to reuse the same logic. */
        if (!is_array($validation)) {
            $validation = [$validation];
        }
        foreach ($validation as $object) {
            /* Ensures $object contains a existing concrete validation class. */
            if ($object instanceof \PFBC\Validation) {
                $this->validation[] = $object;
                if ($object instanceof \PFBC\Validation\Required) {
                    $this->_attributes["required"] = "";
                }
            }
        }
    }
}
