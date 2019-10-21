<?php 
// $Rev: 119 $ - $Author: eduluz $ $Date: 2008-09-05 18:05:00 -0300 (Fri, 05 Sep 2008) $//

class SysUsuarioVO  {


	 /**
	  * @var mixed ID
	  */
	 private $ID;

	 /**
	  * @required
	  * @var mixed nome
	  */
	 private $nome;

	 /**
	  * @var mixed username
	  */
	 private $username;

	 /**
	  * @var mixed nivel
	  */
	 private $nivel;

	 /**
	  * @var mixed senha
	  */
	 private $senha;

	 /**
	  * @var mixed status
	  */
	 private $status;

//--------------------------------------------

	/**
	 * Retorna o valor de ID
	 * @return mixed
	 */
	public function getID () {
		return $this->ID;
	} // eof getID

	/**
	 * Retorna o valor de nome
	 * @return mixed
	 */
	public function getNome () {
		return $this->nome;
	} // eof getNome

	/**
	 * Retorna o valor de username
	 * @return mixed
	 */
	public function getUsername () {
		return $this->username;
	} // eof getUsername

	/**
	 * Retorna o valor de nivel
	 * @return mixed
	 */
	public function getNivel () {
		return $this->nivel;
	} // eof getNivel

	/**
	 * Retorna o valor de senha
	 * @return mixed
	 */
	public function getSenha () {
		return $this->senha;
	} // eof getSenha

	/**
	 * Retorna o valor de status
	 * @return mixed
	 */
	public function getStatus () {
		return $this->status;
	} // eof getStatus



//--------------------------------------------

	/**
	 * Define o valor de ID
	 * @param mixed $ID
	 */
	public function setID ($ID) {
		$this->ID = $ID;
	} // eof setID

	/**
	 * Define o valor de nome
	 * @param mixed $nome
	 */
	public function setNome ($nome) {
		$this->nome = $nome;
	} // eof setNome

	/**
	 * Define o valor de username
	 * @param mixed $username
	 */
	public function setUsername ($username) {
		$this->username = $username;
	} // eof setUsername

	/**
	 * Define o valor de nivel
	 * @param mixed $nivel
	 */
	public function setNivel ($nivel) {
		$this->nivel = $nivel;
	} // eof setNivel

	/**
	 * Define o valor de senha
	 * @param mixed $senha
	 */
	public function setSenha ($senha) {
		$this->senha = $senha;
	} // eof setSenha

	/**
	 * Define o valor de status
	 * @param mixed $status
	 */
	public function setStatus ($status) {
		$this->status = $status;
	} // eof setStatus



}

?>