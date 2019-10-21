<?php

class SysPermissaoUsuarioAR extends BaseAR  {


	 /**
	  * @var mixed pu_id
	  */
	 public $pu_id;

	 /**
	  * @var mixed pu_usu_id
	  */
	 public $pu_usu_id;


//--------------------------------------------

	/**
	 * Retorna o valor de pu_id
	 * @return mixed
	 */
	public function getID () {
		return $this->pu_id;
	} // eof getID

	/**
	 * Retorna o valor de pu_usu_id
	 * @return mixed
	 */
	public function getUsuario () {
		return $this->pu_usu_id;
	} // eof getUsuario


//--------------------------------------------

	/**
	 * Define o valor de pu_id
	 * @param mixed $pu_id
	 */
	public function setID ($pu_id) {
		$this->pu_id = $pu_id;
	} // eof setID

	/**
	 * Define o valor de pu_usu_id
	 * @param mixed $pu_usu_id
	 */
	public function setUsuario ($pu_usu_id) {
		$this->pu_usu_id = $pu_usu_id;
	} // eof setUsuario


	public function SysPermissaoUsuarioAR($idConn=false) {
		BaseAR::init('tb_sys_permissao_usuario',array('pu_id'),$idConn);
	} // eof SysPermissaoUsuarioAR


//	public function insert() {
//		$ar = $this->getPermissaoAR($this);
//		$ar->insert();
//		$this->setID($ar->getID());
//		parent::insert();
//	}

} // eoc SysPermissaoUsuarioAR


?>