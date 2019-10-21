<?php // $Rev: 384 $ $Author: eduluz $ $Date: 2009-04-29 14:17:13 -0300 (qua, 29 abr 2009) $//


/**
 * Classe que agrupa as informa��es de usu�rio logado em uma sess�o de um sistema.
 *
 * @copyright IASoft Desenvolvimento de Sistemas
 * @author Eduardo Schmitt da Luz
 * @package gama3.base.profile
 *
 */
class SysProfile {

	 /**
	  * Usu�rio que est� logado.
	  *
	  * @var SysUsuarioBO usuario
	  */
	 private $usuario;

	 /**
	  * Unidade organizacional da empresa em que est� lotado, ou a qual representa
	  * o usu�rio em quest�o.
	  *
	  * @var SysUnidade unidade
	  */
	 private $unidade;


	 /**
	  * Defini��o de localiza��o, para fins de internacionaliza��o.
	  *
	  * @var string locale
	  */
	 private $locale;



	 /**
	  * Lista de Transacoes que sao permitidas para o usuario em quest�o.
	  *
	  * @var array transacoesPermitidas
	  */
	 private $transacoesPermitidas;


	 /**
	  * @var string padraoPerfil
	  */
	 private $padraoPerfil;



	 function __construct() {
	 	$this->transacoesPermitidas = array();
	 }


//--------------------------------------------

	/**
	 * Retorna o valor de usuario
	 * @return SysUsuarioBO
	 */
	public function getUsuario () {
		if (is_null($this->usuario)) {
			$this->usuario = new SysUsuarioBO();
		}
		return $this->usuario;
	} // eof getUsuario

	/**
	 * Retorna o valor de unidade
	 * @return mixed
	 */
	public function getUnidade () {
		return $this->unidade;
	} // eof getUnidade




	/**
	 * Retorna o valor de locale
	 * @return mixed
	 */
	public function getLocale () {
		return $this->locale;
	} // eof getLocale




	/**
	 * Retorna o valor de transacoesPermitidas
	 * @return mixed
	 */
	public function getTransacoesPermitidas () {
		return $this->transacoesPermitidas;
	} // eof getTransacoesPermitidas



	/**
	 * Define o valor de padraoPerfil
	 * @param mixed $padraoPerfil
	 */
	public function setPadraoPerfil ($padraoPerfil) {
		$this->padraoPerfil = $padraoPerfil;
	} // eof setPadraoPerfil



//--------------------------------------------

	/**
	 * Define o valor de locale
	 * @param mixed $locale
	 */
	public function setLocale ($locale) {
		$this->locale = $locale;
	} // eof setLocale



	/**
	 * Define o valor de usuario
	 * @param SysUsuarioBO $usuario
	 */
	public function setUsuario ($usuario) {
		if (is_null($usuario->getUsername()) && ($usuario->getID() > 0)) {
			$usuario->load();
		}
		$this->usuario = $usuario;
	} // eof setUsuario

	/**
	 * Define o valor de unidade
	 * @param mixed $unidade
	 */
	public function setUnidade ($unidade) {
		$this->unidade = $unidade;
	} // eof setUnidade


	/**
	 * Define o valor de transacoesPermitidas
	 * @param mixed $transacoesPermitidas
	 */
	public function setTransacoesPermitidas ($transacoesPermitidas) {
		$this->transacoesPermitidas = $transacoesPermitidas;
	} // eof setTransacoesPermitidas



	/**
	 * Retorna o valor de padraoPerfil
	 * @return mixed
	 */
	public function getPadraoPerfil () {
		return $this->padraoPerfil;
	} // eof getPadraoPerfil


} // eoc SysProfile


?>