<?php

namespace PFBC;

abstract class View extends Base
{

    protected $_form;

    public function __construct(array $properties = null)
    {
        $this->configure($properties);
    }

    public function _setForm(Form $form)
    {
        $this->_form = $form;
    }

    /* jQuery is used to apply css entries to the last element. */

    public function jQueryDocumentReady()
    {
    }

    public function render()
    {
    }

    public function renderCSS()
    {
        echo 'label span.required { color: #cc0000; font-size:12px;font-weight:bold; padding:0 3px;}';
        echo 'span.help-inline, span.help-block { color: #888; font-size: .9em; font-style: italic; }';
    }

    protected function renderDescriptions($element)
    {
        $shortDesc = $element->getShortDesc();

        // Dica
        if (!empty($shortDesc)) {
            //echo '<span class="help-inline" title="' . $shortDesc . '"></span>';
            echo '<span data-toggle="tooltip" data-placement="top" title="' . $shortDesc . '"><span class="glyphicon glyphicon-info-sign"></span></span>&nbsp;';
        }

        $longDesc = $element->getLongDesc();
        // Ajuda
        if (!empty($longDesc)) {
            echo '<span data-toggle="popover" title="" data-content="' . $longDesc . '"><i class="glyphicon glyphicon-question-sign"></i></span>&nbsp;';
        }
    }

    public function renderJS()
    {
    }

    protected function renderLabel(\PFBC\Element $element)
    {
    }
}
