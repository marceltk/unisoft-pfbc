<?php

namespace PFBC\Element;

class jQueryUIDate extends Textbox
{

    protected $_attributes = [
        "type" => "text",
        "autocomplete" => "off",
    ];
    protected $jQueryOptions;

    public function getCSSFiles()
    {
        return [];
    }

    public function getJSFiles()
    {
        return [];
    }

    public function jQueryDocumentReady()
    {
        parent::jQueryDocumentReady();
        if (!$this->getAttribute("readonly")) {
            //if($this->_dd['type'] != "datetime") {
            echo 'jQuery("#', $this->_attributes["id"], '").datepicker({
                        changeMonth: true,
                        changeYear: true,
                        showButtonPanel: false,
                            beforeShow: function() {
                                setTimeout(function(){
                                    $(\'.ui-datepicker\').css(\'z-index\', 9999);
                                }, 0);
                            }
                    });';
            /*} else {
                echo 'jQuery("#', $this->_attributes["id"], '").datetimepicker($.timepicker.regional[\'pt-BR\']);';
            }*/
        }
    }

    public function render()
    {
        if ($this->_dd['type'] != "datetime") {
            $this->_attributes['alt'] = 'date';
            $this->appendAttribute("class", "input-date");
        } else {
            $this->_attributes['alt'] = 'datetime';
            $this->appendAttribute("class", "input-datetime");
        }
        $this->validation[] = new \PFBC\Validation\Date;
        parent::render();
    }

}
