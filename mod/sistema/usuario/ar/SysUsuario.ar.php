<?php //

/**
 * Realiza as operações de acesso aos dados dos registros de
 * usuários cadastrados no sistema.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package Gama3
 */
class SysUsuarioAR extends BaseAR {

	 /**
	  * @var mixed usu_id
	  */
	 public $usu_id;

	 /**
	  * @var mixed usu_nome
	  */
	 public $usu_nome;

	 /**
	  * @var mixed usu_username
	  */
	 public $usu_username;

	 /**
	  * @var mixed usu_senha
	  */
	 public $usu_senha;

	 /**
	  * @var mixed usu_nivel
	  */
	 public $usu_nivel;

	 /**
	  * @var mixed usu_status
	  */
	 public $usu_status;

//--------------------------------------------

	/**
	 * Retorna o valor de usu_id
	 * @return mixed
	 */
	public function getID () {
		return $this->usu_id;
	} // eof getID

	/**
	 * Retorna o valor de usu_nome
	 * @return mixed
	 */
	public function getNome () {
		return $this->usu_nome;
	} // eof getNome

	/**
	 * Retorna o valor de usu_username
	 * @return mixed
	 */
	public function getUsername () {
		return $this->usu_username;
	} // eof getUsername

	/**
	 * Retorna o valor de usu_senha
	 * @return mixed
	 */
	public function getSenha () {
		return $this->usu_senha;
	} // eof getSenha

	/**
	 * Retorna o valor de usu_nivel
	 * @return mixed
	 */
	public function getNivel () {
		return $this->usu_nivel;
	} // eof getNivel

	/**
	 * Retorna o valor de usu_status
	 * @return mixed
	 */
	public function getStatus () {
		return $this->usu_status;
	} // eof getStatus



//--------------------------------------------

	/**
	 * Define o valor de usu_id
	 * @param mixed $usu_id
	 */
	public function setID ($usu_id) {
		$this->usu_id = $usu_id;
	} // eof setID

	/**
	 * Define o valor de usu_nome
	 * @param mixed $usu_nome
	 */
	public function setNome ($usu_nome) {
		$this->usu_nome = $usu_nome;
	} // eof setNome

	/**
	 * Define o valor de usu_username
	 * @param mixed $usu_username
	 */
	public function setUsername ($usu_username) {
		$this->usu_username = $usu_username;
	} // eof setUsername

	/**
	 * Define o valor de usu_senha
	 * @param mixed $usu_senha
	 */
	public function setSenha ($usu_senha) {
		$this->usu_senha = $usu_senha;
	} // eof setSenha

	/**
	 * Define o valor de usu_nivel
	 * @param mixed $usu_nivel
	 */
	public function setNivel ($usu_nivel) {
		$this->usu_nivel = $usu_nivel;
	} // eof setNivel

	/**
	 * Define o valor de usu_status
	 * @param mixed $usu_status
	 */
	public function setStatus ($usu_status) {
		$this->usu_status = $usu_status;
	} // eof setStatus


	function __construct() {
		BaseAR::init('tb_sys_usuario',array('usu_id'));
		$this->setOID('usu_id','tb_sys_usuario_usu_id_seq');
	}// eof SysUsuarioAR


} // eoc SysUsuarioAR