<?php // $Rev: 169 $ - $Author: eduluz $ $Date: 2008-09-30 17:31:38 -0300 (Tue, 30 Sep 2008) $//

/**
 * Classe responsável pela exibição do formulário de login, e
 * recepção dos dados informados pelo usuário.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.autorizacao
 */
class SysAutorizacaoAction extends SysBaseAction {



	public function SysAutorizacaoAction ($app, $get, $post) {
		$this->SysBaseAction($app,$get,$post);
		$this->registraAcao('showFormLogin');
//		$this->registraAcao('doLogin');
		$this->setBasePath('./mod/sistema/autorizacao');
	} // eof SysAutorizacaoAction



	function showIndex() {
		$this->getApp()->redirecionaActionLogin();	
//		try {
//			$bo = new SysAutorizacaoBO();
//			$bo->estaAutorizado($this->getParms('usuID'),$this->getParms('mm'),$this->getParms('uu'),$this->getParms('aa'),$this->getParms('acao2'));
//		} catch (Exception $e) {
////			echo $e->getMessage(); exit;
//			$this->getApp()->redirecionaActionLogin();
////			header('Location: index.php?m=sistema&u=usuario&a=SysUsuario&acao=showFormLogin');
////			exit;
//		}
//		print_r($r); exit;
//		print_r($this->getParms()); exit;
	}


	/**
	 * Exibe o formulário de login.
	 */
	function showFormLogin() {

		$e = $this->getApp()->getException(false);
		if ($e->getCode() != 0) {
			$this->getSmarty()->assign('msg',$e->getMessage());			
			$this->getApp()->getException(true);
		}
//		echo 'cheguei aqui <br>'; 
//		print_r($e);
//		exit;


//		if ($this->getApp()->getSess()->get('msgErro',false)) {
//			$this->getSmarty()->assign('msg',$this->getApp()->getSess()->get('msgErro'));
//			$this->getApp()->getSess()->set('msgErro',false);
//		}

		$this->exibirPagina('formLogin.tpl',false);
	} // eof showFormLogin


	/**
	 * Realiza o login.
	 * @todo Eliminar este método. Ele será substituído dentro do  MainGama
	 */
//	function doLogin() {
//		echo 'nao deveria ter chegado aqui..., pois deveria ter realizado o login no MainGama';
//		$this->showIndexPrincipal();
//	} // eof doLogin


} // eof SysAutorizacaoAction

?>