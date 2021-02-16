<?php

/**
 * Classe que realizará as requisições no webservice da PMF.
 *
 * @author Carlos Domingues <carlos.domingues@grupoapi.com.br>
 */
class NfpseDAO
{
    /**
     * Conterá a URL do webservice da PMF
     * @var string
     */
    private $_urlWebservice;

    /**
     * Define o ambiente que esta acessando: homologacao ou producao
     * @var string
     */
    private $_ambiente;

    /**
     * Define o usuário (CMC) para autenticação
     * @var string
     */
    private $_userName;

    /**
     * Define a senha para autenticação
     * @var string
     */
    private $_password;

    /**
     * Define o client_id para autenticação
     * @var string
     */
    private $_clientId;

    /**
     * Define o client_secret para autenticação
     * @var string
     */
    private $_clientSecret;

    /**
     * Conterá o AEDF conforme ambiente que está acessando o webservice: homologação ou produção
     * @var string
     */
    private $_aedf;

    /**
     * Conterá o idCNAE conforme definido por empresa no INI de configurações
     * @var string
     */
    private $_idCNAE;

    /**
     * Define o token retornado na autenticação, que será usado em todas as requisições
     * @var string
     */
    private $_tokenRequest;

    /**
     * Retorna a URL do webservice da PMF
     * @return string
     */
    public function getUrlWebservice()
    {
        return $this->_urlWebservice;
    }

    /**
     * Retorna o ambiente que está acessando
     * @return string
     */
    public function getAmbiente()
    {
        return $this->_ambiente;
    }

    /**
     * Retorna o usuário (CMC) para autenticação
     * @return string
     */
    public function getUserName()
    {
        return $this->_userName;
    }

    /**
     * Retorna a senha para autenticação
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Retorna o client_id para autenticação
     * @return string
     */
    public function getClientId()
    {
        return $this->_clientId;
    }

    /**
     * Retorna o client_id para autenticação
     * @return string
     */
    public function getClientSecret()
    {
        return $this->_clientSecret;
    }

    /**
     * Retorna o AEDF conforme ambiente que está acessando o webservice: homologação ou produção
     * @return string
     */
    public function getAEDF()
    {
        return $this->_aedf;
    }

    /**
     * Retorna o idCNAE conforme definido por empresa no INI de configurações
     * @return string
     */
    public function getIdCNAE()
    {
        return $this->_idCNAE;
    }

    /**
     * Retorna o token retornado na autenticação, que será usado em todas as requisições
     * @return string
     */
    public function getTokenRequest()
    {
        return $this->_tokenRequest;
    }

    /**
     * Construtor da classe, que consumirá os métodos do webservice da PMF
     * @param string $empresaAcesso
     */
    public function __construct($empresaAcesso)
    {
        
        // Define a URL para acesso conforme ambiente que esta usando o microservico
        $dominio              = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
        $ambienteAcessar      = ((strpos($dominio, '192.168') !== false || strpos($dominio, 'localhost') !== false || strpos($dominio, 'dev.') !== false || strpos($dominio, 'sistemas.') !== false)) ? 'homologacao'
                : 'producao';
        $ambienteAcessar = "producao";
        $this->_urlWebservice = MainGama::getApp()->getConfig('url'.ucfirst($ambienteAcessar));

        // Define o ambiente
        $this->_ambiente = $ambienteAcessar;

        // Define os dados de autenticação OAuth conforme a empresa que está acessando
        $this->_userName     = MainGama::getApp()->getConfig("username-{$empresaAcesso}");
        $this->_password     = MainGama::getApp()->getConfig("password-{$empresaAcesso}");
        $this->_clientId     = MainGama::getApp()->getConfig("client_id-{$empresaAcesso}");
        $this->_clientSecret = MainGama::getApp()->getConfig("client_secret-{$empresaAcesso}");

        // Define o AEDF - Se homolgação, SEIS primeiros dígitos do CMC, caso contrário o que está definido no INI de configs
        $this->_aedf = ($ambienteAcessar == 'homologacao') ? substr($this->_userName, 0, 6) : MainGama::getApp()->getConfig("aedf-{$empresaAcesso}");

        // Define o idCNAE - Que está definido no INI de configs
        $this->_idCNAE = MainGama::getApp()->getConfig("idCNAE-{$empresaAcesso}");

        // Por fim, realiza a autenticação no webservice para obter o token, que dura umas 12.5 horas
        $this->_tokenRequest = $this->_getTokenOAuth();
    }

    /**
     * Método que realiza a autenticação no webservice da PMF e retorna o token para as demais requisições
     * @return array
     */
    private function _getTokenOAuth()
    {
        try {
            // Dados que serão enviados por parâmetros para realizar a autenticação
            $dadosAutenticar = array(
                'grant_type' => 'password',
                'username'   => $this->_userName,
                'password'   => strtoupper(md5($this->_password)),
                'client_id'  => $this->_clientId,
                'secret_id'  => $this->_clientSecret
            );

            // Base64 do client_id:client_secret para o HEADER
            $headerAuth = base64_encode("{$this->_clientId}:{$this->_clientSecret}");

            $cURL = curl_init();
//            echo "{$this->_urlWebservice}autenticacao/oauth/token";exit;
            curl_setopt($cURL, CURLOPT_URL, "{$this->_urlWebservice}autenticacao/oauth/token");
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURL, CURLOPT_ENCODING, "");
            curl_setopt($cURL, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($dadosAutenticar));
            curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
                "Authorization: Basic {$headerAuth}",
                "Content-Type: application/x-www-form-urlencoded"
            ));
            // Em produção: comentar estas duas linhas para habilitar a verificação de certificados no servidor
            if ($this->_ambiente == 'homologacao') {
                curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
            }
            $resultado      = json_decode(curl_exec($cURL));
            $infoRequisicao = curl_getinfo($cURL);
            curl_close($cURL);

            if ($infoRequisicao['http_code'] != 200) {
                return array('sucesso' => false);
            }

            return array('sucesso' => true, 'conteudo' => $resultado->access_token);
        } catch (Exception $e) {
            return array('sucesso' => false);
        }
    }

    /**
     * Gera a NFPS-e com base no XML devidamente assinado
     * Se OK, retorna um base64 com o XML de retorno da nota gerada
     * @param string $strXMLAssinada
     * @return array
     */
    public function gerarNota($strXMLAssinada)
    {
        try {
            $req            = $this->_execRequisicaoCURL('GE', $strXMLAssinada);
            $infoRequisicao = $req['infoRequisicao'];
            $resultado      = $req['resultado'];
            $msgErroCurl    = $req['msgErroCurl'];

            if ($infoRequisicao['http_code'] != 200) {
                // Valida se o retorno é um XML. Se for, é sinal que houve algum erro nos dados do XML para gerar a nota
                if (strpos($resultado, '<?xml') !== false) {
                    $aux          = (array) simplexml_load_string(str_replace("\n", "", $resultado));
                    $codigoErro   = $infoRequisicao['http_code'];
                    $mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : $aux['message'];
                }
                // Caso contrário, é um erro de execução do webservice, nesse caso o retorno é um JSON
                else {
                    $aux          = json_decode($resultado);
                    $codigoErro   = 500;
                    $mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : (isset($aux->status) ? $aux->error : $aux->error.' - '.$aux->error_description);
                }

                return array('sucesso' => false, 'mensagemErro' => $mensagemErro, 'httpCode' => $codigoErro);
            }

            return array('sucesso' => true, 'conteudoXML' => base64_encode($resultado));
        } catch (Exception $e) {
            return array('sucesso' => false, 'mensagemErro' => $e->getMessage(), 'httpCode' => 400);
        }
    }

    /**
     * Consulta uma nota fiscal e retornar o ID único da mesma
     * @param integer $numeroSerie Contem o número de série da nota gerada
     * @return array
     */
    public function consultarIdNota($numeroSerie)
    {
        try {
            $req            = $this->_execRequisicaoCURL('ID', null, $numeroSerie);
            $infoRequisicao = $req['infoRequisicao'];
            $resultado      = $req['resultado'];
            $msgErroCurl    = $req['msgErroCurl'];

            if ($infoRequisicao['http_code'] != 200) {
                $aux          = json_decode($resultado);
                $mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : (isset($aux->status) ? $aux->error : $aux->error.' - '.$aux->error_description);

                return array('sucesso' => false, 'mensagemErro' => $mensagemErro, 'httpCode' => 500);
            }

            $resultadoAux = json_decode($resultado);

            return array('sucesso' => true, 'idNota' => $resultadoAux->notas[0]->id);
        } catch (Exception $e) {
            return array('sucesso' => false, 'mensagemErro' => $e->getMessage(), 'httpCode' => 400);
        }
    }

    /**
     * Gera e retorna um array com o base64 do PDF da nota fiscal
     *
     * @todo    IMPOTANTE: Estamos autorizados pela PMF para usar este recurso de gerar o PDF no webservice,
     *          pois é um recurso que segundo eles não era para ser usado pelos emitentes de notas.
     *          Assim que tiver tempo após liberar este microserviço, precisarei desenvolver do zero a
     *          geração do PDF, de acordo com o layout atual, inclusive com o QR code com a URL de verificação da nota.
     *
     * @param integer $idNota Contem o ID da nota gerada
     *
     * @return array
     */
    public function gerarPDFNota($idNota)
    {
        try {
            $req            = $this->_execRequisicaoCURL('PD', null, $idNota);
            $infoRequisicao = $req['infoRequisicao'];
            $resultado      = $req['resultado'];
            $msgErroCurl    = $req['msgErroCurl'];

            if ($infoRequisicao['http_code'] != 200) {
                $aux          = json_decode($resultado);
                $mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : (isset($aux->status) ? $aux->error : $aux->error.' - '.$aux->error_description);

                return array('sucesso' => false, 'mensagemErro' => $mensagemErro, 'httpCode' => 500);
            }

            return array('sucesso' => true, 'conteudoPDF' => $resultado);
        } catch (Exception $e) {
            return array('sucesso' => false, 'mensagemErro' => $e->getMessage(), 'httpCode' => 400);
        }
    }

    /**
     * Calncela a NFPS-e com base no XML devidamente assinado
     * Se OK, retorna um base64 com o XML de retorno da nota cancelada
     * @param string $strXMLAssinada
     * @return array
     */
    public function cancelarNota($strXMLAssinada)
    {
        try {
            $req            = $this->_execRequisicaoCURL('CA', $strXMLAssinada);
            $infoRequisicao = $req['infoRequisicao'];
            $resultado      = $req['resultado'];
            $msgErroCurl    = $req['msgErroCurl'];

            if ($infoRequisicao['http_code'] != 200) {
                // Valida se o retorno é um XML. Se for, é sinal que houve algum erro nos dados do XML para gerar a nota
                if (strpos($resultado, '<?xml') !== false) {
                    $aux          = (array) simplexml_load_string(str_replace("\n", "", $resultado));
                    $codigoErro   = $infoRequisicao['http_code'];
                    $mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : $aux['message'];
                }
                // Caso contrário, é um erro de execução do webservice, nesse caso o retorno é um JSON
                else {
                    $aux          = json_decode($resultado);
                    $codigoErro   = 500;
                    $mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : (isset($aux->status) ? $aux->error : $aux->error.' - '.$aux->error_description);
                }

                return array('sucesso' => false, 'mensagemErro' => $mensagemErro, 'httpCode' => $codigoErro);
            }

            return array('sucesso' => true, 'conteudoXML' => base64_encode($resultado));
        } catch (Exception $e) {
            return array('sucesso' => false, 'mensagemErro' => $e->getMessage(), 'httpCode' => 400);
        }
    }

    /**
     * Faz a requisição no webservice da PMF conforme o tipo solicitado:
     *    GE : Gerar uma NFPS-e
     *    ID : Retornar o ID de uma NFPS-e, necessário para gerar o PDF
     *    PD : Gerar o onteúdo binário do PDF, para salvar
     *    CA : Cancelar uma NFPS-e
     *
     * @param string    $tipoRequisicao
     * @param string    $strXMLAssinada
     * @param integer   $numerSerieOuIdNota
     * @return array
     */
    private function _execRequisicaoCURL($tipoRequisicao, $strXMLAssinada = null, $numerSerieOuIdNota = null)
    {
        $cURL      = curl_init();
        $authToken = $this->_tokenRequest['conteudo'];

        switch ($tipoRequisicao) {
            case 'GE':
                $parteUrl = 'processamento/notas/processa';
                $verbo    = 'POST';
                curl_setopt($cURL, CURLOPT_POSTFIELDS, "{$strXMLAssinada}");
                curl_setopt($cURL, CURLOPT_HTTPHEADER, array("Authorization: Bearer {$authToken}", "Content-Type: application/xml"));
                break;
            case 'ID':
                $parteUrl = "consultas/notas/numero/{$numerSerieOuIdNota}";
                $verbo    = 'GET';
                curl_setopt($cURL, CURLOPT_HTTPHEADER, array("Authorization: Bearer {$authToken}"));
                break;
            case 'PD':
                $parteUrl = "pdf/notas/gerar/{$numerSerieOuIdNota}/{$this->_userName}";
                $verbo    = 'GET';
                curl_setopt($cURL, CURLOPT_HTTPHEADER, array("Authorization: Bearer {$authToken}"));
                break;
            case 'CA':
                $parteUrl = 'cancelamento/notas/cancela';
                $verbo    = 'POST';
                curl_setopt($cURL, CURLOPT_POSTFIELDS, "{$strXMLAssinada}");
                curl_setopt($cURL, CURLOPT_HTTPHEADER, array("Authorization: Bearer {$authToken}", "Content-type: application/xml"));
                break;
        }

        curl_setopt_array($cURL, array(
            CURLOPT_URL            => "{$this->_urlWebservice}{$parteUrl}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $verbo
        ));

        // Em produção: comentar estas duas linhas para habilitar a verificação de certificados no servidor
        if ($this->_ambiente == 'homologacao') {
            curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        }

        $resultado      = curl_exec($cURL);
        $infoRequisicao = curl_getinfo($cURL);
        $msgErroCurl    = curl_error($cURL);
        curl_close($cURL);

        //pre($resultado, 'resultado na dao do microservico', false);
        //pred($infoRequisicao, 'info da requisicao na dao do microservico', false);
        //pred('$msgErroCurl', 'msg de erro na dao do microservico', true);

        return array(
            'resultado'      => $resultado,
            'infoRequisicao' => $infoRequisicao,
            'msgErroCurl'    => $msgErroCurl
        );
    }
}