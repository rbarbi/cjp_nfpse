<?php

//require_once 'NfpseBase.service.php';

/*
 * Namespaces das classes para assinatura digital do XML para requisição da NFPS-e
 */
use NFePHP\Common\Signer;
use NFePHP\Common\Certificate;

/**
 * Classe que conterá métodos específicos utilizados pelos métodos do módulo
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
     * Chama a geração de uma nota fiscal na DAO, consumindo o webservice da PMF
     * Retorna um base64 com o XML completo de retorno da nota gerada
     * 
     * @return array
     * @throws Exception
     */
    public function gerarNota()
    {
        try {
            $dadosPost = $this->getDadosPost();
            // Define o AEDF conforme ambiente que está acessando. Mais informações ver item "numeroAEDF" na geração do XML da requisição
            $aedf = $this->dao->getAEDF();
            
            $this->doLoga("Gerando nota para {$dadosPost['dadosNota']['nome_nota']}");

            // Define o idCNAE - conforme definido por empresa no INI de configs
            $idCNAE = $this->dao->getIdCNAE();

            // Gera o XML com os dados que foram postados na requisição
            $dadosStrXML = UtilsNFPSe::gerarXMLrequisicao($dadosPost['dadosNota'], $aedf, $idCNAE);
            if ($dadosStrXML['sucesso'] == false) {
                throw new Exception($dadosStrXML['mensagemErro'], $dadosStrXML['httpCode']);
            }

            // Abre o certificado da empresa específica
            $nomeCertificado  = MainGama::getApp()->getConfig("nome_certificado-{$dadosPost['emp']}");
            $senhaCertificado = MainGama::getApp()->getConfig("senha_certificado-{$dadosPost['emp']}");
            $pathCertificado  = dirname(dirname(__DIR__)).'/certificado-digital/'.$nomeCertificado;
            $strCertificado   = file_get_contents($pathCertificado);
            $objCertificado   = Certificate::readPfx($strCertificado, $senhaCertificado);
            pred($objCertificado);
            // Chama a assinatura digital do XML com o certificado da empresa que está gerando a nota fiscal
            $strXMLAssinada  = Signer::sign($objCertificado, $dadosStrXML['conteudoXML'], 'xmlProcessamentoNfpse', '', OPENSSL_ALGO_SHA1, [true, false, null, null], '');
            
            // Por fim chama o método que gera a nota, validando se gerou corretamente caso contrário dispara exceção
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
     * Reliza a consulta de uma nota fiscal na DAO, consumindo o webservice da PMF e retorna um array com o ID único da nota
     * 
     * @param integer $numeroSerie Contem o número de série da nota gerada
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
             * Se alguma coisa der errada na geração da nota, retorna o código 400 (Bad request)
             * Os objetos Certificate e Signer disparam exceções, que por sua vez extendem da Exception nativa do PHP
             */
            throw new Exception($e->getMessage(), 400);
        }
    }

    /**
     * Chama a geração do PDF de uma nota fiscal na DAO, consumindo o webservice da PMF
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
             * Se alguma coisa der errada na geração da nota, retorna o código 400 (Bad request)
             * Os objetos Certificate e Signer disparam exceções, que por sua vez extendem da Exception nativa do PHP
             */
            throw new Exception($e->getMessage(), 400);
        }
    }

    /**
     * Chama o método de cancelamento de uma NFPS-e, consumindo o webservice da PMF
     * @return array
     * @throws Exception
     */
    public function cancelarNota()
    {
        try {
            $dadosPost = $this->getDadosPost()['dadosNota'];
            
            // Define o AEDF conforme ambiente que está acessando. Mais informações ver item "numeroAEDF" na geração do XML da requisição
            $aedf = $this->dao->getAEDF();

            // Gera o XML com os dados que foram postados na requisição
            $dadosStrXML = UtilsNFPSe::gerarXMLcancelamento($aedf, $dadosPost['numeroSerie'], $dadosPost['codigoVerificacao'], $dadosPost['motivoCancelamento']);
            if ($dadosStrXML['sucesso'] == false) {
                throw new Exception($dadosStrXML['mensagemErro'], $dadosStrXML['httpCode']);
            }

            // Abre o certificado da empresa específica
            $nomeCertificado  = MainGama::getApp()->getConfig("nome_certificado-{$dadosPost['emp']}");
            $senhaCertificado = MainGama::getApp()->getConfig("senha_certificado-{$dadosPost['emp']}");
            $pathCertificado  = dirname(dirname(__DIR__)).'/certificado-digital/'.$nomeCertificado;
            $strCertificado   = file_get_contents($pathCertificado);
            $objCertificado   = Certificate::readPfx($strCertificado, $senhaCertificado);

            // Chama a assinatura digital do XML com o certificado da empresa que está gerando a nota fiscal
            $strXMLAssinada = Signer::sign($objCertificado, $dadosStrXML['conteudoXML'], 'xmlCancelamentoNfpse', '', OPENSSL_ALGO_SHA1, [true, false, null, null], '');
            
            // Por fim chama o método que gera a nota, validando se gerou corretamente caso contrário dispara exceção
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