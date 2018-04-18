<?php

namespace PFBC\Element;

class Lupa extends \PFBC\Element\Textbox
{

    protected $_attributes = ["type" => "text", "readonly" => true];
    protected $prepend;
    protected $append;
    protected $complementos;
    public $_config;

    public function render()
    {
        $this->_dd['caracteristica_lupa'] = true;
        parent::render();
    }

}
