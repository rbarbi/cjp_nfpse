<?php // $Rev: 244 $ - $Author: eduluz $ $Date: 2008-11-19 11:20:08 -0200 (Wed, 19 Nov 2008) $

/**
 * Classe que gerencia as requisições de transações das transações do
 * sistema.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.transacao
 *
 */
class SysTransacaoAction extends SysBaseAction {

	/**
	 * Construtor da classe.
	 *
	 * @param MainGama $app
	 * @param array $get
	 * @param array $post
	 * @return SysTransacaoAction
	 */
	function SysTransacaoAction($app, $get, $post) {
		$this->SysBaseAction($app,$get,$post);
		$this->setBasePath('./mod/sistema/transacao');
		$this->registraAcao('showFormCadTransacao');
		$this->registraAcao('doCadTransacao');
		$this->registraAcao('showFormAltTransacao');
		$this->registraAcao('doAltTransacao');
		$this->registraAcao('showFormListaTransacoes');
		$this->registraAcao('doListarTransacoes');
		$this->registraAcao('doDelTransacao');
		$this->registraAcao('showTransacao');
	} // eof SysTransacaoAction


	/**
	 * Exibe o formulário para cadastro de uma transação.
	 */
	function showFormCadTransacao() {
		$this->exibirPagina('formCadTransacao.tpl');
	} // eof showFormCadTransacao


	/**
	 * Realiza o cadastro da transação.
	 */
	function doCadTransacao() {
		$transacao = $this->getTransacaoBO();
//		print_r($transacao); exit;
		try {
			$transacao->insert();
			$this->getSmarty()->assign('msg','Registro incluido com sucesso');
		} catch (Exception $e) {
			$this->getSmarty()->assign('exception',$e);
		}

		$this->exibirPagina('formCadTransacao.tpl');
	} // eof doCadTransacao




	/**
	 * Exibe o formulário com a lista de transações.
	 */
	function showFormListaTransacoes() {
		$this->doListarTransacoes();
	} // eof showFormListaTransacoes

	/**
	 * Realiza a recuperação e exibe a lista de transações.
	 *
	 * Aqui estou fazendo uma consulta, recuperando uma lista de
	 * transações, renderizando uma tabela com esses dados, e depois
	 * renderizando o formulário 'formListaTransacoes', que contém uma
	 * área nele onde deve estar a lista.
	 *
	 * Essa lógica foi implementada assim para facilitar as alterações
	 * futuras para acoplar o ajax ou algum framework de interface.
	 */
	function doListarTransacoes() {
		$dao = new SysDAO();
		$lista = $dao->listarRegistrosTransacoesAtivas();

		$this->getSmarty()->assign('lista',$lista);
		$s = $this->getSmarty()->fetch('listaTransacoes.tpl');

		$this->getSmarty()->assign('listagem',$s);
		$this->exibirPagina('formListaTransacoes.tpl');
		
//		$bo = new SysAutorizacaoBO();
//		echo '<pre>';
//		print_r($bo->getListaTransacoesPermitidasUsuario(MainGama::getApp()->getSess()->getProfile()->getUsuario()));
		
	} // eof doListarTransacoes




	/**
	 * @return SysTransacaoBO
	 */
	function getTransacaoBO() {
		$bo = new SysTransacaoBO();
		$bo->setID($this->getParms('id'));
		$bo->setNome($this->getParms('nome'));
		$bo->setDescricao($this->getParms('descricao'));

		if ($this->getParms('_m',false)) {	
			$bo->setM($this->getParms('_m'));
			$bo->setU($this->getParms('_u'));
			$bo->setA($this->getParms('_a'));
			$bo->setAcao($this->getParms('_acao'));
		} else {
			$bo->setM($this->getParms('m'));
			$bo->setU($this->getParms('u'));
			$bo->setA($this->getParms('a'));			
			$bo->setAcao($this->getParms('acao'));
		}
		

		$bo->setNivelMinimo($this->getParms('nivel'));
		$bo->setPermissaoDefault($this->getParms('permissao'));
		return $bo;
	}



	function showIndex() {
		$this->showIndexPrincipal();
	} // eof showIndex



	function showFormAltTransacao() {
		$bo = $this->getTransacaoBO();
		$bo->setID($this->getParms('id'));
		$bo->load();
		$this->getSmarty()->assign('bo',$bo);
		$this->exibirPagina('formAltTransacao.tpl');
	}

	function doAltTransacao() {
		$bo = $this->getTransacaoBO();
		
//		
//		echo '<pre>';
//		print_r($bo);
//		exit;
		
		$bo->update();
		$this->getSmarty()->assign('msg','Registro alterado com sucesso');
		$this->doListarTransacoes();
	}


	function doDelTransacao() {
		$bo = $this->getTransacaoBO();
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

		$this->doListarTransacoes();
	}

	function showTransacao() {
		$bo = $this->getTransacaoBO();
		$bo->load();
		$this->getSmarty()->assign('transacao',$bo);
		$this->exibirPagina('showTransacao.tpl');
	}





} // eoc SysTransacaoAction


?>