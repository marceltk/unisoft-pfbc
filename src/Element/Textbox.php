<?php

namespace PFBC\Element;

class Textbox extends \PFBC\Element
{

    protected $_attributes = [
        "type" => "text",
    ];
    protected $prepend;
    protected $append;

    public function render()
    {
        // Bootstrap 3
        if (!$this->hasClass("form-control")) {
            $this->appendAttribute("class", "form-control");
        }

        // Bootstrap 3
        if (!$this->hasClass("input-sm")) {
            $this->appendAttribute("class", "input-sm");
        }

        $addons = [];

        /**
         * Caracteristica Lupa - 19/08/2014
         */
        if ($this->_dd['caracteristica_lupa']) {
            if (!$this->complementos) {
                $this->complementos = $this->_config['dd'][$this->_attributes['name']]['complementos'];
            }

            if (is_array($this->complementos)) {
                $jsLupa = $this->getJsLupa();
            }

            $arrLupaDelete = json_decode(urldecode($jsLupa));

            $apagar = [];
            if ($arrLupaDelete->retornar) {
                foreach ($arrLupaDelete->retornar as $retorno) {
                    if ($retorno) {
                        $apagar[] = $retorno;
                    }
                }
            }

            if ($this->_dd['caracteristica_lupa']['permite_edicao'] !== false && !$this->_dd['ide_desabilitado']) {
                //$this->append = "<button type='button' class='btn btn-default btn-xs' id='lupa-".$this->_attributes['id']."' >&nbsp;<i class='icon-search'></i>&nbsp;</button>";
                $this->append = "<a href=\"javascript:void(0);\" class=\"btn btn-link btn-xs\" id='lupa-" . $this->_attributes['id'] . "'><span class=\"glyphicon glyphicon-search\"></span></a>";
                //$this->append .= "<button type='button' class='btn btn-xs btn-danger' onclick=\"MsfwLupaDeleta('" . implode(",", $apagar) . "');\">&nbsp;<i class='icon-white icon-trash'></i>&nbsp;</button>";
                $this->append .= "<a href=\"javascript:void(0);\" class=\"btn btn-link btn-xs\" onclick=\"MsfwLupaDeleta('" . implode(",", $apagar) . "');\"><span class=\"glyphicon glyphicon-trash\"></span></a>";
            }
        }

        if (!$this->_dd['ide_somente_leitura'] && $this->_attributes['readonly'] && isset($this->_dd['ide_somente_leitura'])) {
            unset($this->_attributes['readonly']);
        }

        if (!empty($this->prepend)) {
            $addons[] = "input-prepend";
        }
        if (!empty($this->append)) {
            $addons[] = "input-group input-sm";
        }
        if (!empty($addons)) {
            echo '<div class="' . implode(" ", $addons) . " " . $this->renderClassSize() . '" style="padding:0;">';
        }

        $this->renderAddOn("prepend");

        parent::render();
        $this->renderAddOn("append");

        if (!empty($addons)) {
            echo '</div>';
        }
    }

    /**
     * Identifica o tamanho do campo e recupera a classe.
     * Faz com que a div fique do tamanho correto do input;
     */
    public function renderClassSize()
    {
        $classes = explode(" ", $this->getAttribute("class"));
        /**
         * Ao adicionar uma classe de tamanho, inserir neste array tbm.
         */
        $array = [
            "input-mini",
            "input-small",
            "input-medium",
            "input-large",
            "input-xlarge",
            "input-xxlarge",
            "input-xxxlarge",
        ];
        if (count($classes)) {
            foreach ($classes as $classe) {
                if (in_array(trim($classe), $array)) {
                    $retorno .= $classe;
                }
            }
        }

        return $retorno;
    }

    protected function renderAddOn($type = "prepend")
    {
        if (!empty($this->$type)) {
            $span = true;
            /*
              if(strpos($this->$type, "<button") !== false)
              $span = false;
             */
            if ($span) {
                echo '<span class="input-group-addon" style="padding:0;">';
            }

            echo $this->$type;

            if ($span) {
                echo '</span>';
            }
        }
    }

    /**
     * 18/08/2014 - Marcel Silva
     * Para elementos com caracteristica de Lupa/Pesquisa
     * */
    private function getJsLupa()
    {
        return trim(urlencode(json_encode($this->complementos)));
    }

    public function renderJs()
    {
        if ($this->_dd['caracteristica_lupa']) {
            echo <<<JS

        $('#lupa-{$this->_attributes['id']}').click(function(){

            var form = $("#amb_form_id").val();
            var urlLupa,jsonLupa;

            if (!urlLupa) {
                urlLupa = "/application/applupa";
            }
            if (urlLupa) {
                showLoading();
                $.ajax({
                    type: "post",
                    url: urlLupa,
                    data: "lupa_config_file={$this->_config_dir}&lupa_campo={$this->_attributes['id']}&"+$("#"+form).serialize()+"&form=null",
                    dataType: "html",
                    context: document.body,
                    success: function(data) {
                        //$("#msfw-extra").html(data);

                        var lupa = new BootstrapDialog({
                            title: 'Pesquisa',
                            message: $(data),
                            size: BootstrapDialog.SIZE_WIDE,
                            buttons: [{
                                cssClass: 'btn-default btn-sm',
                                label: 'Fechar',
                                size: 'size-wide',
                                action: function(dialogRef){
                                    dialogRef.close();
                                    //BootstrapDialog.closeAll();
                                }
                            }]
                        });
                        lupa.open();
                        lupa.setData('lupa','1');

                    },
                    complete: function(data) {
                        hideLoading();
                    }
                }).done(function() {
                });
            }
        });
JS;
        }
    }

}
