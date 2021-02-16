<?php

/**
 * Classe criada para encapsular os m�todos espec�ficos que ser�o utilizados pelo NfpseService.service.php
 * @author Carlos Domingues <carlos.domingues@grupoapi.net.br>
 */
class NfpseBaseService
{
    /**
     * @var NfpseDAO
     */
    protected $dao;

    /**
     * Tipo de sa�da do webservice (JSON ou XML), default JSON
     * @var string
     */
    protected $tipoSaida;

    /**
     * Conter� os erros do processo de valida��o
     * @var array
     */
    protected $_arrErros;

    /**
     * O objeto de manipula��o do cabe�alho HTTP para a sa�da de dados do webservice
     * @var HttpResponseService
     */
    private $_serviceHttpResponse;

    /**
     * Dados do POST, par�metros passados na URL
     * @var array
     */
    protected $dadosPost;

    /**
     * Contrutor da classe
     * @param array $parms
     */
    public function __construct()
    {
        // Pego os par�metros setados ainda no BaseAction
        $parms = MainGama::getApp()->getParms();
        
        
        // Define o tipo de sa�da, conforme passado por par�metro ou ou default do .ini (JSON)
        if (isset($parms["tipoSaida"]) && in_array($parms["tipoSaida"], MainGama::getApp()->getConfig('WS_SAIDAS_PERMITIDAS'))) {
            $this->_setTipoSaida($parms["tipoSaida"]);
        } else {
            $this->_setTipoSaida(MainGama::getApp()->getConfig('WS_SAIDA_PADRAO'));
        }
        $this->dao = $this->getDao($parms['emp']);

        // Seta o objeto de manipula��o do cabe�alho HTTP
        $this->_serviceHttpResponse = new HttpResponseService();

        // Seta o POST recebido
        $this->_setDadosPost($parms);
    }

    /**
     * Retorna o Dao dinamicamente
     * @param $empresaAcesso
     * @return DefaultDAO
     */
    public function getDao($empresaAcesso = null)
    {
        $daoName = $this->getDaoName(false);
        $daoFile = "./mod/".MainGama::getApp()->getM()."/".MainGama::getApp()->getU()."/dao/".$daoName.".dao.php";
        clearstatcache();
        if (is_file($daoFile)) {
            $daoName = $this->getDaoName(true);
            return new $daoName($empresaAcesso);
        } else {
            return null;
        }
    }

    /**
     * Retorna dinamicamente o nome do Dao
     * @param boolean $sufixo
     * @return String
     */
    private function getDaoName($sufixo = true)
    {
        $dao = substr(get_class($this), 0, -7);

        if ($sufixo === true) {
            return $dao."Dao";
        } else {
            return $dao;
        }
    }

    /**
     * Retorna a inst�ncia do objeto Dao
     * @return DefaultDAO
     */
    public function getDaoObject()
    {
        return $this->dao;
    }

    protected function _getTipoSaida()
    {
        return $this->tipoSaida;
    }

    protected function _setTipoSaida($tipoSaida)
    {
        $this->tipoSaida = $tipoSaida;
    }

    private function _setDadosPost($post)
    {
        $this->dadosPost = $post;
    }

    /**
     * Criado para na action do webservice eu ter acesso a esses dados em qualquer m�todo ou a��o
     *
     * @param chave
     * @return array
     */
    public function getDadosPost($chave = null)
    {
        
        if ($chave != null) {
            return $this->dadosPost[$chave];
        }

        return $this->dadosPost;
    }

    /**
     * Usado na checagem do par�metro de autentica��o
     * @param array $string
     * @return boolean
     */
    private function _isJson($string)
    {
        return is_object(json_decode($string));
    }

    /**
     * Esse m�todo recebe uma string de dados e monta o objeto.
     * @param string $dados
     * @return mixed
     */
    public function preparaEntradaDados($dados)
    {
        if ($this->_getTipoSaida() == "JSON") {
            return $this->_entraJSON($dados);
        } else if ($this->_getTipoSaida() == "XML") {
            return $this->_entraXML($dados);
        }
    }

    /**
     * Recebe uma string JSON e retorna um array com as informa��es
     * @param string $dados
     * @return array
     */
    private function _entraJSON($dados)
    {
        $retorno = array();
        if (is_string($dados)) {
            $dados   = (array) json_decode($dados);
            $JSON    = new JSONView();
            $retorno = $JSON->decodeUTF8Recursivo($dados, true);
        }

        return $retorno;
    }

    /**
     * Recebe uma string XML e retorna um array com as informa��es
     * @param string $dados
     * @return array
     */
    private function _entraXML($dados)
    {
        $retorno = array();
        if (is_string($dados) && strpos(strtolower($dados), "<?xml") !== false) {
            $XML     = new XMLParse($dados);
            $retorno = $XML->string2array();
        }

        return $retorno;
    }

    /**
     * Esse m�todo recebe um objeto ou array de dados e prepara o retorno dos dados conforme o padr�o de sa�da escolhido.
     * @param Array|Object $dados
     */
    public function preparaRetornoDados($dados)
    {
        if ($this->_getTipoSaida() == "JSON") {
            return $this->_retornaJSON($dados);
        } else if ($this->_getTipoSaida() == "XML") {
            return $this->_retornaXML($dados);
        }
    }

    /**
     * Recebe o retorno do webservice e escreve em tela conforme padr�o de sa�da, tamb�m
     * define o cabe�alho HTTP de resposta conforme c�digo de status HTTP passado.
     * 
     * @param mixed $retorno
     * @param integer $statusHttp
     * @param string $mensagemHttp
     */
    public function escreveRetorno($retorno, $statusHttp = 200, $mensagemHttp = null)
    {
        if (ob_get_status(true)) {
            ob_clean();
        }

        if ($this->_getTipoSaida() == "JSON") {
            header('Content-Type: application/json; charset=UTF-8');
        } else if ($this->_getTipoSaida() == "XML") {
            header('Content-Type: application/xml; charset=UTF-8');
        }

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        /*
         * Nova implementa��o:
         * A sa�da do webservice ter� o cabe�alho devidamente formatado conforme c�digo de status passado
         */
        $this->_serviceHttpResponse->montaCabecalhoHttp($statusHttp, $mensagemHttp);

        echo $retorno;
    }

    /**
     * Recebe um array ou objeto de dados e retorna no formato JSON
     * @param type $dados
     */
    function _retornaJSON($dados)
    {
        $JSON = new JSONView($dados);
        return $JSON->getDadosJSON();
    }

    /**
     * Recebe um array ou objeto de dados e retorna no formato JSON
     * @param mixed $dados
     * @param mixed $xml
     * @return string
     */
    protected function _retornaXML($dados, $xml = false)
    {
        if ($xml === false) {
            $conteudoXML = '<?xml version="1.0" encoding="ISO-8859-1"?>';
            $conteudoXML .= '<root>';
        } else {
            $conteudoXML = "";
        }

        foreach ($dados as $key => $value) {

            $key = Encoding::toLatin1(trim($key));
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $conteudoXML .= "<{$key}>";
                    $conteudoXML .= $this->_retornaXML($value, $conteudoXML);
                    $conteudoXML .= "</{$key}>";
                } else {
                    $conteudoXML .= "<item>";
                    $conteudoXML .= $this->_retornaXML($value, $conteudoXML);
                    $conteudoXML .= "</item>";
                }
            } else if (is_object($value)) {
                $valueArray = get_object_vars($value);
                $conteudoXML .= "<item>";
                $conteudoXML .= $this->_retornaXML($valueArray, $conteudoXML);
                $conteudoXML .= "</item>";
            } else {
                $value = Encoding::toLatin1(trim($value));
                if (!is_numeric($key)) {
                    $value = str_replace(chr(160), " ", $value);
                    $conteudoXML .= "<{$key}><![CDATA[{$value}]]></{$key}>";
                } else {
                    $value = str_replace(chr(160), " ", $value);
                    $conteudoXML .= "<item><![CDATA[{$value}]]></item>";
                }
            }
        }

        if ($xml === false) {
            $conteudoXML .= "</root>";
        }

        return $conteudoXML;
    }

    /**
     * Recebe uma exce��o capturada e retorna no formato que quem estiver consumindo os m�todos definir
     * @param Exception $e
     */
    public function reportaErro(Exception $e)
    {
        $ret = $this->preparaRetornoDados(array('erro' => $e->getMessage()));

        /*
         * O "codigo" retornado no array "erros" ser� sempre um HTTP_CODE v�lido
         */
        $this->escreveRetorno($ret, $e->getCode(), $e->getMessage());
    }
}