<?php

namespace PFBC\Element;

class Decimal extends \PFBC\Element\Textbox
{

    protected $_attributes = ["type" => "text"];

    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Numeric;
        parent::render();
    }

    public function renderJs()
    {
        if (!$this->_dd['complementos']['decimais']) {
            $this->_dd['complementos']['decimais'] = 2;
        }
        $inteiras = str_pad(9, ($this->_dd['length'] - $this->_dd['complementos']['decimais']), 9);
        $decimais = str_pad(9, $this->_dd['complementos']['decimais'], 9);
        $modelo = $decimais . "." . $inteiras;
        //echo '$("#'.$this->_attributes['id'].'").mask(\''.$modelo.'\');' . PHP_EOL;
        echo '$("#' . $this->_attributes['id'] . '").setMask({ mask : \'' . $modelo . '\', type: \'reverse\', setSize: false});' . PHP_EOL;
    }

}
