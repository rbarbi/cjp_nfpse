<?php

//require_once 'NfpseBase.service.php';

/*
 * Namespaces das classes para assinatura digital do XML para requisi��o da NFPS-e
 */
use NFePHP\Common\Signer;
use NFePHP\Common\Certificate;

/**
 * Classe que conter� m�todos espec�ficos utilizados pelos m�todos do m�dulo
 * @author Carlos Domingues <carlos.domingues@grupoapi.net.br>
 */
class NfpseService extends NfpseBaseService
{

    /**
     * Construtor da classe
     * @param array $parms
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Chama a gera��o de uma nota fiscal na DAO, consumindo o webservice da PMF
     * Retorna um base64 com o XML completo de retorno da nota gerada
     * 
     * @return array
     * @throws Exception
     */
    public function gerarNota()
    {
        try {
            $dadosPost = $this->getDadosPost();
            // Define o AEDF conforme ambiente que est� acessando. Mais informa��es ver item "numeroAEDF" na gera��o do XML da requisi��o
            $aedf = $this->dao->getAEDF();
            
            $this->doLoga("Gerando nota para {$dadosPost['dadosNota']['nome_nota']}");

            // Define o idCNAE - conforme definido por empresa no INI de configs
            $idCNAE = $this->dao->getIdCNAE();

            // Gera o XML com os dados que foram postados na requisi��o
            $dadosStrXML = UtilsNFPSe::gerarXMLrequisicao($dadosPost['dadosNota'], $aedf, $idCNAE);
            if ($dadosStrXML['sucesso'] == false) {
                throw new Exception($dadosStrXML['mensagemErro'], $dadosStrXML['httpCode']);
            }

            // Abre o certificado da empresa espec�fica
            $nomeCertificado  = MainGama::getApp()->getConfig("nome_certificado-{$dadosPost['emp']}");
            $senhaCertificado = MainGama::getApp()->getConfig("senha_certificado-{$dadosPost['emp']}");
            $pathCertificado  = dirname(dirname(__DIR__)).'/certificado-digital/'.$nomeCertificado;
            $strCertificado   = file_get_contents($pathCertificado);
            $objCertificado   = Certificate::readPfx($strCertificado, $senhaCertificado);
            pred($objCertificado);
            // Chama a assinatura digital do XML com o certificado da empresa que est� gerando a nota fiscal
            $strXMLAssinada  = Signer::sign($objCertificado, $dadosStrXML['conteudoXML'], 'xmlProcessamentoNfpse', '', OPENSSL_ALGO_SHA1, [true, false, null, null], '');
            
            // Por fim chama o m�todo que gera a nota, validando se gerou corretamente caso contr�rio dispara exce��o
            $dadosNotaGerada = $this->dao->gerarNota($strXMLAssinada);
            if ($dadosNotaGerada['sucesso'] == false) {
                throw new Exception($dadosNotaGerada['mensagemErro'], $dadosNotaGerada['httpCode']);
            }

            return $dadosNotaGerada;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    
    function doLoga($msg){
        $arq = fopen("./log/logGeracaoNota".date("d_m_Y").".txt",'a');
        fwrite($arq,date("d/m/Y H:i:s")." ==> ". $msg."\r\n");
        fclose($arq);
    }
    /**
     * Reliza a consulta de uma nota fiscal na DAO, consumindo o webservice da PMF e retorna um array com o ID �nico da nota
     * 
     * @param integer $numeroSerie Contem o n�mero de s�rie da nota gerada
     * @return integer
     * @throws Exception
     */
    public function consultarIdNota($numeroSerie)
    {
        try {
            $dadosNota = $this->dao->consultarIdNota($numeroSerie);
            if ($dadosNota['sucesso'] == false) {
                throw new Exception($dadosNota['mensagemErro']);
            }

            return $dadosNota;
        } catch (Exception $e) {
            /*
             * Se alguma coisa der errada na gera��o da nota, retorna o c�digo 400 (Bad request)
             * Os objetos Certificate e Signer disparam exce��es, que por sua vez extendem da Exception nativa do PHP
             */
            throw new Exception($e->getMessage(), 400);
        }
    }

    /**
     * Chama a gera��o do PDF de uma nota fiscal na DAO, consumindo o webservice da PMF
     * Retorna um array com o base64 com o binario do PDF para o sistema da empresa que acessar salvar em arquivo
     *
     * @param integer $idNota Contem o ID da nota gerada
     * @return array
     * @throws Exception
     */
    public function gerarPDFNota($idNota)
    {
        try {
            $dadosPdfGerado = $this->dao->gerarPDFNota($idNota);
            if ($dadosPdfGerado['sucesso'] == false) {
                throw new Exception($dadosPdfGerado['mensagemErro']);
            }

            return $dadosPdfGerado;
        } catch (Exception $e) {
            /*
             * Se alguma coisa der errada na gera��o da nota, retorna o c�digo 400 (Bad request)
             * Os objetos Certificate e Signer disparam exce��es, que por sua vez extendem da Exception nativa do PHP
             */
            throw new Exception($e->getMessage(), 400);
        }
    }

    /**
     * Chama o m�todo de cancelamento de uma NFPS-e, consumindo o webservice da PMF
     * @return array
     * @throws Exception
     */
    public function cancelarNota()
    {
        try {
            $dadosPost = $this->getDadosPost()['dadosNota'];
            
            // Define o AEDF conforme ambiente que est� acessando. Mais informa��es ver item "numeroAEDF" na gera��o do XML da requisi��o
            $aedf = $this->dao->getAEDF();

            // Gera o XML com os dados que foram postados na requisi��o
            $dadosStrXML = UtilsNFPSe::gerarXMLcancelamento($aedf, $dadosPost['numeroSerie'], $dadosPost['codigoVerificacao'], $dadosPost['motivoCancelamento']);
            if ($dadosStrXML['sucesso'] == false) {
                throw new Exception($dadosStrXML['mensagemErro'], $dadosStrXML['httpCode']);
            }

            // Abre o certificado da empresa espec�fica
            $nomeCertificado  = MainGama::getApp()->getConfig("nome_certificado-{$dadosPost['emp']}");
            $senhaCertificado = MainGama::getApp()->getConfig("senha_certificado-{$dadosPost['emp']}");
            $pathCertificado  = dirname(dirname(__DIR__)).'/certificado-digital/'.$nomeCertificado;
            $strCertificado   = file_get_contents($pathCertificado);
            $objCertificado   = Certificate::readPfx($strCertificado, $senhaCertificado);

            // Chama a assinatura digital do XML com o certificado da empresa que est� gerando a nota fiscal
            $strXMLAssinada = Signer::sign($objCertificado, $dadosStrXML['conteudoXML'], 'xmlCancelamentoNfpse', '', OPENSSL_ALGO_SHA1, [true, false, null, null], '');
            
            // Por fim chama o m�todo que gera a nota, validando se gerou corretamente caso contr�rio dispara exce��o
            $dadosNotaCancelada = $this->dao->cancelarNota($strXMLAssinada);
            if ($dadosNotaCancelada['sucesso'] == false) {
                throw new Exception($dadosNotaCancelada['mensagemErro'], $dadosNotaCancelada['httpCode']);
            }

            return $dadosNotaCancelada;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}