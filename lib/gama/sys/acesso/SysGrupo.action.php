<?php // $Rev: 141 $ - $Author: eduluz $ $Date: 2008-09-16 17:15:08 -0300 (ter, 16 set 2008) $


//require_once('./lib/gama/sys/acesso/ar/grupo.ar.php');
//require_once('./lib/gama/sys/acesso/bo/SysGrupo.bo.php');

$action = new SysGrupoAction($this,$GET,$POST);
return $action->exec($GET,$POST);

/* ########################################################
   ########################################################
   ######################################################## */

class SysGrupoAction extends BaseAction {



	/**
	 * Construtor da classe.
	 *
	 * @param MainGama $app
	 * @param array $GET
	 * @param array $POST
	 * @return SysUsuarioAction
	 */
	function SysGrupoAction(&$app, $GET, $POST) {
		parent::BaseAction($app,$GET,$POST,'./lib/gama/sys/acesso');
		$this->registraAcao('showFormCadGrupo');
		$this->registraAcao('doCadGrupo');
//		$this->registraAcao('showFormListaUsuarios');
//		$this->registraAcao('doListarUsuarios');
//		$this->registraAcao('showFormAltUsuario');
//		$this->registraAcao('doAltUsuario');
//		$this->registraAcao('doDelUsuario');
//		$this->registraAcao('doReativarUsuario');
	}



	function showIndex() {
		$this->showMenuGrupo();
	}

	function showMenuGrupo() {
		$this->getSmarty()->display('grupo/menu_grupo.tpl');
	}


	/**
	 * @todo
	 *
	 */
	function getGrupoBO() {
		// @todo
	}





	function showFormCadGrupo() {

	}




}


?>