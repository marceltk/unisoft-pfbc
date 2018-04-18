<?php

namespace PFBC\Element;

use ServiceLocatorFactory\ServiceLocatorFactory;

class Upload extends \PFBC\Element
{

    protected $_attributes = ["type" => "text", "readonly" => true];
    protected $prepend;
    protected $append;
    protected $nome_arquivo;

    public function renderUploadName()
    {
        if ($this->_attributes['value'] > 0) {
            if (!$this->_form->serviceLocator) {
                $this->_form->serviceLocator = ServiceLocatorFactory::getInstance();
            }

            $em = $this->_form->serviceLocator->get('Doctrine\ORM\EntityManager');

            $Appupload = $em->getRepository("Application\Entity\Appupload")->find($this->_attributes['value']);
            if ($Appupload) {
                $this->_attributes['value'] = $Appupload->getNomeOriginal();
                $this->hash = $Appupload->getNome();
                $this->_ext = $Appupload->getExt();
            }
        }
        $this->nome_arquivo = $this->_attributes['value'];
    }

    public function getNomeArquivo()
    {
        return $this->nome_arquivo;
    }

    public function render()
    {
        $this->_attributes['class'] = "upload";
        $id_upload = $this->_attributes['value'];
        $this->renderUploadName();

        if ($this->isRequired()) {
            $this->appendAttribute("class", "required");
        }

        $this->appendAttribute("class", "form-control input-sm");

        echo "<input type='hidden' value='" . $id_upload . "' name='" . $this->_attributes['name'] . "' id='" . $this->_attributes['id'] . "' />";

        $addons = [];

        // Envia um novo arquivo.
        if (!isset($this->_dd['complementos']['ide_permite_upload']) || $this->_dd['complementos']['ide_permite_upload']) {
            //$this->append = "<button type='button' class='btn btn-default btn-sm' onclick='MsfwUpload(\"" . $this->_attributes['id'] . "\");'><i class='glyphicon glyphicon-arrow-up'></i></button>";
            $this->append = '<a href="javascript:void(0)" class="btn btn-link btn-xs" onclick="MsfwUpload(\'' . $this->_attributes['id'] . '\');"><span class="glyphicon glyphicon-open"></span></a>';
        } else {
            //$this->append = "<button type='button' class='btn btn-default btn-sm disabled' disabled='disabled'><i class='icon-arrow-up'></i></button>";
            $this->append = '<a href="javascript:void(0)" class="btn btn-link btn-xs"><span class="glyphicon glyphicon-open"></span></a>';
        }

        // Efetua o download do arquivo/imagem que está em anexo.
        if ((!isset($this->_dd['complementos']['ide_permite_download']) || $this->_dd['complementos']['ide_permite_download'])) {
            //$this->append .= "<button type='button' class='btn btn-default btn-sm' onclick='MsfwDownload(\"" . $this->_attributes['id'] . "\");'><i class='glyphicon glyphicon-download-alt'></i></button>";
            $this->append .= '<a href="javascript:void(0)" class="btn btn-link btn-xs" onclick="MsfwDownload(\'' . $this->_attributes['id'] . '\',null,\'' . $this->hash . '\');"><span class="glyphicon glyphicon-save"></span></a>';
        } else {
            // Efetua o download do arquivo/imagem que está em anexo.
            //$this->append .= "<button type='button' class='btn btn-default btn-sm disabled' disabled='disabled'><i class='glyphicon glyphicon-download-alt'></i></button>";
            $this->append .= '<a href="javascript:void(0);" class="btn btn-link btn-xs disabled"><span class="glyphicon glyphicon-save"></span></a>';
        }

        // Exclui somente o que está no campo texto, não exclui o upload de fato.
        if ((!isset($this->_dd['complementos']['ide_permite_exclusao']) || $this->_dd['complementos']['ide_permite_exclusao'])) {
            //$this->append .= "<button type='button' class='btn btn-default btn-danger btn-sm' onclick=\"MsfwExcluiUpload('" . $this->_attributes['id'] . "','" . $id_upload . "');\"><i class='glyphicon glyphicon-trash icon-white'></i></button>";
            $this->append .= '<a href="javascript:void(0)" class="btn btn-link btn-xs" onclick="MsfwExcluiUpload(\'' . $this->_attributes['id'] . '\',\'' . $id_upload . '\');"><span class="glyphicon glyphicon-remove"></span></a>';
        } else {
            //$this->append .= "<button type='button' class='btn btn-default btn-danger btn-sm disabled' disabled='disabled'><i class='glyphicon glyphicon-trash icon-white'></i></button>";
            $this->append .= '<a href="javascript:void(0)" class="btn btn-link btn-xs"><span class="glyphicon glyphicon-remove"></span></a>';
        }

        if (in_array($this->_ext, ['png', 'jpg', 'gif', 'jpeg'])) {
            // Visualizar
            $this->append .= '<a href="javascript:void(0);" onclick="MsfwVisualizarImagem(\'' . $id_upload . '\')" class="btn btn-link btn-xs"><span     class="icon14 glyphicon glyphicon-picture"></span></a>';
        }
        //$this->append = "<span class=\"input-group-btn\" role=\"group\">" . $this->append . "</span>";

        if (!empty($this->prepend)) {
            $addons[] = "input-prepend";
        }
        if (!empty($this->append)) {
            $addons[] = "input-group input-sm";
        }
        if (!empty($addons)) {
            echo '<div class="MsfwdivUpload ', implode(" ", $addons), ' input-xlarge" style="padding:0px;">';
        }

        // alterando o nome do campo principal
        $this->_attributes['id'] = $this->_attributes['id'] . "_texto";
        $this->_attributes['name'] = $this->_attributes['name'] . "_texto";

        $this->renderAddOn("prepend");
        $this->appendAttribute("class", "input-xlarge");
        parent::render();
        $this->renderAddOn("append");

        if (!empty($addons)) {
            echo '</div>';
        }
    }

    public function renderCss()
    {
        echo ".MsfwdivUpload a {cursor:pointer;}";
    }

    protected function renderAddOn($type = "prepend")
    {
        if (!empty($this->$type)) {
            $span = true;
            if (strpos($this->$type, "<button") !== false) {
                $span = false;
            }

            if ($span) {
                echo '<span class="input-group-addon" style="padding:0;x">';
            }

            echo $this->$type;

            if ($span) {
                echo '</span>';
            }
        }
    }

}
