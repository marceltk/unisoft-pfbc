<?php

namespace PFBC\Element;

class Textarea extends \PFBC\Element
{
    protected $_attributes = ["rows" => "8"];

    public function render()
    {
        /**
         * Removendo a caracteristica de Lupa caso tenha.
         */
        if ($this->_dd['caracteristica_lupa']) {
            unset($this->_dd['caracteristica_lupa']);
        }

        // Bootstrap 3
        if (!$this->hasClass("form-control")) {
            $this->appendAttribute("class", "form-control");
        }

        // Bootstrap 3
        if (!$this->hasClass("input-sm")) {
            $this->appendAttribute("class", "input-sm");
        }

        echo "<textarea", $this->getAttributes("value"), " style='min-height:120px;'>";
        if (!empty($this->_attributes["value"])) {
            echo $this->filter($this->_attributes["value"]);
        }
        echo "</textarea>";
    }

    public function getJSFiles()
    {
        if ($this->getAttribute("maxlength") > 0) {
            return [
                "../../../../module/Application/media/js/bootstrap-maxlength.js",
            ];
        }
    }

    public function jQueryDocumentReady()
    {
        parent::jQueryDocumentReady();
        if ($this->getAttribute("maxlength") > 0 && !$this->getAttribute("disabled") && !$this->getAttribute("readonly")) {
            echo 'jQuery("#', $this->_attributes["id"], '").maxlength({
                alwaysShow: true,
                threshold: 10,
                warningClass: "label label-success",
                limitReachedClass: "label label-important",
                postText: " caracteres restantes. ",
                validate: true                
            });';
        }
    }

}
