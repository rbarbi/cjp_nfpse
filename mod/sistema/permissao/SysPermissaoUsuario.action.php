<?php // $Rev: $ $Author: $ $Date: $//

/**
 * Classe que especializa as operações de permissões para usuários.
 * 
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.permissao
 */
class SysPermissaoUsuarioAction extends SysPermissaoAction {
	

	/**
	 * Construtor da classe.
	 *
	 * @param MainGama $app
	 * @param array $get
	 * @param array $post
	 * @return SysPermissaoUsuarioAction
	 */
	function SysPermissaoUsuarioAction($app, $get, $post) {
		parent::SysPermissaoAction($app,$get,$post);
		$this->registraAcao('showFormCadPermissaoUsuario');
		$this->registraAcao('doCadPermissaoUsuario');
		$this->registraAcao('doDelPermissaoUsuario');
		$this->registraAcao('showPermissoesUsuario');		
	} // eof SysPermissaoUsuarioAction
	
	
	/**
	 * Exibe o formulário para cadastro de permissões de usuário.
	 *
	 */
	function showFormCadPermissaoUsuario() {

		$dao = new SysDAO();
		$lsUsuarios = $dao->getArrayUsuarios();
		$lsUsuarios->setChave($this->getParms('usuID'));

		$transacao = new SysDAO();
		$lsTransacoes = $transacao->getArrayTransacoes();

		$permissao = new SysPermissaoBO();
		$lsPermissoes = $permissao->getArrayPermissoesDisponiveis();

		$this->getSmarty()->assign('lsUsuarios',$lsUsuarios);
		$this->getSmarty()->assign('lsTransacoes',$lsTransacoes);
		$this->getSmarty()->assign('lsPermissoes',$lsPermissoes);


		$this->getSmarty()->display('formCadPermissaoUsuario.tpl');
		//		$s = $this->getSmarty()->fetch('formCadPermissaoUsuario.tpl');
		//
		//		$this->getSmarty()->assign('corpo',$s);
		//
		//		$this->exibirPagina('../../usuario/template/showUsuario.tpl');

	} // eof showFormCadPermissaoUsuario



	/**
	 * Realiza o cadastro da permissão de usuário.
	 *
	 */
	function doCadPermissaoUsuario() {
		$permissao = new SysPermissaoUsuarioBO();
		$permissao->setPermissao($this->getParms('permissao'));
		$permissao->getUsuario()->setID($this->getParms('usuID'));
		$permissao->getTransacao()->setID($this->getParms('trID'));
		try {
			$permissao->insert();
			$this->getSmarty()->assign('msg','Inclusao efetuada com sucesso');
			MainGama::getApp()->atualizaPermissoesAcesso();
		} catch (Exception $e) {
			$this->getSmarty()->assign('msg',$e->getMessage());
		}
		$this->showFormCadPermissaoUsuario();
	} // eof doCadPermissaoUsuario


	/**
	 * Cria um BO a partir da dados vindos do formulário.
	 * @return SysPermissaoBO
	 */
	function getPermissaoUsuarioBO() {
		$bo = new SysPermissaoUsuarioBO($this->getApp());
		$bo->setID($this->getParms('usuID'));
		return $bo;
	} // eof getPermissaoUsuarioBO



	/**
	 * Exibe a lista de permissões de um dado usuário.
	 *
	 */
	function showPermissoesUsuario() {
		$dao = new SysDAO();
		$lista = $dao->getListaPermissoes($this->getParms('usuID'));
		$this->getSmarty()->assign('lista',$lista);
		$this->exibirPagina('listaPermissoesUsuario.tpl',false);
//		echo '<pre>';
//		print_r(MainGama::getApp()->getSess()->getProfile()->getTransacoesPermitidas());
	} // eof showPermissoesUsuario



	/**
	 * Exclui uma permissão
	 *
	 */
	public function doDelPermissaoUsuario() {
		$permissao = new SysPermissaoUsuarioBO();
		$permissao->setID($this->getParms('peID'));
		try {
			$permissao->delete();
			$this->getSmarty()->assign('msg','Exclusao efetuada com sucesso');
			MainGama::getApp()->atualizaPermissoesAcesso();
		} catch (Exception $e) {
			$this->getSmarty()->assign('msg',$e->getMessage());
		}
		$this->setParm('usuID',MainGama::getApp()->getSess()->getProfile()->getUsuario()->getID());
		$this->showPermissoesUsuario();
		//		$this->showFormCadPermissaoUsuario();
	} // eof doDelPermissaoUsuario

	
} // eoc SysPermissaoUsuarioAction


?>