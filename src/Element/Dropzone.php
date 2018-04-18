<?php

namespace PFBC\Element;

class Dropzone extends \PFBC\Element
{

    protected $_attributes = ["type" => "file", "multiple" => true];

    public function render()
    {
        echo "  <div class=\"fallback\">";
        parent::setValidation([new \PFBC\Validation\ValidaExtensaoArquivo()]);
        parent::render();
        echo "</div>";
    }

}
