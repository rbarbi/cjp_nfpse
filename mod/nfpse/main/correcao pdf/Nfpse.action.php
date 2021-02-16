<?php

/**
 * Classe que conter� todos os m�todos que poder�o ser consumidos no webservice
 * @author Carlos Domingues <carlos.domingues@grupoapi.net.br>
 */
class NfpseAction extends DefaultAction {

	public function __construct($app, $GET, $POST) {
		// Antes de qualquer a��o, � neces�rio adicionar ao $_POST o decode do JSON que veio na requisi��o do consumidor do microsevi�o
		$dados = (array) json_decode(file_get_contents('php://input'));

		//pre($dados, 'dados na action principal');
		if (isset($dados[0])) {
			$dados = (array) $dados[0];
		}
		//pred($dados, 'dados depois de manipulado (depende do php://input)');

		if (!empty($dados)) {
			$_POST['dadosNota'] = $dados;
			$_POST['emp'] = $dados['emp'];
		}

		//pred($_POST, 'post, depois de adicionar os dados da nota');
		// Chamo construtor da classe pai
		parent::__construct($app, $GET, $POST, "./mod/".MainGama::getApp()->getM());
	}

	/**
	 * Index padr�o, caso acessar sem par�metros
	 */
	public function indexAction() {
		$retorno = $this->service->preparaRetornoDados(array(
			'erro' => 'Por favor defina a a��o e par�metros para executar no microservi�o.'
		));

		$this->service->escreveRetorno($retorno);
	}

	/**
	 * A��o padr�o para gerar uma NFPS-e, seu XML e PDF
	 * @throws Exception
	 */
	public function gerarNotaAction() {
		try {
			// Verifica se a autentica��o que � realizada na construtora da DAO est� OK
			if ($this->service->getDaoObject()->getTokenRequest()['sucesso'] !== true) {
				throw new Exception('N�o � poss�vel gerar NFPS-e porque a autentica��o no wenservice da PMF n�o foi realizada.', 401);
			}

			// Chama o m�todo espec�fico no Service para gerar a nota
			$dadosNota = $this->service->gerarNota();

			/*
			 * Se gerou sem problemas, chama o m�todo para gerar o PDF para esta nota.
			 * Antes chama o m�todo que efetua uma consulta simples da nota fiscal no webservice, para retornar
			 * o ID �nico, que n�o � retornado no XML da nota gerada, por�m necess�rio na gera��o do PDF.
			 */
			$dadosAux = (array) simplexml_load_string(base64_decode($dadosNota['conteudoXML']));
			$dadosIdNota = $this->service->consultarIdNota($dadosAux['numeroSerie']);
			$dadosPdfNota = $this->service->gerarPDFNota($dadosAux['numeroSerie'], $dadosIdNota['idNota'], $dadosIdNota['cmcNota']);

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
	public function cancelarNotaAction() {
		try {
			$post = $this->service->getDadosPost()['dadosNota'];

			// Verifica se a autentica��o que � realizada na construtora da DAO est� OK
			if ($this->service->getDaoObject()->getTokenRequest()['sucesso'] !== true) {
				throw new Exception('N�o � poss�vel gerar NFPS-e porque a autentica��o no wenservice da PMF n�o foi realizada.', 401);
			}

			// Chama o m�todo espec�fico no Service para cancelar a nota
			$dadosNota = $this->service->cancelarNota();
			$dadosIdNota = $this->service->consultarIdNota($post['numeroSerie']);
			$dadosPdfNota = $this->service->gerarPDFNota($post['numeroSerie'], $dadosIdNota['idNota'], $dadosIdNota['cmcNota']);

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
	 * M�todo para consultar notas geradas na PMF com base na data inicial e final passadas.
	 * Retorna um array de n�meros de s�rie das notas consultadas.
	 */
	public function consultarNotasPorDatasAction() {
		try {
			// Verifica se a autentica��o que � realizada na construtora da DAO est� OK
			if ($this->service->getDaoObject()->getTokenRequest()['sucesso'] !== true) {
				throw new Exception('N�o � poss�vel gerar NFPS-e porque a autentica��o no wenservice da PMF n�o foi realizada.', 401);
			}

			$this->service->escreveRetorno(json_encode($this->service->consultarNotasPorDatas()));
		} catch (Exception $ex) {
			$this->service->reportaErro($ex);
			exit;
		}
	}

	/**
	 * A��o para retornar o XML e PDF de uma nota fiscal com base nos n�meros de s�rie passado
	 * @throws Exception
	 */
	public function consultarNotasPorNumerosSerieAction() {
		try {
			// Verifica se a autentica��o que � realizada na construtora da DAO est� OK
			if ($this->service->getDaoObject()->getTokenRequest()['sucesso'] !== true) {
				throw new Exception('N�o � poss�vel gerar NFPS-e porque a autentica��o no wenservice da PMF n�o foi realizada.', 401);
			}

			$dadosPost = $this->service->getDadosPost()['dadosNota'];

			// Valida��es b�sicas
			if (!isset($dadosPost['emp'])) {
				throw new Exception('Par�metro "emp" � obrigat�rio', 400);
			}
			if (!isset($dadosPost['series'])) {
				throw new Exception('Par�metro "series" � obrigat�rio', 400);
			}

			$arrRetorno = [];
			foreach ($dadosPost['series'] as $numeroSerie) {
				$dadosIdNota = $this->service->consultarIdNota($numeroSerie);
				$dadosXmlNota = $this->service->retornarXMLNota($dadosIdNota['idNota'], $dadosIdNota['cmcNota']);
				$dadosPdfNota = $this->service->gerarPDFNota($numeroSerie, $dadosIdNota['idNota'], $dadosIdNota['cmcNota']);

				// Nota e PDF devidamente gerados, cria um array e retorna
				$arrRetorno[] = [
					'numeroSerie' => $numeroSerie,
					'conteudoXML' => $dadosXmlNota['conteudoXML'],
					'conteudoPDF' => $dadosPdfNota['conteudoPDF']
				];
			}

			$this->service->escreveRetorno(json_encode($arrRetorno));
		} catch (Exception $ex) {
			$this->service->reportaErro($ex);
			exit;
		}
	}

	/**
	 * M�todo que � utilizado na intranet da POL para consultar os valores de reten��es de uma fatura
	 */
	public function consultarRetencoesFaturaAction() {
		try {
			$dadosFatura = $this->service->getDadosPost();
			$arrRetorno = $this->service->consultarRetencoesFatura($dadosFatura['dadosNota']);

			$this->service->escreveRetorno(json_encode($arrRetorno));
		} catch (Exception $ex) {
			$this->service->reportaErro($ex);
			exit;
		}
	}

	/**
	 * M�todo que ser� executado de 30 em 30 minutos na CRON, para atualizar o valor da fatura com base nas reten��es de impostos
	 */
	public function atualizaValorRetencaoFaturasAction() {
		$dataInicial = !empty($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;

		$this->service->atualizaValorRetencaoFaturas($dataInicial);
	}
}