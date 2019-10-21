<?php // $Rev: 141 $ - $Author: eduluz $ $Date: 2008-09-16 17:15:08 -0300 (ter, 16 set 2008) $

//require_once('./lib/gama/sys/acesso/ar/transacao.ar.php');
//require_once('./lib/gama/sys/acesso/bo/SysTransacao.bo.php');

$action = new SysTransacaoAction($this,$GET,$POST);
return $action->exec($GET,$POST);

// ---

class SysTransacaoAction extends BaseAction {

	/**
	 * Construtor da classe.
	 *
	 * @param MainGama $app
	 * @param array $GET
	 * @param array $POST
	 * @return SysTransacaoAction
	 */
	function SysTransacaoAction(&$app, $GET, $POST) {
		parent::BaseAction($app,$GET,$POST,'./lib/gama/sys/acesso');
		$this->registraAcao('showFormCadTransacao');
		$this->registraAcao('doCadTransacao');
		$this->registraAcao('showFormListaTransacoes');
		$this->registraAcao('doListarTransacoes');
		$this->registraAcao('showFormAltTransacao');
		$this->registraAcao('doAltTransacao');
		$this->registraAcao('doDelTransacao');
		$this->registraAcao('doReativarTransacao');
		$this->registraAcao('showTransacao');
	}


	/**
	 * Retorna uma inst�ncia de TransacaoBO, preenchido com base nos dados
	 * vindos por par�metros.
	 *
	 * @return SysTransacaoBO
	 */
	function getTransacaoBO() {
		$bo = new SysTransacaoBO($this->getApp());
		$bo->setId($this->getParms('oid'));
		$bo->setNome($this->getParms('nome'));
		return $bo;
	}

	function showIndex() {
		$this->showMenuTransacao();
	}

	function showMenuTransacao() {
		$this->getSmarty()->display('menu_transacao.tpl');
	}




	/**
	 * Exibe o formul�rio para cadastro do usu�rio.
	 *
	 */
	function showFormCadTransacao() {
		$saida =  array ('template' => base64_encode($this->getSmarty()->fetch('form_cad_transacao.tpl')), 'js'=>base64_encode("lib/criarDialog.js"));
		echo json_encode($saida);
	}

	/**
	 * Realiza a grava��o do usu�rio.
	 */
	function doCadTransacao() {

		$bo = $this->getTransacaoBO();
		$bo->setStatusRegistro('A');
//		$bo->setNivelAcesso(SysTransacaoBO::USER_NIVEL_GESTOR);

		try {
			$bo->insert();
			echo "Transacao cadastrado com sucesso.";
		} catch (SysException $e) {
			echo "Erro ao cadastrar usuario.";
		}
	}


	function showFormListaTransacoes() {
		$saida =  array ('template' => base64_encode($this->getSmarty()->fetch('form_lista_transacoes.tpl')), 'js'=>base64_encode("lib/gama/sys/acesso/template/javascript/criarDlgListaTransacao.js"));
		echo json_encode($saida);
	}


	function doListarTransacoes() {
		//valores default
		$results = -1; // pegar todos registros
		$startIndex = 0; // pegar o inicio dos registros
		$filtro = "";

		// numero de registros a retornar
		if(strlen($_GET['results']) > 0) {
			$results = $_GET['results'];
		}

		//a partir de qual registro
		if(strlen($_GET['startIndex']) > 0) {
			$startIndex = $_GET['startIndex'];
		}

		// filtro a ser aplicado
		if(strlen($_GET['filtro']) > 0) {
			$filtro = $_GET['filtro'];
		}
		//cria o BO
		$bo = new SysTransacaoBO($this->getApp());
		//pega uma pagina de dados
		$allRecords = $bo->getListaTransacaos($results,$startIndex,$filtro);
		if (count($allRecords) ==0){
			$total = 0;
		} else{
			$total = $bo->getNumRegistros($filtro);
		}
		// Create return value
		$returnValue = array(
		'recordsReturned'=>count($allRecords),
		'totalRecords'=>$total,
		'startIndex'=>$startIndex,
		'records'=>$allRecords
		);
		//converte pra json
		echo json_encode($returnValue);
	}

	/**
	 * Exibe o formul�rio de altera��o de um usu�rio,
	 * selecionado em uma lista ou outro formul�rio.
	 */
	function showFormAltTransacao() {
		//o id passado pela requisi��o do javascript � setado no BO
		$bo = $this->getTransacaoBO();
		//o objeto bo � carregado com o metodo load
		$bo->load();
		//os campos do BO s�o setados no template
		$this->getSmarty()->assign('transacao',$bo);
		$this->getSmarty()->assign('lsKFiltroStatus',array_keys(SysTransacaoBO::getListaStatus()));
		$this->getSmarty()->assign('lsVFiltroStatus',array_values(SysTransacaoBO::getListaStatus()));

		//o array que ser� covertido em json � criado, 'template' � o tempalte armaty para a dialog de altera��o
		$saida =  array ('template' => base64_encode($this->getSmarty()->fetch('form_alt_transacao.tpl')),
		//'js' � o arquivo javascript que contem a fun��o para cria��o da dialog de altera��o
		'js'=>base64_encode("lib/criarDlgAlterar.js"));//essa linha � continua��o da anterior.
		//o array � convertido para json e enviado
		echo json_encode($saida);
	} // eof showFormAltTransacao


	function doAltTransacao() {
		$bo = $this->getTransacaoBO();
		try {
			$bo->update();
			echo "Usu�rio alterado com sucesso.";
		} catch (SysException $e) {
			echo "Erro ao atualizar usu�rio";
		}
	}


	function doDelTransacao() {
		$bo = $this->getTransacaoBO();
		try {
			$bo->delete();
			echo "Transacao excluido com sucesso";
		} catch (SysException $e) {
			echo "Erro ao excluir Transacao";
		}
	}


	function doReativarTransacao() {
		$bo = $this->getTransacaoBO();
		$bo->load();
		$bo->setStatusRegistro(SysTransacaoBO::ST_REG_ATIVO);
		try {
			$bo->update();
			$this->getSmarty()->assign('msg','Registro reativado com sucesso.');
		} catch (SysException $e) {
			$this->getSmarty()->assign('exception',$e);
		}
		$this->showFormAltTransacao();
	}

	function showTransacao(){
		//o id passado pela requisi��o do jascript � setado
		$bo = $this->getTransacaoBO();
		//os dados s�o carregados no BO
		$bo->load();
		//os dados do BO s�o setados no template
		$this->getSmarty()->assign('transacao',$bo);
		//o array que ser� covertido em json � criado, 'template' � o tempalte armaty para a dialog de altera��o
		$saida =  array ('template' => base64_encode($this->getSmarty()->fetch('form_visualiza_transacao.tpl')),
		//'js' � o arquivo javascript que contem a fun��o para cria��o da dialog de altera��o
		'js'=>base64_encode("lib/criarDlgVisualizar.js"));//essa linha � continua��o da anterior.
		//o array � convertido para json e enviado
		echo json_encode($saida);
	}


} // eoc SysTransacaoAction



?>