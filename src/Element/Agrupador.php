<?php

namespace PFBC\Element;

class Agrupador extends \PFBC\Element
{

    public $dd = []; // são os campos (Objetos) que serão organizados no agrupador.
    public $tabela; // necessita enviar no cfg ou se utilizado fora do framework automático, necessita setar a tabela.
    public $entity; // necessita enviar no cfg a entidade para poder pegar a chave primária.
    public $tabela_campo; // campo de relacionamento
    public $rotina_pre; // campo de relacionamento
    public $rotina_pos; // campo de relacionamento
    public $ide_permite_exclusao = 'S';
    public $ide_permite_alteracao = 'S';
    public $ide_permite_inclusao = 'S';
    public $frontController;

    /**
     * $dd é um array contendo instancias de objetos (Campos) do PFBC Form, não recebe simplesmente um array de configurações
     * Intenção posterior é fazer tudo isso em cada campo para pegar somente a instancia do campo com todos os dados.
     */

    public function __construct($dd, $label, $name, array $properties = null)
    {
        if ($dd) {
            $this->dd = $dd;
        }
        parent::__construct($label, $name, $properties);
        $this->Util = new \Application\Controller\Plugin\CommonUtil();
    }

    public function setForm($Form, $frontController = false)
    {
        $this->frontController = $frontController;
        $this->Form = $Form;
    }

    /**
     * Colocando funções específicas para cada agrupador, como validações.. etc..
     * A Intenção é isolar cada validação de agrupador.
     */
    public function jQueryDocumentReady()
    {
        $id_form_agrupador = $this->getAttribute('id');
        $id_form = $this->Form->getAttribute('id');

        /**
         * Inserindo um registro do agrupador.
         */
        echo "var ide_permite_inclusao = '" . $this->ide_permite_inclusao . "';";
        echo "var ide_permite_alteracao = '" . $this->ide_permite_alteracao . "';";
        echo "var ide_permite_exclusao = '" . $this->ide_permite_exclusao . "';";
        echo "var campos,erroMsg,bErro;";
        echo "bMensagem = '';";
        echo "var nomecampo = '';";
        echo "var id_form = '" . $id_form . "';\n";
        echo "var campos_form = $('#'+id_form).serialize();\n";

        if ($this->ide_permite_inclusao) {
            echo "$('#" . $id_form_agrupador . "-inserir').click(function(){
                bErro = false;

                // Quantidades Máximas
                if($('#" . $id_form_agrupador . "').val() >= $('#agr_qtde_maxima_" . $id_form_agrupador . "').val() && $('#agr_qtde_maxima_" . $id_form_agrupador . "').val() > 0){
                    bErro = true;
                    erroMsg ='<div class=\"alert alert-warning alert-form\"><strong class=\"alert-heading\"><span class=\"glyphicon glyphicon-exclamation-sign\"></span> <strong>Atenção!</strong> Limite máximo de '+$('#agr_qtde_maxima_" . $id_form_agrupador . "').val()+' registro(s) excedido.</strong><a href=\"#\" data-dismiss=\"alert\" class=\"close\">×</a></div>';
                    $('#fieldset-{$id_form_agrupador} .alert').remove();
                    $('#fieldset-{$id_form_agrupador}').prepend(erroMsg);
                  return false;
                }

				campos = $('#{$id_form_agrupador}-campos').val().split(',');
				erroMsg = '';
					if(campos.length > 0) {
					erroMsg +='<div class=\"alert alert-danger alert-form\"><strong class=\"alert-heading\">Houveram alguns erros ao inserir registros no agrupador, veja abaixo:</strong>';
									erroMsg +='<a href=\"#\" data-dismiss=\"alert\" class=\"close\">×</a>';
					erroMsg +='<ul>';
					$(campos).each(function(i,item){
                        item = item+'_cmp';
                        if($('#'+item+'_texto').length) {
                            item_texto = item+'_texto';
                        }
						if($('#'+item).hasClass('required') && (!$('#'+item).val() || $('#'+item).val() == '0,00')){
							nomecampo = $(\"label[for=\"+item+\"] strong\").text();
                            if(!nomecampo) {
                                nomecampo = $('#'+item).parent().children('label').text().replace('*','');
                                if(!nomecampo){
                                    nomecampo = $('#'+item_texto).parent().parent().parent().children('label').text().replace('*','');
                                }
                            }
							erroMsg += '<li>Erro: O campo <strong>'+nomecampo+'</strong> é de preenchimento obrigatório.</li>';
							bErro = true;
						}
					});
					erroMsg +='</ul></div>';
				}
				if(!bErro) {
					$('#fieldset-{$id_form_agrupador} .alert').remove();
					MsfwAgrupadorInsere('" . $id_form_agrupador . "',campos,id_form,'" . $this->rotina_pre . "','" . $this->rotina_pos . "');\n
				} else {
				$('#fieldset-{$id_form_agrupador} .alert').remove();
				  $('#fieldset-{$id_form_agrupador}').prepend(erroMsg);
				}

			});";
        }

        /**
         * Alterando um registro do agrupador.
         */
        if ($this->ide_permite_alteracao) {
            echo "$('#" . $id_form_agrupador . "-alterar').click(function(){

				var id_linha = $(this).attr('rel');
				campos = $('#{$id_form_agrupador}-campos').val().split(',');
				bErro = false;
				erroMsg = '';

					if(campos.length > 0) {
					erroMsg +='<div class=\"alert alert-danger alert-form\"><strong class=\"alert-heading\">Houveram alguns erros ao alterar registros no agrupador, veja abaixo:</strong>';
									erroMsg +='<a href=\"#\" data-dismiss=\"alert\" class=\"close\">×</a>';
					erroMsg +='<ul>';
					$(campos).each(function(i,item){
                        item = item+'_cmp';
                        if($('#'+item+'_texto').length) {
                            item_texto = item+'_texto';
                        }
						if($('#'+item).hasClass('required') && (!$('#'+item).val() || $('#'+item).val() == '0,00')){
							nomecampo = $(\"label[for=\"+item+\"] strong\").text();
                            if(!nomecampo) {
                                nomecampo = $('#'+item).parent().children('label').text().replace('*','');
                                if(!nomecampo){
                                    nomecampo = $('#'+item_texto).parent().parent().parent().children('label').text().replace('*','');
                                }
                            }
							erroMsg += '<li>Erro: O campo <strong>'+nomecampo+'</strong> é de preenchimento obrigatório.</li>';
							bErro = true;
						}
					});
					erroMsg +='</ul></div>';
				}
				if(!bErro) {
					//MsfwExecutaRotinaMVC('" . $this->rotina_pre . "',campos_form);\n

					$('#fieldset-{$id_form_agrupador} .alert').remove();
					MsfwAgrupadorAlterar('" . $id_form_agrupador . "',id_linha,campos,id_form,'" . $this->rotina_pre . "','" . $this->rotina_pos . "');

					//MsfwExecutaRotinaMVC('" . $this->rotina_pos . "',campos_form);\n

					if(ide_permite_inclusao == 'S') {
						$('#" . $id_form_agrupador . "-inserir').show();
						$('#" . $id_form_agrupador . "-alterar').hide();
					} else {
						$('#div-" . $id_form_agrupador . "').hide();
					}

				} else {
					$('#fieldset-{$id_form_agrupador} .alert').remove();
				  $('#fieldset-{$id_form_agrupador}').prepend(erroMsg);
				}

			});";
        }

        /**
         * Botão cancelar
         **/
        echo "$('#" . $id_form_agrupador . "-cancelar').click(function(){
			showLoading();
     		campos = $('#{$id_form_agrupador}-campos').val().split(',');
			$('#fieldset-{$id_form_agrupador} .alert').remove(); // remove a div de erro quando clicado em cancelar.
			$(campos).each(function(i,item){
                $(\"#\"+item+\"_cmp\").val('').trigger('change');
				$(\"#\"+item+\"_cmp_texto\").val('');
				if(ide_permite_inclusao == 'S') {
					$('#" . $id_form_agrupador . "-inserir').show();
					$('#" . $id_form_agrupador . "-alterar').hide();
				} else {
					$('#div-" . $id_form_agrupador . "').hide();
				}
			});
			//$('input:text').setMask();
            initMasks();
			hideLoading();
		});";

        //echo "MsfwbindDeleteAgrupador('".$id_form_agrupador."','".$this->Form->getAttribute('id')."','". $this->rotina_pre ."','". $this->rotina_pos ."');\n";

        echo "$('.excluir-linha-" . $id_form_agrupador . "').click(function(){
			MsfwAgrupadorDeleta('" . $id_form_agrupador . "',$(this).attr('rel'),'" . $this->ide_permite_exclusao . "');

			//$('form .btn').addClass('disabled');
			//$('form .btn').attr('disabled','disabled');

			campos_form = $('#'+id_form).serialize();
			MsfwExecutaRotinaMVC('" . $this->rotina_pre . "',campos_form+'&evento=before&acao=delete');\n
			MsfwExecutaRotinaMVC('" . $this->rotina_pos . "',campos_form+'&evento=after&acao=delete');\n
            //$('#" . $id_form_agrupador . "_agrupador_id_linha_'+$(this).attr('rel')).remove();

			//$('form .btn').removeClass('disabled');
			//$('form .btn').removeAttr('disabled');
			//$('input:text').setMask();
            initMasks();
			hideLoading();
		});";

        parent::renderJs();
    }

    public function render()
    {
        $id_form_agrupador = $this->getAttribute('id');
        $titles = [];

        // tabela do agrupador.
        echo "<input type='hidden' name='" . $id_form_agrupador . "-tabela' id='" . $id_form_agrupador . "-tabela' value='" . $this->tabela . "' />";

        // titulos da tabela.
        $dd = $this->dd;
        $camposBanco = [];
        foreach ($dd as $key => $value) {
            // Ignorando quando for tipo Hidden, para não colocar uma coluna a mais na tabela.
            if (!$value instanceof \PFBC\Element_Hidden) {
                if (is_array($value)) {
                    $value = $value[1];
                }

                if (method_exists($value, "getLabel")) {
                    $titles[] = $value->getLabel();
                }
            }
            $camposBanco[] = $key;
        }

        $dd_agrupador = $this->getDd();

        $required = null;
        if ($this->isRequired()) {
            $required = '<span class="required" title="Campo de preenchimento obrigatório.">*</span> ';
        }

        // Quantidades
        if ($dd_agrupador['complementos']['qtde_minima'] || $dd_agrupador['complementos']['qtde_maxima']) {
            $descQtde = "<span style='font-size:12px;font-weigt:bold;'> (";
            $qtdeMinima = false;

            if ($dd_agrupador['complementos']['qtde_minima']) {
                $descQtde .= "Qtde. Mínima " . $dd_agrupador['complementos']['qtde_minima'];
                $qtdeMinima = true;
            }
            if ($dd_agrupador['complementos']['qtde_maxima']) {
                if ($qtdeMinima) {
                    $descQtde .= " / ";
                }
                $descQtde .= "Qtde. Máxima " . $dd_agrupador['complementos']['qtde_maxima'];
            }

            $descQtde .= ")</span>";
        }

        //echo "<fieldset class='fieldset-agrupador' id='fieldset-" . $this->getAttribute('id') . "'><legend>".$required . $this->getLabel().$descQtde."</legend>";

        echo "<div class='panel panel-default'>";
        echo "<div class='panel-heading bg-dark-light'><strong>" . $required . $this->getLabel() . $descQtde . "</strong></div>";
        echo "<div class='panel-body fieldset-agrupador' id='fieldset-" . $this->getAttribute('id') . "' style='margin:5px;'>";

        if ($this->_attributes['tamanho_maximo']) {
            $style = "style='overflow:auto;max-height:" . $this->_attributes['tamanho_maximo'] . ";'";
        }

        echo "<div class='content-agrupador' " . $style . ">";
        echo "<a id='ancora-" . $this->getAttribute('id') . "'></a>";

        if (!$this->getAttribute('readonly')) {
            //$Form = $this->_form->serviceLocator->get("PFBC\Form");
            $Form = new \PFBC\Form(null, "div-" . $id_form_agrupador);

            $Form->addElement(new \PFBC\Element\Hidden("form-agrupador", $id_form_agrupador));
            //$Form->setIdForm("div-" . $id_form_agrupador);

            $Form->configure([
                                 "action" => '',
                                 'view' => new \PFBC\View\Agrupador(),
                             ]);

            //$em = $this->frontController->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $Form->serviceLocator = $this->frontController->getServiceLocator();
            foreach ($this->dd as $key => $value) {
                // [0] => Hidden [1] => Textbox (Quando campo Select somente leitura)
                if (is_array($value)) {
                    $value = $value[1];
                }
                $value->setAttribute("name", $value->getAttribute("name") . "_cmp"); // alterando o nome dos campos nativos do agrupador.
                $Form->addElement($value);
                $campos .= $key . ",";
            }

            $Form->addElement(new \PFBC\Element\Button(
                                  "<span class='glyphicon glyphicon-floppy-disk'></span> Inserir", 'button', ["class" => "btn btn-xs btn-default btn-embossed", 'id' => $id_form_agrupador . "-inserir"]
                              )
            );

            $Form->addElement(new \PFBC\Element\Button(
                                  "<span class='glyphicon glyphicon-edit'></span> Alterar", 'button', ["class" => "btn btn-xs btn-default btn-embossed hidden", 'id' => $id_form_agrupador . "-alterar"]
                              )
            );

            $Form->addElement(new \PFBC\Element\Button(
                                  "<span class='glyphicon glyphicon-minus'></span> Cancelar", 'button', ["class" => "btn btn-xs btn-default btn-embossed", 'id' => $id_form_agrupador . "-cancelar"]
                              )
            );

            //$Form->addElement(new Element_Button("<i class='icon-white icon-pencil'></i>Alterar", 'button', array("class" => "btn-info btn-mini", "onclick" => "MsfwAgrupadorAltera('" . $id_form_agrupador . "')")));

            $Form->render();
        } else {
            // Quando o agrupador é somente leitura, teremos de trazer somente o nome dos campos para buscar o resultado do select.
            foreach ($this->dd as $key => $value) {
                $campos .= $key . ",";
            }
        }

        $campos = substr(trim($campos), 0, -1);

        // configurações do agrupador
        $cfg .= "<input type='hidden' name='" . $id_form_agrupador . "-campos' id='" . $id_form_agrupador . "-campos' value='" . $campos . "' />";
        echo $cfg;

        /*
         * Quando existir uma entidade relacionada pegamos todos os valores, isso vem do Util, mas poderá ser enviado separadamente quando necessário criar um formulário personalizado.
         */
        if (is_object($this->entity)) {
            $classe = $this->Util->getDoctrineFieldName($this->tabela);
            $front = $this->frontController;

            $em = $this->frontController->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $ordem = " id ASC";

            /***
             * Considerando ordenação no Agrupador.
             * 21/06/2015 11:13:06
             **/
            $dd_agrupador = $this->getDd();
            if ($dd_agrupador['ordem']) {
                $ordem = $dd_agrupador['ordem'];
                if (is_array($ordem)) {
                    foreach ($ordem as $campo => $direcao) {
                        $ordem_tmp[] .= $campo . " " . $direcao;
                    }
                    $ordem = implode(",", $ordem_tmp);
                    unset($ordem_tmp);
                }
            }

            $sql = "SELECT " . ($campos ? "id," . $campos : "id") . " FROM " . $this->tabela . " WHERE " . $this->tabela_campo . " = " . $this->entity->getId() . " ORDER BY " . $ordem;
            $query = $em->getConnection()->prepare($sql);

            try {
                $query->execute();
                $itens = $query->fetchAll();
            } catch (\Exception $e) {
                print "<pre>";
                print_r($sql);
                print "</pre>";
                print "<pre>";
                print_r($e->getTraceAsString());
                print "</pre>";
            }
        }

        echo "<input type='hidden' name='" . $this->getAttribute('name') . "' id='" . $this->getAttribute('id') . "' value='" . (count($itens) > 0 ? count($itens) : null) . "' />";

        // Quantidades Minima e Máxima
        echo "<input type='hidden' name='agr_qtde_minima_" . $this->getAttribute('name') . "' id='agr_qtde_minima_" . $this->getAttribute('id') . "' value='" . (int) $dd_agrupador['complementos']['qtde_minima'] . "' />";

        echo "<input type='hidden' name='agr_qtde_maxima_" . $this->getAttribute('name') . "' id='agr_qtde_maxima_" . $this->getAttribute('id') . "' value='" . (int) $dd_agrupador['complementos']['qtde_maxima'] . "' />";

        if ($this->ide_permite_inclusao == "S") {
            echo "<script type='text/javascript'>MsfwAgrupadorMostraCampos('" . $id_form_agrupador . "')</script>";
            //echo "<div style='text-align:center'><a href='javascript:void(0);'  id='".$id_form_agrupador."_mostra_campos' style='cursor:pointer' class=\"btn btn-mini btn-success\" onclick=\"MsfwAgrupadorMostraCampos('" . $id_form_agrupador . "',this)\"><i class=\"icon-plus-sign icon-white\"></i> Novo Registro</a></div>";
        }

        // Inicio da tabela dos dados do agrupador.
        $table = "<table class='table table-striped table-hover' id='" . $id_form_agrupador . "-tabelahtml' width='100%'>";

        /**
         * Cabeçalho da tabela com base nos campos.
         */
        $table .= "<thead>";
        $table .= "<tr>";
        foreach ($titles as $label) {
            $table .= "<th>";
            $table .= $label;
            $table .= "</th>";
        }
        $table .= "<th width='10%' style='text-align:center;'>Ações</th>";
        $table .= "</tr>";
        $table .= "</thead>";
        $table .= "<tbody>";

        /**
         * $this->dd -> Contém a configuração setada via array de configurações, mas a diferença é que o mesmo contém os objetos dos campos, ou seja, poderemos trabalhar a nível de objeto.
         * Já em outras situações poderemos trabalhar com o config.php de cada área, pois lá estão também todas as configurações de cada campo.
         */

        if (count($itens)) {
            foreach ($itens as $Item) {
                $table .= "<tr id='" . $id_form_agrupador . "_" . $Item['id'] . "'>";
                echo "<input type='hidden' name='" . $id_form_agrupador . "_agrupador_id_linha[]' id='" . $id_form_agrupador . "_agrupador_id_linha_" . $Item['id'] . "' value='" . $Item['id'] . "' />";

                foreach ($camposBanco as $campo) {
                    // [0] => Hidden [1] => Textbox (Quando campo Select somente leitura)
                    if (is_array($this->dd[$campo])) {
                        $this->dd[$campo] = $this->dd[$campo][1];
                    }

                    /**
                     * Valor Default em campos
                     */
                    $this->dd[$campo]->setAttribute("rel", $this->dd[$campo]->getAttribute("value"));
                    if ($Item[$campo]) {
                        $this->dd[$campo]->setAttribute("value", $Item[$campo]);
                    }

                    /**
                     * Separando os campos Hidden para renderiza-los antes da tabela, sempre com referencia ao ID no banco para podermos atualizar os valores.
                     **/
                    if ($this->dd[$campo] instanceof \PFBC\Element\Hidden) {
                        $campos_ocultos[] = $this->dd[$campo];
                        $itens_ocultos[] = $Item;
                        continue;
                    }

                    $valor_original_campo = $Item[$campo];
                    // Campos Select, Radio e Checkbox
                    if ($this->dd[$campo] instanceof \PFBC\OptionElement) {
                        $Item[$campo] = $this->dd[$campo]->getOptionText($Item[$campo]);
                    }

                    // Campo Upload
                    if ($this->dd[$campo] instanceof \PFBC\Element\Upload && $Item[$campo]) {
                        $this->dd[$campo]->setAttribute('value', $Item[$campo]);
                        $this->dd[$campo]->renderUploadName();

                        $Appupload = $em->getRepository("Application\Entity\Appupload")->find($Item[$campo]);
                        // Tratamento para imagens exibir um thumb da mesma
                        if ($Appupload) {
                            /*if (in_array(strtolower($Appupload->getExt()), array("jpg", "png", "gif", "jpeg"))) {
                                try {
                                    $img = \Application\Controller\Plugin\CommonImagem::thumb($Appupload, 80,50);
                                    $imgGrande = \Application\Controller\Plugin\CommonImagem::thumb($Appupload, 0,0,"FFFFFF",100);
                                    $nome_arquivo = "<img src=\"" . $img . "\" />";
                                } catch (Exception $e) {
                                    $nome_arquivo = $this->dd[$campo]->getNomeArquivo();
                                }
                                $Item[$campo] = "<a href='".$imgGrande ."' target='new' \">" . $nome_arquivo . "</a>";
                            } else {
                             */
                            $nome_arquivo = $this->dd[$campo]->getNomeArquivo();
                            $Item[$campo] = "<a href='javascript:void(0)' title=\"Clique para o download do arquivo\" data-toggle=\"tooltip\" onclick=\"MsfwDownload('','" . $valor_original_campo . "', '" . $Appupload->getNome() . "')\">" . $nome_arquivo . "</a>";
                            /*}*/
                        }
                    }

                    // formatando numeros monetários.
                    if ($this->dd[$campo] instanceof \PFBC\Element\Monetario) {
                        $valor_original_campo = $Item[$campo] = $this->Util->moneyFormat($valor_original_campo);
                    }

                    // formatando numeros monetários.
                    if ($this->dd[$campo] instanceof \PFBC\Element\Date && $valor_original_campo) {
                        $Date = new \DateTime($valor_original_campo);
                        $valor_original_campo = $Item[$campo] = $this->Util->getDataFormatada($Date->getTimeStamp(), "d/m/Y");
                    }

                    $table .= "<td valign='middle'>" .
                              ((!empty($Item[$campo]) || $Item[$campo] == "0") ? "<span>" . $Item[$campo] . "</span>" : '<span>--</span>') .
                              "<input type='hidden' name='" . $campo . "[]' id='" . $campo . "_" . $Item['id'] . "' value='" . $valor_original_campo . "' />";
                    // Para campos Upload
                    if ($nome_arquivo) {
                        $table .= "<input type='hidden' name='" . $campo . "_texto[]' id='" . $campo . "_texto_" . $Item['id'] . "' value='" . $nome_arquivo . "' />";
                    }
                    $table .= "</td>";
                }

                $table .= "<td class='delete' style='text-align:center;'>";

                if ($this->ide_permite_alteracao == "S") {
                    $table .= "<a href='#ancora-" . $id_form_agrupador . "' onclick=\"MsfwAgrupadorAlterarLinha('" . $id_form_agrupador . "','" . $Item['id'] . "');\" class='btn btn-xs btn-default btn-embossed'><span class='glyphicon glyphicon-pencil'></span></a>&nbsp;";
                }

                if ($this->ide_permite_exclusao == "S") {
                    //$table .= "<a href='javascript:void(0);'onclick=\"MsfwAgrupadorDeleta('" . $id_form_agrupador . "','" . $Item['id'] . "','".$this->ide_permite_exclusao."');\" class='btn btn-danger btn-mini'><i class='icon-white icon-trash excluir-linha-".$id_form_agrupador."'></i></a>";
                    $table .= "<a href='javascript:void(0);' class='btn  btn-danger btn-xs btn-embossed'><span class='glyphicon glyphicon-trash icon-white excluir-linha-" . $id_form_agrupador . "' rel='" . $Item['id'] . "'></span></a>";
                }

                $table .= "</td>";

                $table .= "</tr>";
            }
        } else {
            $table .= "<tr class='sem_registro'>";
            $table .= "<td colspan='" . (count($titles) + 1) . "' style='text-align:center;'>Nenhum registro.</td>"; // Soma-se + 1 ao $titles por causa da coluna Ações da tabela do agrupador.
            $table .= "</tr>";
        }

        $table .= "</tbody>";

        $table .= "</table>";

        /**
         * Renderizando campos ocultos.
         */
        if (count($itens_ocultos)) {
            $i = 0;
            foreach ($itens_ocultos as $item_oculto) {
                echo "<input type='hidden' name='" . $campos_ocultos[$i]->getAttribute('name') . "[]' id='" . $campos_ocultos[$i]->getAttribute('name') . "_" . $item_oculto['id'] . "' value='" . $item_oculto[$campos_ocultos[$i]->getAttribute('name')] . "' />";
                $i++;
            }
        }
        echo $table;
        echo "</div>"; // End content-agrupador
        echo "</div>";
        echo "</div>";
    }
}
