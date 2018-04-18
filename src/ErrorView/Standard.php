<?php

namespace PFBC\ErrorView;

class Standard extends \PFBC\ErrorView
{

    public function applyAjaxErrorResponse()
    {
        $id = $this->_form->getAttribute("id");
        echo <<<JS
		var errorSize = response.errors.length;
        //if(errorSize == 1)
        //var errorFormat = "Foi encontrado um erro na validação dos dados, veja abaixo";
        //else
		var errorFormat = "Foram encontrados erros na validação dos dados";

		var errorHTML = '<div class="alert alert-danger alert-form"><a class="close" data-dismiss="alert" href="#">×</a><strong class="alert-heading"><a href="#" class="btn btn-link btn-xs"></a><span class="glyphicon glyphicon-exclamation-sign"></span> ' + errorFormat + ':</strong><ul>';
		for(e = 0; e < errorSize; ++e) {
            console.log(response.errors[e]);
            if(response.errors[e]){
			     errorHTML += '<li>' + response.errors[e] + '</li>';
            }
        }
		errorHTML += '</ul></div>';
        jQuery("#$id").prepend(errorHTML);

        // Retirando o valor do campo digitado para segurança adicional.
        if(jQuery("#amb_password").length){
		  jQuery("#amb_password").val('');
        }
JS;
    }

    /***
     * Trata array de profundidade 1 para erros personalizados.
     * Cada linha (mensagem) é um array, onde a validação em sí poderá ter 1 ou mais mensagens de erro.
     **/
    private function parse($errors)
    {
        $list = [];
        if (!empty($errors)) {
            $keys = array_keys($errors);
            $keySize = sizeof($keys);
            for ($k = 0; $k < $keySize; ++$k) {
                if (count($errors[$keys[$k]][0]) > 1) {
                    foreach ($errors[$keys[$k]][0] as $l) {
                        $list[] = $l;
                    }
                } else {
                    $list[] = $errors[$keys[$k]][0];
                }
            }
        }

        return $list;
    }

    public function render()
    {
        $errors = $this->parse($this->_form->getErrors());
        if (!empty($errors)) {
            $size = sizeof($errors);
            $errors = implode("</li><li>", $errors);

            if ($size == 1) {
                $format = " erro encontrado";
            } else {
                $format = $size . " erros encontrados";
            }
            //				<strong class="alert-heading">Erro no envio do formulário, veja o(s) $format:</strong>
            echo <<<HTML
			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert" href="#">×</a>
				<strong class="alert-heading"><a href="#" class="btn btn-link btn-xs"><span class="glyphicon glyphicon-exclamation-sign"></span></a> Atenção, foram encontrados alguns problemas.</strong>
				<ul><li>$errors</li></ul>
			</div>
HTML;
        }
    }

    public function renderAjaxErrorResponse()
    {
        $errors = $this->parse($this->_form->getErrors());
        if (!empty($errors)) {
            header("Content-type: application/json");
            echo json_encode(["errors" => $errors]);
        }
    }
}
