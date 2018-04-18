<?php

namespace PFBC\Validation;

use Zend\Session\Container;

class ValidaExtensaoArquivo extends \PFBC\Validation
{

    protected $message = "Erro: %element% possui um arquivo com extensão inválida!";

    public function isValid($value)
    {
        if (!$value) {
            return false;
        }
        $pathinfo = pathinfo($value);
        $s = new Container('Msfw_Parametros');

        $Appparametro = unserialize($s->parametros);
        $extensoes = $Appparametro->getExtPermitida();

        $retorno = false;
        if ($extensoes) {
            $arrExtensoes = explode(",", $extensoes);
            foreach ($arrExtensoes as $ext) {
                if (strtolower($ext) == strtolower($pathinfo['extension'])) {
                    $retorno = true;
                    break;
                }
            }
        }

        return $retorno;
    }
}
