<?php // $Rev: $ $Author: $ $Date: $//

/**
 * Realiza as operações de acesso aos dados dos registros de
 * grupos de usuários cadastrados no sistema.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package Gama3
 */
class SysGrupoAR extends BaseAR {


	 /**
	  * @var mixed gu_id 
	  */
	 public $gu_id;

	 /**
	  * @var mixed gu_nome 
	  */
	 public $gu_nome;

	 /**
	  * @var mixed gu_descricao 
	  */
	 public $gu_descricao;

	 /**
	  * @var mixed gu_usuario_admin_id 
	  */
	 public $gu_usuario_admin_id;

	 /**
	  * @var mixed gu_status_registro 
	  */
	 public $gu_status_registro;

//--------------------------------------------

	/**
	 * Retorna o valor de gu_id
	 * @return mixed
	 */
	public function getID () {
		return $this->gu_id;
	} // eof getID 

	/**
	 * Retorna o valor de gu_nome
	 * @return mixed
	 */
	public function getNome () {
		return $this->gu_nome;
	} // eof getNome 

	/**
	 * Retorna o valor de gu_descricao
	 * @return mixed
	 */
	public function getDescricao () {
		return $this->gu_descricao;
	} // eof getDescricao 

	/**
	 * Retorna o valor de gu_usuario_admin_id
	 * @return mixed
	 */
	public function getIdUsuarioAdmin () {
		return $this->gu_usuario_admin_id;
	} // eof getIdUsuarioAdmin 

	/**
	 * Retorna o valor de gu_status_registro
	 * @return mixed
	 */
	public function getStatus () {
		return $this->gu_status_registro;
	} // eof getStatus 



//--------------------------------------------

	/**
	 * Define o valor de gu_id
	 * @param mixed $gu_id
	 */
	public function setID ($gu_id) {
		$this->gu_id = $gu_id;
	} // eof setID 

	/**
	 * Define o valor de gu_nome
	 * @param mixed $gu_nome
	 */
	public function setNome ($gu_nome) {
		$this->gu_nome = $gu_nome;
	} // eof setNome 

	/**
	 * Define o valor de gu_descricao
	 * @param mixed $gu_descricao
	 */
	public function setDescricao ($gu_descricao) {
		$this->gu_descricao = $gu_descricao;
	} // eof setDescricao 

	/**
	 * Define o valor de gu_usuario_admin_id
	 * @param mixed $gu_usuario_admin_id
	 */
	public function setIdUsuarioAdmin ($gu_usuario_admin_id) {
		$this->gu_usuario_admin_id = $gu_usuario_admin_id;
	} // eof setIdUsuarioAdmin 

	/**
	 * Define o valor de gu_status_registro
	 * @param mixed $gu_status_registro
	 */
	public function setStatus ($gu_status_registro) {
		$this->gu_status_registro = $gu_status_registro;
	} // eof setStatus 

	

	/**
	 * Construtor da classe
	 *
	 * @param string $idConn
	 * @return SysGrupoAR
	 */
	function SysGrupoAR($idConn=false) {
		BaseAR::init('tb_sys_grupo_usuarios',array('gu_id'),$idConn);
		$this->setOID('gu_id','tb_sys_grupo_usuarios_gu_id_seq');
	}// eof SysGrupoAR


} // eoc SysGrupoAR



?>