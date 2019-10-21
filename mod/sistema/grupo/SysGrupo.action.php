<?php // $Rev: 154 $ - $Author: eduluz $ $Date: 2008-09-26 17:57:01 -0300 (sex, 26 set 2008) $

/**
 * Classe que gerencia as requisições de transações de usuários do
 * sistema.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.Grupo
 *
 */
class SysGrupoAction extends SysBaseAction {


	/**
	 * Construtor da classe
	 *
	 * @param MainGama $app
	 * @param array $get
	 * @param array $post
	 * @return SysGrupoAction
	 */
	function SysGrupoAction($app, $get, $post) {
		$this->SysBaseAction($app,$get,$post);
		$this->setBasePath('./mod/sistema/Grupo');
		$this->registraAcao('showFormCadGrupo');
		$this->registraAcao('doCadGrupo');
		$this->registraAcao('showFormListaGrupos');
		$this->registraAcao('doListarGrupos');
		$this->registraAcao('showFormAltGrupo');
		$this->registraAcao('doAltGrupo');
		$this->registraAcao('doDelGrupo');
		$this->registraAcao('showGrupo');
		$this->registraAcao('showFormIncluirUsuarioGrupo');
		$this->registraAcao('doIncluirUsuarioGrupo');
		$this->registraAcao('showFormListaUsuariosGrupo');
		$this->registraAcao('doListarUsuariosGrupo');
		$this->registraAcao('doDelUsuarioGrupo');
	} // eof GrupoAction


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
	function showFormCadGrupo() {
		$this->exibirPagina('formCadGrupo.tpl');
	} // eof showFormCadGrupo


	/**
	 * Realiza o cadastro de usuários.
	 *
	 */
	function doCadGrupo() {
		$Grupo = $this->getGrupoBO();

		try {
			$Grupo->insert();
			$this->getSmarty()->assign('msg','Registro incluido com sucesso');
		} catch (Exception $e) {
			$this->getSmarty()->assign('exception',$e);
		}

		$this->exibirPagina('formCadGrupo.tpl');
		//		echo "<hr>" . mb_detect_encoding($this->getParms('nome'));

	} // eof doCadGrupo





	/**
	 * Monta uma instância de usuário (SysGrupoBO), preenchendo-a
	 * com os dados vindos do formulário, e devolve-a.
	 *
	 * @return SysGrupoBO
	 */
	function getGrupoBO() {
		$bo = new SysGrupoBO();
		$bo->setID($this->getParms('id'));
		$bo->setNome($this->getParms('nome'));
		$bo->setDescricao($this->getParms('descricao'));
		$bo->setStatus(BasePersistenteBO::ST_REG_ATIVO);
		$bo->getUsuarioAdmin()->setID($this->getApp()->getSess()->getProfile()->getUsuario()->getID());
		return $bo;
	} // eof getGrupoBO


	/**
	 * Exibe o formulário para listagem dos usuários.
	 *
	 */
	function showFormListaGrupos() {
		$this->doListarGrupos();
		// $this->exibirPagina('formListaGrupos.tpl');
	} // eof showFormListaGrupos


	/**
	 * Realiza a listagem de usuários
	 */
	function doListarGrupos() {
		//		$bo = $this->getGrupoBO();

		$dao = new SysDAO();


		$lista = $dao->listarRegistrosGruposAtivos();

		$this->getSmarty()->assign('lista',$lista);
		$s = $this->getSmarty()->fetch('listaGrupos.tpl');

		$this->getSmarty()->assign('listagem',$s);
		$this->exibirPagina('formListaGrupos.tpl');
	} // eof doListarGrupos



	/**
	 * Exibe o formulário para alteração de um usuário selecionado.
	 */
	function showFormAltGrupo() {
		if (!$this->getParms('id',false)) {
			//			if (!$this->getApp()->getSess()->get('usuID',false)) {
			$this->getSmarty()->assign('msg','E necessario selecionar o Grupo');
			return $this->doListarGrupos();
			//			} else {
			//				$this->setParm('id',$this->getApp()->getSess()->get('usuID'));
			//			}
			//		} else {
			//			$this->getApp()->getSess()->set('usuID',$this->getParms('id'));
		}
		$bo = $this->getGrupoBO();
		$bo->setID($this->getParms('id'));
		$bo->load();
		$this->getSmarty()->assign('bo',$bo);
		$this->exibirPagina('formAltGrupo.tpl');
	} // eof showFormAltGrupo



	/**
	 * Realiza a alteração do usuário.
	 */
	function doAltGrupo() {
		$bo = $this->getGrupoBO();


		$bo->update();
		$this->getSmarty()->assign('msg','Registro alterado com sucesso');
		$this->doListarGrupos();
	} // eof doAltGrupo


	/**
	 * Realiza a exclusão física ou lógica de um registro de usuário.
	 * + Primeiramente tenta-se excluir o registro normalmente (exclusão física);
	 * + Se este usuário possui algum vínculo com outros registros do sistema,
	 * vai ocorrer um erro de integridade referencial - neste caso será feita a
	 * exclusão lógica, que consiste na definição do valor da flag de status do
	 * registro como Inativa (I).
	 *
	 */
	function doDelGrupo() {
		$bo = $this->getGrupoBO();
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

		$this->doListarGrupos();
	} // eof doDelGrupo



	/**
	 * Exibe um registro de um usuário selecionado.
	 *
	 */
	function showGrupo() {
		if (!$this->getParms('id',false)) {
			$this->showFormListaGrupos();
		} else {
			$bo = new SysGrupoBO();
			$bo->setID($this->getParms('id'));
			$bo->load();
			$this->getSmarty()->assign('grupo',$bo);
			$this->exibirPagina('showGrupo.tpl');
		}
	} // eof showGrupo


	/**
	 * Exibe o formulário para inclusão de usuários no grupo selecionado.
	 *
	 */
	function showFormIncluirUsuarioGrupo() {

		$grupo = new SysGrupoBO();
		$grupo->setID($this->getParms('grupoID'));

		$dao = new SysDAO();
		$lsUsuarios = $dao->getArrayUsuarios();
		$lsUsuarios->setChave($this->getParms('usuID',$this->getApp()->getSess()->getProfile()->getUsuario()->getID()));

		$this->getSmarty()->assign('lsUsuarios',$lsUsuarios);
		$this->getSmarty()->assign('grupo',$grupo);

		$this->exibirPagina('formCadUsuarioGrupo.tpl',false);
	} // eof showFormIncluirUsuarioGrupo



	/**
	 * Efetiva a inclusão do usuário neste grupo.
	 *
	 */
	function doIncluirUsuarioGrupo() {
		$grupo = new SysGrupoBO();
		$grupo->setID($this->getParms('grupoID'));
		try {
			$grupo->incluiUsuarioGrupo($this->getParms('usuID'));
			$this->getSmarty()->assign('msg','Inclusao efetuada com sucesso');
		} catch (Exception $e) {
			if ($e->getCode() == -5) {
				$se = new SysException('Este usuario ja esta associado a este grupo',$e->getCode());
			} else {
				$se = new SysException($e->getMessage(),$e->getCode());
			}
			$this->getSmarty()->assign('msg',$se->getMessage());
			$se->setDescricao($e->getMessage());
			$this->getApp()->setException($se);
		}
		$this->showFormIncluirUsuarioGrupo();
	} // eof doIncluirUsuarioGrupo


	/**
	 * Exibe o formulário para consulta dos usuários que fazem parte deste grupo.
	 *
	 */
	function showFormListaUsuariosGrupo() {
		$this->doListarUsuariosGrupo();
	} // eof showFormListaUsuariosGrupo


	/**
	 * Exibe a lista de usuários que pertencem a este grupo.
	 *
	 */
	function doListarUsuariosGrupo() {
		$dao = new SysDAO();
		$lista = $dao->getListaUsuariosGrupo($this->getParms('grupoID'));
		$this->getSmarty()->assign('lista',$lista);
		$this->exibirPagina('listaUsuariosGrupo.tpl',false);
	} // eof doListarUsuariosGrupo


	/**
	 * Exclui o usuário do grupo.
	 *
	 */
	function doDelUsuarioGrupo() {
		$grupo = new SysGrupoBO();
		$grupo->setID($this->getParms('grupoID'));
		try {
			$grupo->excluiUsuarioGrupo($this->getParms('usuID'));
			$this->getSmarty()->assign('msg','Exclusao efetuada com sucesso');
		} catch (Exception $e) {
			$se = new SysException($e->getMessage(),$e->getCode());
			$this->getSmarty()->assign('msg',$se->getMessage());
			$se->setDescricao($e->getMessage());
			$this->getApp()->setException($se);
		}
		$this->showFormListaUsuariosGrupo();
	}


} // eoc SysGrupoAction


?>