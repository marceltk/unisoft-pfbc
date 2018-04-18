<?php

namespace PFBC\Validation;

class Cnpj extends \PFBC\Validation
{

    protected $message = "Erro: o documento digitado no campo <strong>%element%</strong> não é válido!";

    public function isValid($cnpj)
    {
        if (!$cnpj) {
            return true;
        }

        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        $cnpj = (string) $cnpj;
        $cnpj_original = $cnpj;
        $primeiros_numeros_cnpj = substr($cnpj, 0, 12);

        $primeiro_calculo = $this->multiplica_cnpj($primeiros_numeros_cnpj);
        $primeiro_digito = ($primeiro_calculo % 11) < 2 ? 0 : 11 - ($primeiro_calculo % 11);
        $primeiros_numeros_cnpj .= $primeiro_digito;

        $segundo_calculo = $this->multiplica_cnpj($primeiros_numeros_cnpj, 6);
        $segundo_digito = ($segundo_calculo % 11) < 2 ? 0 : 11 - ($segundo_calculo % 11);

        $cnpj = $primeiros_numeros_cnpj . $segundo_digito;

        if ($cnpj === $cnpj_original) {
            return true;
        }
    }

    public function multiplica_cnpj($cnpj, $posicao = 5)
    {
        $calculo = 0;
        for ($i = 0; $i < strlen($cnpj); $i++) {
            $calculo = $calculo + ($cnpj[$i] * $posicao);
            $posicao--;
            if ($posicao < 2) {
                $posicao = 9;
            }
        }

        return $calculo;
    }
}
