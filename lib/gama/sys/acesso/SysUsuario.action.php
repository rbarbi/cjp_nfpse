<?php // $Rev: 13 $ - $Author: rodrigo $ $Date: 2008-06-12 14:31:27 -0300 (qui, 12 jun 2008) $

require_once('./lib/gama/sys/acesso/ar/usuario.ar.php');
require_once('./lib/gama/sys/acesso/bo/SysUsuario.bo.php');

$action = new SysUsuarioAction($this,$GET,$POST);
return $action->exec($GET,$POST);

// ---

class SysUsuarioAction extends BaseAction {

	/**
	 * Construtor da classe.
	 *
	 * @param MainGama $app
	 * @param array $GET
	 * @param array $POST
	 * @return SysUsuarioAction
	 */
	function SysUsuarioAction(&$app, $GET, $POST) {
		parent::BaseAction($app,$GET,$POST,'./lib/gama/sys/acesso');
		$this->registraAcao('showFormCadUsuario');
		$this->registraAcao('doCadUsuario');
		$this->registraAcao('showFormListaUsuarios');
		$this->registraAcao('doListarUsuarios');
		$this->registraAcao('showFormAltUsuario');
		$this->registraAcao('doAltUsuario');
		$this->registraAcao('doDelUsuario');
		$this->registraAcao('doReativarUsuario');
		$this->registraAcao('showUsuario');
	}


	/**
	 * Retorna uma inst�ncia de UsuarioBO, preenchido com base nos dados
	 * vindos por par�metros.
	 *
	 * @return SysUsuarioBO
	 */
	function getUsuarioBO() {
		$bo = new SysUsuarioBO($this->getApp());
		$bo->setId($this->getParms('oid'));
		$bo->setNome($this->getParms('nome'));
		$bo->setUsername($this->getParms('username'));
		$bo->setSenha($this->getParms('senha'));
		$bo->setStatusRegistro($this->getParms('statusRegistro'));
		$bo->setNivelAcesso($this->getParms('nivelAcesso'));
		return $bo;
	}

	function showIndex() {
		$this->showMenuUsuario();
	}

	function showMenuUsuario() {
		$this->getSmarty()->display('menu_usuario.tpl');
	}




	/**
	 * Exibe o formul�rio para cadastro do usu�rio.
	 *
	 */
	function showFormCadUsuario() {
		$saida =  array ('template' => base64_encode($this->getSmarty()->fetch('form_cad_usuario.tpl')), 'js'=>base64_encode("lib/criarDialog.js"));
		echo json_encode($saida);
	}

	/**
	 * Realiza a grava��o do usu�rio.
	 */
	function doCadUsuario() {

		$bo = $this->getUsuarioBO();
		$bo->setStatusRegistro('A');
		$bo->setNivelAcesso(SysUsuarioBO::USER_NIVEL_GESTOR);

		try {
			$bo->insert();
			echo "Usuario cadastrado com sucesso.";
		} catch (SysException $e) {
			echo "Erro ao cadastrar usuario.";
		}
	}


	function showFormListaUsuarios() {
		$saida =  array ('template' => base64_encode($this->getSmarty()->fetch('form_lista_usuarios.tpl')), 'js'=>base64_encode("lib/gama/sys/acesso/template/javascript/criarDlgListaUsuario.js"));
		echo json_encode($saida);
	}


	function doListarUsuarios() {
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
		$bo = new SysUsuarioBO($this->getApp());
		//pega uma pagina de dados
		$allRecords = $bo->getListaUsuarios($results,$startIndex,$filtro);
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
	function showFormAltUsuario() {
		//o id passado pela requisi��o do javascript � setado no BO
		$bo = $this->getUsuarioBO();
		//o objeto bo � carregado com o metodo load
		$bo->load();
		//os campos do BO s�o setados no template
		$this->getSmarty()->assign('usuario',$bo);
		$this->getSmarty()->assign('lsKFiltroStatus',array_keys(SysUsuarioBO::getListaStatus()));
		$this->getSmarty()->assign('lsVFiltroStatus',array_values(SysUsuarioBO::getListaStatus()));

		//o array que ser� covertido em json � criado, 'template' � o tempalte armaty para a dialog de altera��o
		$saida =  array ('template' => base64_encode($this->getSmarty()->fetch('form_alt_usuario.tpl')),
		//'js' � o arquivo javascript que contem a fun��o para cria��o da dialog de altera��o
		'js'=>base64_encode("lib/criarDlgAlterar.js"));//essa linha � continua��o da anterior.
		//o array � convertido para json e enviado
		echo json_encode($saida);
	} // eof showFormAltUsuario


	function doAltUsuario() {
		$bo = $this->getUsuarioBO();
		try {
			$bo->update();
			echo "Usu�rio alterado com sucesso.";
		} catch (SysException $e) {
			echo "Erro ao atualizar usu�rio";
		}
	}


	function doDelUsuario() {
		$bo = $this->getUsuarioBO();
		try {
			$bo->delete();
			echo "Usuario excluido com sucesso";
		} catch (SysException $e) {
			echo "Erro ao excluir Usuario";
		}
	}


	function doReativarUsuario() {
		$bo = $this->getUsuarioBO();
		$bo->load();
		$bo->setStatusRegistro(SysUsuarioBO::ST_REG_ATIVO);
		try {
			$bo->update();
			$this->getSmarty()->assign('msg','Registro reativado com sucesso.');
		} catch (SysException $e) {
			$this->getSmarty()->assign('exception',$e);
		}
		$this->showFormAltUsuario();
	}

	function showUsuario(){
		//o id passado pela requisi��o do jascript � setado
		$bo = $this->getUsuarioBO();
		//os dados s�o carregados no BO
		$bo->load();
		//os dados do BO s�o setados no template
		$this->getSmarty()->assign('usuario',$bo);
		//o array que ser� covertido em json � criado, 'template' � o tempalte armaty para a dialog de altera��o
		$saida =  array ('template' => base64_encode($this->getSmarty()->fetch('form_visualiza_usuario.tpl')),
		//'js' � o arquivo javascript que contem a fun��o para cria��o da dialog de altera��o
		'js'=>base64_encode("lib/criarDlgVisualizar.js"));//essa linha � continua��o da anterior.
		//o array � convertido para json e enviado
		echo json_encode($saida);
	}


} // eoc SysUsuarioAction



?>