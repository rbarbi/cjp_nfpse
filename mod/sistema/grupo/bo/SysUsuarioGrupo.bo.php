<?php // $Rev: 154 $ - $Author: eduluz $ $Date: 2008-09-26 17:57:01 -0300 (sex, 26 set 2008) $

/**
 * Usuário no sistema.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package Gama3
 * @created 10-jun-2008 16:54:19
 */
class SysUsuarioGrupoBO extends BasePersistenteBO {


	 /**
	  * @var SysUsuarioBO usuario 
	  */
	 private $usuario;

	 /**
	  * @var SysGrupoBO grupo 
	  */
	 private $grupo;

	 /**
	  * @var int nivel 
	  */
	 private $nivel;

	 /**
	  * @var SysUsuarioBO usuarioCadastrante 
	  */
	 private $usuarioCadastrante;

	 /**
	  * @var timestamp dhCadastro 
	  */
	 private $dhCadastro;

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
	 * Retorna o valor de grupo
	 * @return SysGrupoBO
	 */
	public function getGrupo () {
		if (is_null($this->grupo)) {
			$this->grupo = new SysGrupoBO();
		}
		return $this->grupo;
	} // eof getGrupo 

	
	/**
	 * Retorna o valor de nivel
	 * @return int
	 */
	public function getNivel () {
		return $this->nivel;
	} // eof getNivel 

	
	/**
	 * Retorna o valor de usuarioCadastrante
	 * @return SysUsuarioBO
	 */
	public function getUsuarioCadastrante () {
		if (is_null($this->usuarioCadastrante)) {
			$this->usuarioCadastrante = new SysUsuarioBO();
		}
		return $this->usuarioCadastrante;
	} // eof getUsuarioCadastrante 

	
	/**
	 * Retorna o valor de dhCadastro
	 * @return timestamp
	 */
	public function getDhCadastro () {
		return $this->dhCadastro;
	} // eof getDhCadastro 



//--------------------------------------------

	/**
	 * Define o valor de usuario
	 * @param SysUsuarioBO $usuario
	 */
	public function setUsuario ($usuario) {
		$this->usuario = $usuario;
	} // eof setUsuario 

	
	/**
	 * Define o valor de grupo
	 * @param SysGrupoBO $grupo
	 */
	public function setGrupo ($grupo) {
		$this->grupo = $grupo;
	} // eof setGrupo 

	
	/**
	 * Define o valor de nivel
	 * @param mixed $nivel
	 */
	public function setNivel ($nivel) {
		$this->nivel = $nivel;
	} // eof setNivel 

	
	/**
	 * Define o valor de usuarioCadastrante
	 * @param SysUsuarioBO $usuarioCadastrante
	 */
	public function setUsuarioCadastrante ($usuarioCadastrante) {
		$this->usuarioCadastrante = $usuarioCadastrante;
	} // eof setUsuarioCadastrante 

	
	/**
	 * Define o valor de dhCadastro
	 * @param mixed $dhCadastro
	 */
	public function setDhCadastro ($dhCadastro) {
		$this->dhCadastro = $dhCadastro;
	} // eof setDhCadastro 



	/**
	 * Retorna um AR de Grupo vazio, apenas com o ID preenchido.
	 *
	 * @return SysUsuarioGrupoAR
	 */
	function getAR() {
		$ar = new SysUsuarioGrupoAR();
		return $ar;
	} // eof getAR

	
	/**
	 * @return SysUsuarioGrupoAR
	 */
	function getUsuarioGrupoAR() {
		$ar = $this->getAR();
		$ar->setIdUsuario($this->getUsuario()->getID());
		$ar->setIdGrupo($this->getGrupo()->getID());
		$ar->setIdUsuarioCadastrante($this->getUsuarioCadastrante()->getID());
		$ar->setNivel($this->getNivel());
		$ar->setDhCadastro($this->getDhCadastro());
		return $ar;
	} // eof getGrupoAR

	
	/**
	 * @param SysUsuarioGrupoAR $ar
	 */
	function bind($ar) {
		$this->getUsuario()->setID($ar->getIdUsuario());
		$this->getGrupo()->setId($ar->getIdGrupo());
		$this->getUsuarioCadastrante()->setID($ar->getIdUsuarioCadastrante());
		$this->setDhCadastro($ar->getDhCadastro());
		$this->setNivel($ar->getNivel());	
	} // eof bind

	
	/**
	 * Inclui um registro de usuário.
	 *
	 */
	function insert() {
		$ar = $this->getUsuarioGrupoAR();
		parent::insert($ar);
	} // eof insert


	/**
	 * Atualiza um registro de usuário
	 *
	 */
	function update() {
		$ar = $this->getUsuarioGrupoAR();
		parent::update($ar);
	} // eof update


	/**
	 * Tenta excluir o usuário, mas se o mesmo estiver associado a algum grupo
	 * ou permissão, então desativa-o.
	 *
	 * @return boolean true=excluiu fisicamente; false=desativou logicamente
	 */
	function delete() {
//		try {
			parent::delete();
//			return true;
//		} catch (Exception $e) {
//			throw 
//			$this->load();
//			$ar = $this->getUsuarioGrupoAR();
//			parent::update($ar);
//			return false;
//		}
	} // eof delete






} //  SysUsuarioGrupoBO

?>