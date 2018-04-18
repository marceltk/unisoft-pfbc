<?php

namespace PFBC\Element;

class Historico extends \PFBC\Element
{

    protected $_attributes = ["rows" => "5"];

    public function render()
    {
        //echo "<div class='tmp_historico " . $this->getAttribute("class") . "'>";
        echo "<div class='tmp_historico input-historico-100'>";
        //$this->appendAttribute("class", "form-control");
        //$this->appendAttribute("class", "form-control-historico");
        $this->setAttribute("class", "form-control");
        $this->appendAttribute("class", "form-control-historico");
        $this->appendAttribute("class", "input-100");

        if (!$this->getAttribute("disabled") && !$this->getAttribute("readonly")) {
            echo "<textarea" . $this->getAttributes("value") . "></textarea>";
        }

        if (!empty($this->_attributes["value"])) {
            echo "<div class='tmp_historico_conteudo'>";
            $valor = str_replace("_BR_", "<br />", $this->_attributes["value"]);
            //echo nl2br($valor);
            //$valor = nl2br($valor);
            $pattern = "/<p>.*?<\/p>/ms";
            preg_match_all($pattern, $valor, $matches);
            ob_start();
            ?>

            <div class="panel panel-default">

                <div class="panel-header panel-controls">
                    <div class="control-btn">
                        <a href="#" class="panel-maximize"><span class="icon-size-fullscreen"></span></a>
                    </div>
                </div>

                <div class="panel-content">
                    <div class="timeline-centered">
                        <?php

                        foreach ($matches[0] as $match) {
                            $descricao = preg_replace("/<strong><em>.*?<\/em><\/strong>.-? /ms", "", $match);
                            $descricao = str_replace(["<p>", "</p>"], "", $descricao);

                            preg_match("/<strong><em>.*?<\/em><\/strong>/ms", $match, $matchDataLogin);
                            $stringDataLogin = preg_replace("/<strong><em>|<\/em><\/strong>/ms", "", $matchDataLogin[0]);

                            list($login, $datetime) = explode("-", $stringDataLogin);
                            list($data, $hora) = explode(" ", trim($datetime));

                            //left-aligned
                            ?>
                            <article class="timeline-entry ">

                                <div class="timeline-entry-inner">
                                    <time class="timeline-time" datetime="<?php echo $datetime ?>">
                                        <span><?php echo $data ?></span>
                                        <span><?php echo $hora ?></span></time>

                                    <div class="timeline-icon bg-primary">
                                        <span class="fa fa-history" aria-hidden="true"></span>
                                    </div>

                                    <div class="timeline-label">
                                        <h2><span class="fa fa-user" aria-hidden="true"></span>
                                            <strong><?php echo $login ?></strong></h2>
                                        <p><?php echo $descricao ?></p>
                                    </div>
                                </div>

                            </article>
                            <?php

                        }
                        ?>

                        <article class="timeline-entry begin">
                            <div class="timeline-entry-inner">
                                <div class="timeline-icon"
                                     style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                                    <span class="fa fa-star" aria-hidden="true"></span>
                                </div>
                            </div>
                        </article>

                    </div>
                </div>
            </div>
            <?php

            $conteudo = ob_get_contents();
            ob_end_clean();

            echo $conteudo;
            echo "</div>";
        } else {
            echo "<div style='text-align:center;background:#eaeaea;padding:5px;'>Nenhum registro no histórico</div>";
        }
        echo "</div>";
    }

    public function getJSFiles()
    {
        return [// "/js/43_bootstrap-maxlength.js"
        ];
    }

    public function jQueryDocumentReady()
    {
        parent::jQueryDocumentReady();
        /*if ($this->getAttribute("maxlength")) {
            echo 'jQuery("div.tmp_historico div p:odd").css("background","#ddd");';
            echo 'jQuery("div.tmp_historico div p strong:odd").addClass("odd");';
            echo 'jQuery("div.tmp_historico div p strong:even").addClass("even");';
        }*/
    }

}
