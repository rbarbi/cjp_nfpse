<?php

/**
 * Classe que conter� todos os m�todos que poder�o ser consumidos no webservice
 * @author Carlos Domingues <carlos.domingues@grupoapi.net.br>
 */
class NfpseAction extends DefaultAction
{

    public function __construct($app, $GET, $POST)
    {
        // Antes de qualquer a��o, � neces�rio adicionar ao $_POST o decode do JSON que veio na requisi��o do consumidor do microsevi�o
        $dados = (array) json_decode(file_get_contents('php://input'));
        //pre($dados, 'dados na action principal');
        if (isset($dados[0])) {
            $dados = (array) $dados[0];
        }
        //pred($dados, 'dados depois de manipulado (depende do php://input)');

        if (!empty($dados)) {
            $_POST['dadosNota'] = $dados;
            $_POST['emp']       = $dados['emp'];
        }
        //pred($_POST, 'post, depois de adicionar os dados da nota');
        // Chamo construtor da classe pai
        parent::__construct($app, $GET, $POST, "./mod/".MainGama::getApp()->getM());
    }

    /**
     * Index padr�o, caso acessar sem par�metros
     */
    public function indexAction()
    {
        
        $retorno = $this->service->preparaRetornoDados(array(
            'erro' => 'Por favor defina a a��o e par�metros para executar no microservi�o.'
        ));
        $this->service->escreveRetorno($retorno);
    }

    /**
     * A��o padr�o para gerar uma NFPS-e, seu XML e PDF
     * @throws Exception
     */
    public function gerarNotaAction()
    {
        
        try {
            
            // Verifica se a autentica��o que � realizada na construtora da DAO est� OK
            if ($this->service->getDaoObject()->getTokenRequest()['sucesso'] !== true) {
                throw new Exception('N�o � poss�vel gerar NFPS-e porque a autentica��o no webService da PMF n�o foi realizada.', 401);
            }
            // Chama o m�todo espec�fico no Service para gerar a nota
            $dadosNota = $this->service->gerarNota();
            /*
             * Se gerou sem problemas, chama o m�todo para gerar o PDF para esta nota.
             * Antes chama o m�todo que efetua uma consulta simples da nota fiscal no webservice, para retornar
             * o ID �nico, que n�o � retornado no XML da nota gerada, por�m necess�rio na gera��o do PDF.
             */
            $dadosAux     = (array) simplexml_load_string(base64_decode($dadosNota['conteudoXML']));
            $dadosIdNota  = $this->service->consultarIdNota($dadosAux['numeroSerie']);
            $dadosPdfNota = $this->service->gerarPDFNota($dadosIdNota['idNota']);

            // Nota e PDF devidamente gerados, cria um array e retorna
            $arrRetorno = array(
                'conteudoXML' => $dadosNota['conteudoXML'],
                'conteudoPDF' => $dadosPdfNota['conteudoPDF']
            );

            $this->service->escreveRetorno(json_encode($arrRetorno));
        } catch (Exception $ex) {
            $this->service->reportaErro($ex);
            exit;
        }
    }

    /**
     * A��o para cancelar uma NFPS-e
     * @throws Exception
     */
    public function cancelarNotaAction()
    {
        try {
            $post = $this->service->getDadosPost()['dadosNota'];

            // Verifica se a autentica��o que � realizada na construtora da DAO est� OK
            if ($this->service->getDaoObject()->getTokenRequest()['sucesso'] !== true) {
                throw new Exception('N�o � poss�vel gerar NFPS-e porque a autentica��o no wenservice da PMF n�o foi realizada.', 401);
            }

            // Chama o m�todo espec�fico no Service para cancelar a nota
            $dadosNota    = $this->service->cancelarNota();
            $dadosIdNota  = $this->service->consultarIdNota($post['numeroSerie']);
            $dadosPdfNota = $this->service->gerarPDFNota($dadosIdNota['idNota']);

            // Nota e PDF devidamente gerados, cria um array e retorna
            $arrRetorno = array(
                'conteudoXML' => $dadosNota['conteudoXML'],
                'conteudoPDF' => $dadosPdfNota['conteudoPDF']
            );

            $this->service->escreveRetorno(json_encode($arrRetorno));
        } catch (Exception $ex) {
            $this->service->reportaErro($ex);
            exit;
        }
    }
}