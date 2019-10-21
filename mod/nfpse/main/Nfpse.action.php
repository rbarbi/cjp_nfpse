<?php

/**
 * Classe que conterá todos os métodos que poderão ser consumidos no webservice
 * @author Carlos Domingues <carlos.domingues@grupoapi.net.br>
 */
class NfpseAction extends DefaultAction
{

    public function __construct($app, $GET, $POST)
    {
        // Antes de qualquer ação, é necesário adicionar ao $_POST o decode do JSON que veio na requisição do consumidor do microseviço
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
     * Index padrão, caso acessar sem parâmetros
     */
    public function indexAction()
    {
        
        $retorno = $this->service->preparaRetornoDados(array(
            'erro' => 'Por favor defina a ação e parâmetros para executar no microserviço.'
        ));
        $this->service->escreveRetorno($retorno);
    }

    /**
     * Ação padrão para gerar uma NFPS-e, seu XML e PDF
     * @throws Exception
     */
    public function gerarNotaAction()
    {
        
        try {
            
            // Verifica se a autenticação que é realizada na construtora da DAO está OK
            if ($this->service->getDaoObject()->getTokenRequest()['sucesso'] !== true) {
                throw new Exception('Não é possível gerar NFPS-e porque a autenticação no webService da PMF não foi realizada.', 401);
            }
            // Chama o método específico no Service para gerar a nota
            $dadosNota = $this->service->gerarNota();
            /*
             * Se gerou sem problemas, chama o método para gerar o PDF para esta nota.
             * Antes chama o método que efetua uma consulta simples da nota fiscal no webservice, para retornar
             * o ID único, que não é retornado no XML da nota gerada, porém necessário na geração do PDF.
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
     * Ação para cancelar uma NFPS-e
     * @throws Exception
     */
    public function cancelarNotaAction()
    {
        try {
            $post = $this->service->getDadosPost()['dadosNota'];

            // Verifica se a autenticação que é realizada na construtora da DAO está OK
            if ($this->service->getDaoObject()->getTokenRequest()['sucesso'] !== true) {
                throw new Exception('Não é possível gerar NFPS-e porque a autenticação no wenservice da PMF não foi realizada.', 401);
            }

            // Chama o método específico no Service para cancelar a nota
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