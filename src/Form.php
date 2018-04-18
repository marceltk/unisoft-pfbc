<?php

namespace PFBC;

use Zend\ServiceManager\ServiceLocatorInterface;

class Form extends Base
{

    protected $_elements = [];
    protected $_prefix = "http";
    protected $_values = [];
    protected $_attributes = [];
    protected $ajax;
    protected $ajaxCallback;
    protected $errorView;
    protected $labelToPlaceholder;
    protected $resourcesPath;
    public $rotina_onsubmit;
    public $rotina_onload;
    protected $__method_return_ajax = "json";
    protected $frontController;

    /* Prevents various automated from being automatically applied.  Current options for this array
      included jQuery, bootstrap and focus. */
    protected $prevent = [];
    public $view;

    // ZF2 ServiceLocatorInterface
    public $serviceLocator;

    public function __construct(ServiceLocatorInterface $serviceLocator = null, $id = "pfbc")
    {
        $this->serviceLocator = $serviceLocator;

        $this->configure([
                             "action" => basename($_SERVER["SCRIPT_NAME"]),
                             "id" => preg_replace("/\W/", "-", $id),
                             "method" => "post",
                         ]);

        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $this->_prefix = "https";
        }

        /* The Standard view class is applied by default and will be used unless a different view is
          specified in the form's configure method */
        if (empty($this->view)) {
            $this->view = new \PFBC\View\Vertical;
        }

        if (empty($this->errorView)) {
            $this->errorView = $this->error = new \PFBC\ErrorView\Standard;
        }
        $this->resourcesPath = __DIR__ . "/../../../module/Application/media/plugins/";
    }

    public function setIdForm($id = "pfbc")
    {
        $this->configure([
                             "id" => preg_replace("/\W/", "-", $id),
                         ]);
    }

    public function __sleep()
    {
        return ["_attributes", "_elements", "errorView"];
    }

    public function addElement(Element $element)
    {
        $element->_setForm($this);

        //If the element doesn't have a specified id, a generic identifier is applied.
        $id = $element->getAttribute("id");
        if (empty($id)) //$element->setAttribute("id", $this->_attributes["id"] . "-element-" . sizeof($this->_elements)); // retirado para o id do elemento ser igual o nome do elemento
        {
            $id = $element->getAttribute("name");
        }
        // fazer validação para [] em campos checkbox
        $element->setAttribute("id", $id);
        $this->_elements[] = $element;

        /* For ease-of-use, the form tag's encytype attribute is automatically set if the File element
          class is added. */
        if ($element instanceof \PFBC\Element\File) {
            $this->_attributes["enctype"] = "multipart/form-data";
        }
    }

    /* Values that have been set through the setValues method, either manually by the developer
      or after validation errors, are applied to elements within this method. */

    protected function applyValues()
    {
        foreach ($this->_elements as $element) {
            $name = $element->getAttribute("name");
            if (isset($this->_values[$name])) {
                $element->setAttribute("value", $this->_values[$name]);
            } elseif (substr($name, -2) == "[]" && isset($this->_values[substr($name, 0, -2)])) {
                $element->setAttribute("value", $this->_values[substr($name, 0, -2)]);
            }
        }
    }

    public static function clearErrors($id = "pfbc")
    {
        if (!empty($_SESSION["pfbc"][$id]["errors"])) {
            unset($_SESSION["pfbc"][$id]["errors"]);
        }
    }

    public static function clearValues($id = "pfbc")
    {
        if (!empty($_SESSION["pfbc"][$id]["values"])) {
            unset($_SESSION["pfbc"][$id]["values"]);
        }
    }

    public function getAjax()
    {
        return $this->ajax;
    }

    public function getElements()
    {
        return $this->_elements;
    }

    public function getErrorView()
    {
        return $this->errorView;
    }

    public function getPrefix()
    {
        return $this->_prefix;
    }

    public function getPrevent()
    {
        return $this->prevent;
    }

    public function getResourcesPath()
    {
        return $this->resourcesPath;
    }

    public function getErrors()
    {
        $errors = [];
        if (session_id() == "") {
            $errors[""] = ["Error: The pfbc project requires an active session to function properly.  Simply add session_start() to your script before any output has been sent to the browser."];
        } else {
            $errors = [];
            $id = $this->_attributes["id"];
            if (!empty($_SESSION["pfbc"][$id]["errors"])) {
                $errors = $_SESSION["pfbc"][$id]["errors"];
            }
        }

        return $errors;
    }

    protected static function getSessionValues($id = "pfbc")
    {
        $values = [];
        if (!empty($_SESSION["pfbc"][$id]["values"])) {
            $values = $_SESSION["pfbc"][$id]["values"];
        }

        return $values;
    }

    // 17/05/2014 - Marcel - $entityManager Recebe o entitymanager para fazer a validação.
    public static function isValid($id = "pfbc", $clearValues = true, $entityManager = null, $ServiceLocator = null)
    {
        $valid = true;
        /* The form's instance is recovered (unserialized) from the session. */
        $form = self::recover($id);
        if (!empty($form)) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $data = $_POST;
            } else {
                $data = $_GET;
            }

            /* Any values/errors stored in the session for this form are cleared. */
            //self::clearValues($id);
            self::clearErrors($id);

            /* Each element's value is saved in the session and checked against any validation rules applied
              to the element. */
            if (!empty($form->_elements)) {
                foreach ($form->_elements as $element) {
                    $name = $element->getAttribute("name");
                    if (substr($name, -2) == "[]") {
                        $name = substr($name, 0, -2);
                    }

                    /* The File element must be handled differently b/c it uses the $_FILES superglobal and
                      not $_GET or $_POST. */
                    if ($element instanceof \PFBC\Element\File) {
                        $data[$name] = $_FILES[$name]["name"];
                    }

                    if (isset($data[$name])) {
                        $value = $data[$name];
                        if (is_array($value)) {
                            $valueSize = sizeof($value);
                            for ($v = 0; $v < $valueSize; ++$v) {
                                $value[$v] = stripslashes($value[$v]);
                            }
                        } else {
                            $value = stripslashes($value);
                        }
                        self::_setSessionValue($id, $name, $value);
                    } else {
                        $value = null;
                    }

                    /* If a validation error is found, the error message is saved in the session along with
                      the element's name. */
                    if (!$element->isValid($value, $entityManager, $ServiceLocator)) {
                        self::setError($id, $element->getErrors(), $name);
                        $valid = false;
                    }
                }
            }

            /* If no validation errors were found, the form's session values are cleared. */
            if ($valid) {
                if ($clearValues) {
                    self::clearValues($id);
                }
                self::clearErrors($id);
            }
        } else {
            $valid = false;
        }

        return $valid;
    }

    /* This method restores the serialized form instance. */

    protected static function recover($id)
    {
        if (!empty($_SESSION["pfbc"][$id]["form"])) {
            return unserialize($_SESSION["pfbc"][$id]["form"]);
        } else {
            return "";
        }
    }

    public function render($returnHTML = false)
    {
        if (!empty($this->labelToPlaceholder)) {
            foreach ($this->_elements as $element) {
                $label = $element->getLabel();
                if (!empty($label)) {
                    //$element->setAttribute("placeholder", $label);
                    $element->setLabel("");
                }
            }
        }

        $this->view->_setForm($this);
        $this->errorView->_setForm($this);

        /* When validation errors occur, the form's submitted values are saved in a session
          array, which allows them to be pre-populated when the user is redirected to the form. */
        $values = self::getSessionValues($this->_attributes["id"]);
        if (!empty($values)) {
            $this->setValues($values);
        }
        $this->applyValues();

        if ($returnHTML) {
            ob_start();
        }

        $this->renderCSS();
        $this->view->render();
        $this->renderJS();

        /* The form's instance is serialized and saved in a session variable for use during validation. */
        $this->save();

        if ($returnHTML) {
            $html = ob_get_contents();
            ob_end_clean();

            return $html;
        }
    }

    /* When ajax is used to submit the form's data, validation errors need to be manually sent back to the
      form using json. */

    public static function renderAjaxErrorResponse($id = "pfbc")
    {
        $form = self::recover($id);
        if (!empty($form)) {
            $form->errorView->renderAjaxErrorResponse();
        }

        \PFBC\Form::clearErrors($id);
        \PFBC\Form::clearValues($id);
    }

    protected function renderCSS()
    {
        $this->renderCSSFiles();

        echo '<style type="text/css">';
        $this->view->renderCSS();
        $this->errorView->renderCSS();
        foreach ($this->_elements as $element) {
            $element->renderCSS();
        }
        echo '</style>';
    }

    protected function renderCSSFiles()
    {
        $urls = [];
        if (!in_array("bootstrap", $this->prevent)) //$urls[] = $this->_prefix . "://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css";
        {
            foreach ($this->_elements as $element) {
                $elementUrls = $element->getCSSFiles();
                if (is_array($elementUrls)) {
                    $urls = array_merge($urls, $elementUrls);
                }
            }
        }

        /* This section prevents duplicate css files from being loaded. */
        if (!empty($urls)) {
            $urls = array_values(array_unique($urls));
            foreach ($urls as $url) {
                echo '<link type="text/css" rel="stylesheet" href="', $url, '"/>';
            }
        }
    }

    // seta onde irá ser renderizado os resultados das pesquisas da lupa
    public function setDivPesquisaLupa($id_div = false)
    {
        $this->__id_div_retorno_lupa;
    }

    private function getDivPesquisaLupa()
    {
        return $this->__id_div_retorno_lupa;
    }

    // seta o método de retorno
    public function setMethodReturnAjax($method = "json")
    {
        $this->__method_return_ajax = $method;
    }

    // recupera o método de retorno dos dados quando requisição ajax.
    private function getMethodReturnAjax()
    {
        return $this->__method_return_ajax;
    }

    protected function renderJS()
    {
        $elements = $this->getElements();

        foreach ($elements as $element) {
            if ($element->getAttribute("name") == "div-conteudo-retorno") {
                $this->_div_conteudo_retorno = $element->getAttribute('value');
            }
            if ($element->getAttribute("name") == "amb_password") {
                $this->amb_password = true;
            }
        }

        $this->renderJSFiles();

        echo '<script type="text/javascript">';
        $this->view->renderJS();
        foreach ($this->_elements as $element) {
            $element->renderJS();
        }

        $id = $this->_attributes["id"];

        echo 'jQuery(document).ready(function() {';

        /* jQuery is used to set the focus of the form's initial element. */
        if (!in_array("focus", $this->prevent)) {
            echo 'jQuery("#', $id, ' :input:visible:enabled:first").focus();';
        }

        $this->view->jQueryDocumentReady();
        foreach ($this->_elements as $element) {
            $element->jQueryDocumentReady();
        }

        /* For ajax, an anonymous onsubmit javascript function is bound to the form using jQuery.  jQuery's
          serialize function is used to grab each element's name/value pair. */
        if (!empty($this->ajax)) {
            // echo 'jQuery("#', $id, '").bind("submit", function() {';
            $this->error->clear();
            echo <<<JS
                var options = {
                //target:        '#output1',   // target element(s) to be updated with server response
                beforeSubmit:  showRequest,  // pre-submit callback
                success:       showResponse,  // post-submit callback
                error:       showError,  // post-submit callback
                type:      "{$this->_attributes["method"]}",        // 'get' or 'post', override for form's 'method' attribute
                dataType:  "{$this->getMethodReturnAjax()}",
                url: "{$this->_attributes["action"]}"
                };

JS;

            if (!$this->rotina_onsubmit) {
                echo <<<JS
                jQuery('#$id').submit(function(){

                    // Segurança Adicional - Marcel Silva - 29/03/2016
                    if(!$("#amb_password").val() && $("#amb_password").length){
                        Mw2SeguracaAdicional('$id','amb_password');
                        return false;
                    }

                    var dialogConfirm = new BootstrapDialog({
                        title: '<span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;Confirmação',
                        message: 'Confirma a gravação dos dados?',
                        size: BootstrapDialog.SIZE_SMALL,
                        onshown : function (dialog){
                            dialog.getButton('btn-confirm-sim').focus();
                        },
                        buttons: [{
                                label: 'Sim',
                                id : 'btn-confirm-sim',
                                cssClass: 'btn-primary btn-sm btn-embossed',
                                action: function (dialogRef) {
                                    jQuery('#$id').ajaxSubmit(options);
                                    dialogRef.close();
                                }
                            },
                            {
                                label: 'Cancelar',
                                cssClass: 'btn-default btn-sm btn-embossed',
                                action: function (dialogRef) {
                                    dialogRef.close();
                                }
                            }]
                    });
                    dialogConfirm.open();
                    if(dialogConfirm.isOpened()) {
                        $(dialogConfirm).prev('.modal-backdrop').css('z-index','1050');
                    }
                    return false;
                });

JS;
            } else {
                echo <<<JS

                jQuery('#$id').submit(function(e){

                    // Segurança Adicional - Marcel Silva - 29/03/2016
                    if(!$("#amb_password").val() && $("#amb_password").length){
                        Mw2SeguracaAdicional('$id','amb_password');
                        return false;
                    }

                    showLoading();
                    $.ajax({
                            type: "post",
                            url: '$this->rotina_onsubmit',
                            dataType: "html",
                            data : $(this).serialize(),
                            context: document.body,
                            success: function(data) {
                            $("#msfw-rotina").html(data);
                            hideLoading();
                            if(bSubmit) {
                                var dialogConfirm = new BootstrapDialog({
                                    title: '<span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;Confirmação',
                                    message: 'Confirma a gravação dos dados?',
                                    size: BootstrapDialog.SIZE_SMALL,
                                    onshown : function (dialog){
                                        dialog.getButton('btn-confirm-sim').focus();
                                    },
                                    buttons: [{
                                            label: 'Sim',
                                            id : 'btn-confirm-sim',
                                            cssClass: 'btn-primary btn-sm btn-embossed',
                                            action: function (dialogRef) {
                                                jQuery('#$id').ajaxSubmit(options);
                                                dialogRef.close();
                                            }
                                        },
                                        {
                                            label: 'Cancelar',
                                            cssClass: 'btn-default btn-sm btn-embossed',
                                            action: function (dialogRef) {
                                                dialogRef.close();
                                            }
                                        }]
                                });
                                dialogConfirm.open();
                            }
                        },
                        complete: function(data) {},
                        error: function(data) {}
                    });
                    return false;
                });

JS;
            }
        }
        echo <<<JS
    });
JS;
        if (!empty($this->ajax)) {
            echo <<<JS
                function showError(data) {
                    MsfwDialogAlerta('Houve um erro ao efetuar a requisição!', data.responseText);
                }

  function showResponse(response, statusText, xhr, \$form) {
        hideLoading();
JS;
            echo "var h = null;";
            echo "var w = null;";
            echo "var ok,lis,man,erro,btCustom;";
            echo "var t = new Array();";

            echo "if(response != undefined && typeof response == 'object' && !response.errors) {";
            //echo "jQuery('#dialog').html(response.msg);";

            // para quando for uma ação de login ou direcionamento
            echo "if(response.redirect) { window.location.href = response.redirect; return true;} \n";

            // executando uma rotina posterior ao envio do formulário.
            echo "if(response.rotina_pos) {";
            echo "eval(response.rotina_pos);";
            echo "}\n";

            echo "if(response.retorno_personalizado) {";
            echo "
            var rotina_custom;\n
            var url_custom;\n
            var nome_botao_custom;\n
			$(response.retorno_personalizado).each(function(i,item){\n
					$(item).each(function(i2,item2){\n
					nome_botao_custom = item2.nome_botao;
					url_custom = item2.url;
                    if(item2.rotina){
					   rotina_custom1 = item2.rotina;
                    }

					  btCustom = {
							id: 'button-'+nome_botao_custom,
							label: nome_botao_custom,
                            cssClass: 'btn-default btn-sm btn-embossed',
							action: function(dialogRef) {
								if(item2.url) {
								    loader(item2.url,'" . $this->_div_conteudo_retorno . "');
                                }
                                dialogRef.close();

                                if(rotina_custom1) {
                                   	eval(rotina_custom1);
                                }
							}
						};
						  t.push(btCustom);
						});
				});\n";

            echo "}\n";

            // Retorno erro
            echo "if(response.erro){";
            echo "var h = 500;";
            echo "var w = 750;";
            echo "erro = {
                        id: 'button-erro',
                        text: 'Fechar',
                        click: function() {
                            \$('#dialog').dialog('close');
                        }";
            echo "};";
            echo "t.push(erro);";
            echo "}";

            // Retorno Ok // #Bootstrap Modal
            echo "if(response.retorno_ok){";
            echo "ok = {";
            echo "
                id: 'button-ok',
                label: 'Ok',
                cssClass: 'btn-primary btn-sm btn-embossed',
                action: function(dialogRef) {
                    if(response.rotina_pos_retorno_ok) {
                        eval(response.rotina_pos_retorno_ok);
                    } else {\n
                        loader(response.retorno_ok,'" . $this->_div_conteudo_retorno . "');
                    }\n
                    dialogRef.close();

                    if(rotina_custom) {
                        eval(rotina_custom);
                    }
                }
            ";
            echo "};";
            echo "t.push(ok);";
            echo "}";

            // Listagem // #Bootstrap
            echo "if(response.retorno_lis){";
            echo "lis = {
                        id: 'button-lis',
                        label: 'Exibir Todos',
                        cssClass: 'btn-default btn-sm btn-embossed',
                        action: function(dialogRef) {
                            loader(response.retorno_lis,'" . $this->_div_conteudo_retorno . "');
                            dialogRef.close();
                        }";
            echo "};";
            echo "t.push(lis);";
            echo "}";

            // Manutenção // #Bootstrap
            echo "if(response.retorno_man){";
            echo "man = {
                        id: 'button-man',
                        cssClass: 'btn-default btn-sm btn-embossed',
                        label: 'Novo Registro',
                        action: function(dialogRef) {
                            loader(response.retorno_man,'" . $this->_div_conteudo_retorno . "');
                            dialogRef.close();
                        }";
            echo "};";
            echo "t.push(man);";
            echo "}";

            echo "if(t.length <= 0){";
            echo "ok1 = {
                        id: 'button-fechar',
                        label: 'Fechar',
                        cssClass: 'btn-default btn-sm btn-embossed',
                        action: function(dialogRef) {
                            dialogRef.close();
                        }";
            echo "};";
            echo "t.push(ok1);";
            echo "}";

            echo <<<JS
                BootstrapDialog.closeAll(); // Fecha todos os Modais
                var dialogConfirmRetorno = new BootstrapDialog({
                    title: '<span class="glyphicon glyphicon-info-sign"></span>&nbsp;Mensagem',
                    message: response.msg,
                    closable : false,
                    onshown : function (dialog){
                        var btnOk = dialog.getButton('button-ok');
                        if(btnOk){
                            btnOk.focus();
                        }
                    },
                    size: BootstrapDialog.SIZE_NORMAL,
                    buttons: t
                });
                $("#button-ok").focus();
                dialogConfirmRetorno.open();
JS;

            echo "}";

            echo <<<JS
if(response != undefined && typeof response == "object" && response.errors) {
JS;
            $this->error->clear();
            $this->error->applyAjaxErrorResponse();
            echo <<<JS
        jQuery("html, body").animate({ scrollTop: jQuery("#$id").offset().top - 60 }, 500 );
        } else {
JS;
            $this->error->clear();
            if (!empty($this->ajaxCallback)) {
                echo $this->ajaxCallback, "(response);";
            }

            echo '}';

            echo <<<JS

  }

  function showRequest(){
        showLoading();
  }
JS;
        } // end if ajax

        echo <<<JS
</script>
JS;
    }

    protected function renderJSFiles()
    {
        $urls = [];
        if (!in_array("jQuery", $this->prevent)) //$urls[] = $this->_prefix . "://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js";
        {
            if (!in_array("bootstrap", $this->prevent)) //$urls[] = $this->_prefix . "://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/js/bootstrap.min.js";
            {
                echo "";
            }
        }
        foreach ($this->_elements as $element) {
            $elementUrls = $element->getJSFiles();
            if (is_array($elementUrls)) {
                $urls = array_merge($urls, $elementUrls);
            }
        }

        /* This section prevents duplicate js files from being loaded. */
        if (!empty($urls)) {
            $urls = array_values(array_unique($urls));
            foreach ($urls as $url) {
                echo '<script type="text/javascript" src="', $url, '"></script>';
            }
        }
    }

    /* The save method serialized the form's instance and saves it in the session. */

    protected function save()
    {
        $_SESSION["pfbc"][$this->_attributes["id"]]["form"] = serialize($this);
    }

    /* Valldation errors are saved in the session after the form submission, and will be displayed to the user
      when redirected back to the form. */

    public static function setError($id, $errors, $element = "")
    {
        if (!is_array($errors)) {
            $errors = [$errors];
        }
        if (empty($_SESSION["pfbc"][$id]["errors"][$element])) {
            $_SESSION["pfbc"][$id]["errors"][$element] = [];
        }

        foreach ($errors as $error) {
            $_SESSION["pfbc"][$id]["errors"][$element][] = $error;
        }
    }

    public static function _setSessionValue($id, $element, $value)
    {
        $_SESSION["pfbc"][$id]["values"][$element] = $value;
    }

    /* An associative array is used to pre-populate form elements.  The keys of this array correspond with
      the element names. */

    public function setValues(array $values)
    {
        $this->_values = array_merge($this->_values, $values);
    }

    /**
     * 25/11/2012
     * Marcel - Adicionada a função para retornar a resposta.
     */
    public static function renderAjaxSuccessResponse($array = null)
    {
        $dados = [
            "msg" => "As informações foram gravadas com sucesso!",
            "retorno_ok" => '',
            "retorno_lis" => '',
            "retorno_man" => '',
            "rotina_pos" => '',
            "rotina_pre" => '',
            "rotina_pos_retorno_ok" => '',
        ];

        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if ($value) {
                    $dados[$key] = $value;
                }
            }
        }
        echo json_encode($dados);
    }
}
