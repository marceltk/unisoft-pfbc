<?php

namespace PFBC\Validation;

class Database extends \PFBC\Validation
{

    protected $message = "Erro: o Valor digitado no campo <strong>%element%</strong> já existe cadastrado no sistema.";
    protected $_base;
    protected $_sql;

    public function __construct($msg = '', $parametros = '', $primaryKey = null)
    {
        $this->_base = $base;
        $this->_parametros = $parametros;
        $this->_primaryKey = $primaryKey;
    }

    public function isValid($value, $entityManager = null)
    {
        if ($value) { // valida somente se tiver valor
            //$front = Zend_Controller_Front::getInstance();

            if (count($this->_parametros)) {
                if ($this->_parametros['mensagem']) {
                    $this->mensagem = $this->_parametros['mensagem'];
                }
                $sql = $this->_parametros['sql'];
                $filtros = $this->_parametros['filtros'];
                $Entity = $this->_parametros['entity'];
            }

            if ($sql) {
                $em = $entityManager; //entity manager       
                //$em = $front->getRequest()->_em; //entity manager       

                if (is_object($Entity)) {
                    $arrFiltros = str_replace(['%value%', '%primaryKey%'], [$value, $Entity->getId()], $filtros);
                } else {
                    $arrFiltros = str_replace(['%value%', '%primaryKey%'], [$value, "'" . $this->_primaryKey . "'"], $filtros);
                }
                // Monta o filtro
                if (is_array($arrFiltros)) {
                    foreach ($arrFiltros as $fil) {
                        $filtro2 .= $fil;
                    }
                }

                $sql = str_replace(['[filtros]'], $filtro2, $sql);
                $query = $em->getConnection()->prepare($sql);
                $query->execute();
                $itens = $query->fetchColumn();

                if ($itens >= 1) {
                    return false;
                }
            }
        }

        return true;
    }
}
