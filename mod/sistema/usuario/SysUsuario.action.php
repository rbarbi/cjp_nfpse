<?php // $Rev: 310 $ - $Author: eduluz $ $Date: 2008-12-31 11:22:07 -0200 (Wed, 31 Dec 2008) $

/**
 * Classe que gerencia as requisições de transações de usuários do
 * sistema.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.usuario
 *
 */
class SysUsuarioAction extends SysBaseAction {


	/**
	 * Construtor da classe
	 *
	 * @param MainGama $app
	 * @param array $get
	 * @param array $post
	 * @return SysUsuarioAction
	 */
	function SysUsuarioAction($app, $get, $post) {
		$this->SysBaseAction($app,$get,$post);
		$this->setBasePath('./mod/sistema/usuario');
		$this->registraAcao('showFormCadUsuario');
		$this->registraAcao('doCadUsuario');
		$this->registraAcao('doCadUsuario3');
		$this->registraAcao('showFormListaUsuarios');
		$this->registraAcao('doListarUsuarios');
		$this->registraAcao('showFormAltUsuario');
		$this->registraAcao('doAltUsuario');
		$this->registraAcao('doDelUsuario');
		$this->registraAcao('showUsuario');
	} // eof UsuarioAction


	/**
	 * Exibe a página inicial (menu geral do sistema).
	 *
	 */
	function showIndex() {
		$this->showIndexPrincipal();
	} // eof showIndex



	/**
	 * Exibe o formulário para cadastrar usuários.
	 *
	 */
	public function showFormCadUsuario() {
		$this->exibirPagina('formCadUsuario.tpl');
	} // eof showFormCadUsuario


	/**
	 * Realiza o cadastro de usuários.
	 *
	 */
	public function doCadUsuario() {
		$usuario = $this->getUsuarioBO();
		$usuario->setNivel(SysUsuarioBO::USER_NIVEL_USUARIO);

		try {
			$usuario->insert();
			$this->getSmarty()->assign('msg','Registro incluido com sucesso');
		} catch (Exception $e) {
			$this->getSmarty()->assign('exception',$e);
		}

		$this->exibirPagina('formCadUsuario.tpl');
		//		echo "<hr>" . mb_detect_encoding($this->getParms('nome'));

	} // eof doCadUsuario


	public function doCadUsuario3() {
		$this->doCadUsuario2();
	}
	
	public function doCadUsuario2() {
//		$msg = $this->getApp()->getException(false)->getMessage();
		$msg = '*'. $this->getParms('username') . '*';
		echo "{success: false, errors: { message: '$msg' }}";
		exit;
	}


	/**
	 * Monta uma instância de usuário (SysUsuarioBO), preenchendo-a
	 * com os dados vindos do formulário, e devolve-a.
	 *
	 * @return SysUsuarioBO
	 */
	protected function getUsuarioBO() {
		$bo = new SysUsuarioBO();
		$bo->setID($this->getParms('id'));

		if (strlen($this->getParms('senha')) > 0) {
			$bo->setSenha($this->getParms('senha'));
		} else {
			if ($this->getApp()->getAcao() == 'doAltUsuario') {
				$bo->load();
				//				$bo->load('usu_id = ' . $bo->getID());
			}
		}

		$bo->setNome($this->getParms('nome'));
		//$bo->setNivelAcesso($this->getParms('nivel'));

		$bo->setUsername($this->getParms('username'));
		$bo->setStatus(BasePersistenteBO::ST_REG_ATIVO);
		return $bo;
	} // eof getUsuarioBO


	/**
	 * Exibe o formulário para listagem dos usuários.
	 *
	 */
	function showFormListaUsuarios() {
		$this->doListarUsuarios();
		// $this->exibirPagina('formListaUsuarios.tpl');
	} // eof showFormListaUsuarios


	/**
	 * Realiza a listagem de usuários
	 */
	function doListarUsuarios() {
		//		$bo = $this->getUsuarioBO();

		$dao = new SysDAO();


		$lista = $dao->listarRegistrosUsuariosAtivos();

		$this->getSmarty()->assign('lista',$lista);
		$s = $this->getSmarty()->fetch('listaUsuarios.tpl');

		$this->getSmarty()->assign('listagem',$s);
		$this->exibirPagina('formListaUsuarios.tpl');
	} // eof doListarUsuarios



	/**
	 * Exibe o formulário para alteração de um usuário selecionado.
	 */
	function showFormAltUsuario() {
		if (!$this->getParms('id',false)) {
			//			if (!$this->getApp()->getSess()->get('usuID',false)) {
			$this->getSmarty()->assign('msg','E necessario selecionar o usuario');
			return $this->doListarUsuarios();
			//			} else {
			//				$this->setParm('id',$this->getApp()->getSess()->get('usuID'));
			//			}
			//		} else {
			//			$this->getApp()->getSess()->set('usuID',$this->getParms('id'));
		}
		$bo = $this->getUsuarioBO();
		$bo->setID($this->getParms('id'));
		$bo->load();
		$this->getSmarty()->assign('bo',$bo);
		$this->exibirPagina('formAltUsuario.tpl');
	} // eof showFormAltUsuario



	/**
	 * Realiza a alteração do usuário.
	 */
	function doAltUsuario() {
		$bo = $this->getUsuarioBO();


		$bo->update();
		$this->getSmarty()->assign('msg','Registro alterado com sucesso');
		$this->doListarUsuarios();
	} // eof doAltUsuario


	/**
	 * Realiza a exclusão física ou lógica de um registro de usuário.
	 * + Primeiramente tenta-se excluir o registro normalmente (exclusão física);
	 * + Se este usuário possui algum vínculo com outros registros do sistema,
	 * vai ocorrer um erro de integridade referencial - neste caso será feita a
	 * exclusão lógica, que consiste na definição do valor da flag de status do
	 * registro como Inativa (I).
	 *
	 */
	function doDelUsuario() {
		$bo = $this->getUsuarioBO();
		$bo->setID($this->getParms('id'));
		try {
			$res = $bo->delete();
			if ($res === true) {
				$this->getSmarty()->assign('msg','Registro excluido com sucesso');
			} else if ($res === false) {
				$this->getSmarty()->assign('msg','Registro desativado com sucesso');
			}
		} catch (SysException $e) {
			$this->getSmarty()->assign('msg',' ((( '.$this->getApp()->getCon()->ErrorMsg() .' )))  '.$e->getMessage());
		}

		$this->doListarUsuarios();
	} // eof doDelUsuario



	/**
	 * Exibe um registro de um usuário selecionado.
	 *
	 */
	function showUsuario() {
		$bo = $this->getUsuarioBO();
		//		$bo = new SysUsuarioBO();
		//		$bo->setID($this->getParms('id'));
		$bo->load();
		//		$bo->load('usu_id = ' . $bo->getID());
		$this->getSmarty()->assign('usuario',$bo);
		$this->exibirPagina('showUsuario.tpl');
	} // eof showUsuario





} // eoc SysUsuarioAction


?>