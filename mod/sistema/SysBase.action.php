<?php // $Rev: 194 $ $Author: eduluz $ $Date: 2008-10-03 18:25:09 -0300 (Fri, 03 Oct 2008) $//

/**
 * Classe comum a todos os Actions do cadadstro e controle
 * de acesso.
 * 
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema
 */
class SysBaseAction extends BaseAction {

	/**
	 * Como � um construtor abstrato, n�o invoco o BaseAction,
	 * mas apenas este.
	 *
	 * @return SysBaseAction
	 */
	function SysBaseAction($app,$get,$post) {		
		$this->BaseAction($app,$get,$post,'./mod/sistema');
		$this->registraAcao('showIndexPrincipal');
	}


	/**
	 * M�todo que exibe a p�gina passada por par�metro.
	 * Por centralizar a exibi��o das p�ginas (templates),
	 * permite que vari�veis e procedimentos que sejam
	 * comuns a todas sejam definidas aqui.
	 *
	 * @param string $nomeTemplate
	 */
	function exibirPagina($nomeTemplate,$showFooter=true) {
//		$this->getSmarty()->assign('m',$this->getApp()->getM());
		$this->getSmarty()->assign('m',MainGama::getApp()->getM());
//		$this->getSmarty()->assign('u',$this->getApp()->getU());
		$this->getSmarty()->assign('u',MainGama::getApp()->getU());
		$this->getSmarty()->assign('a',$this->getApp()->getA());

		$this->getSmarty()->assign('_',$this->getApp()->getI18N());
//		$this->getSmarty()->assign('__',create_function('$s','return "aaa";'));

		//		Teste para verificar o esquema de definir vari�veis com as paths
		//		dos diret�rios, al�m de alterar o comportamento da aplica��o de acordo
		//		com par�metros de configura��o diferenciados.
		//
		//		$this->getSmarty()->assign('imgPath','./mod/sistema/template/img');
		//
		//		if ($this->getApp()->getConfig(array('geral','cdEmpresa'),'false') == 'IASOFT') {
		//			$this->getSmarty()->assign('imgLogoEmpresa','146.png');
		//		} else {
		//			$this->getSmarty()->assign('imgLogoEmpresa','125.png');
		//		}




		$this->getSmarty()->display($nomeTemplate);
		if ($showFooter) {
			$this->getSmarty()->display('../../template/footer.tpl');
		}
	} // eof exibirPagina



	/**
	 * P�gina que exibe a p�gina principal de todo o sistema.
	 */
	function showIndexPrincipal() {
		error_reporting(E_ALL);
		$this->getSmarty()->assign('_usuario',$this->getApp()->getSess()->get('usuario'));
		$this->exibirPagina('../../template/index.tpl');
//		echo '<pre>';
//		$lista = SysAutorizacaoBO::getListaTransacoesPermitidasUsuario($this->getApp()->getSess()->getProfile()->getUsuario()); 
//		print_r(array_keys($lista));
	} // eof showIndexPrincipal




	/**
	 * Altera o valor do objeto Smarty que vem do BaseAction, para permitir que se 
	 * coloque o profile no gerenciador de templates, e assim facilitar para o desenvolvedor
	 * da interface.
	 *
	 * @return Smarty
	 */
	function getSmarty() {		
		parent::getSmarty();
//		parent::getSmarty()->debugging = true;
//		parent::getSmarty()->debug_tpl = '../../../../lib/Smarty/debug.tpl';
		if ($this->getApp()->isLogged()) {
			$this->smarty->assign('_profile',MainGama::getApp()->getSess()->getProfile());
			

//			MainGama::getApp()->getSess()->getProfile()->getUsuario()->load();

//			$this->getApp()->getSess()->getProfile()->getUsuario()->load() ;
			$this->smarty->assign('_usuario',MainGama::getApp()->getSess()->getProfile()->getUsuario());
		}
		return $this->smarty;
	} // eof getSmarty


} // eoc SysBaseAction


?>