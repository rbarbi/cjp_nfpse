<?php // $Rev: 226 $ - $Author: eduluz $ $Date: 2008-11-05 18:27:01 -0200 (Wed, 05 Nov 2008) $//

//require_once('./mod/sistema/usuario/bo/SysUsuario.bo.php');
//require_once('./mod/sistema/usuario/vo/SysUsuario.vo.php');
//require_once('./mod/sistema/usuario/dao/SysUsuario.dao.php');
//require_once('./mod/sistema/usuario/ar/SysUsuario.ar.php');

//require_once('./mod/sistema/transacao/bo/SysTransacao.bo.php');
//require_once('./mod/sistema/transacao/dao/SysTransacao.dao.php');
//require_once('./mod/sistema/transacao/ar/SysTransacao.ar.php');


/**
 * Classe que centraliza as permissões de acesso a transações do
 * sistema.
 *
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.permissao
 */
class SysPermissaoAction extends SysBaseAction {


	/**
	 * Construtor da classe
	 *
	 * @param MainGama $app
	 * @param array $get
	 * @param array $post
	 * @return SysPermissaoAction
	 */
	function SysPermissaoAction($app, $get, $post) {
		$this->SysBaseAction($app,$get,$post);
		$this->setBasePath('./mod/sistema/permissao');
	} // eof SysPermissaoAction



	/**
	 * Exibe o formulário principal.
	 *
	 */
	function showIndex() {
		$this->showIndexPrincipal();
	} // eof showIndex




} // eoc SysPermissaoAction


?>