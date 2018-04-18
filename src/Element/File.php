<?php

namespace PFBC\Element;

class File extends \PFBC\Element
{

    protected $_attributes = ["type" => "file"];

    public function render()
    {
        $this->_attributes['required'] = true;
        parent::setValidation([new \PFBC\Validation\ValidaExtensaoArquivo()]);
        parent::render();
    }

}
