<?php // $Rev: $ $Author: $ $Date: $//

/**
 * Realiza as operações de acesso aos dados dos registros de
 * grupos de usuários cadastrados no sistema.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package Gama3
 */
class SysUsuarioGrupoAR extends BaseAR {


	 /**
	  * @var mixed ug_usu_id 
	  */
	 public $ug_usu_id;

	 /**
	  * @var mixed ug_gu_id 
	  */
	 public $ug_gu_id;

	 /**
	  * @var mixed ug_nivel 
	  */
	 public $ug_nivel;

	 /**
	  * @var mixed ug_usu_resp_cad_id 
	  */
	 public $ug_usu_resp_cad_id;

	 /**
	  * @var mixed ug_dh_cadastro 
	  */
	 public $ug_dh_cadastro;

//--------------------------------------------

	/**
	 * Retorna o valor de ug_usu_id
	 * @return mixed
	 */
	public function getIdUsuario () {
		return $this->ug_usu_id;
	} // eof getIdUsuario 

	/**
	 * Retorna o valor de ug_gu_id
	 * @return mixed
	 */
	public function getIdGrupo () {
		return $this->ug_gu_id;
	} // eof getIdGrupo 

	/**
	 * Retorna o valor de ug_nivel
	 * @return mixed
	 */
	public function getNivel () {
		return $this->ug_nivel;
	} // eof getNivel 

	/**
	 * Retorna o valor de ug_usu_resp_cad_id
	 * @return mixed
	 */
	public function getIdUsuarioCadastrante () {
		return $this->ug_usu_resp_cad_id;
	} // eof getIdUsuarioCadastrante 

	/**
	 * Retorna o valor de ug_dh_cadastro
	 * @return mixed
	 */
	public function getDhCadastro () {
		return $this->ug_dh_cadastro;
	} // eof getDhCadastro 



//--------------------------------------------

	/**
	 * Define o valor de ug_usu_id
	 * @param mixed $ug_usu_id
	 */
	public function setIdUsuario ($ug_usu_id) {
		$this->ug_usu_id = $ug_usu_id;
	} // eof setIdUsuario 

	/**
	 * Define o valor de ug_gu_id
	 * @param mixed $ug_gu_id
	 */
	public function setIdGrupo ($ug_gu_id) {
		$this->ug_gu_id = $ug_gu_id;
	} // eof setIdGrupo 

	/**
	 * Define o valor de ug_nivel
	 * @param mixed $ug_nivel
	 */
	public function setNivel ($ug_nivel) {
		$this->ug_nivel = $ug_nivel;
	} // eof setNivel 

	/**
	 * Define o valor de ug_usu_resp_cad_id
	 * @param mixed $ug_usu_resp_cad_id
	 */
	public function setIdUsuarioCadastrante ($ug_usu_resp_cad_id) {
		$this->ug_usu_resp_cad_id = $ug_usu_resp_cad_id;
	} // eof setIdUsuarioCadastrante 

	/**
	 * Define o valor de ug_dh_cadastro
	 * @param mixed $ug_dh_cadastro
	 */
	public function setDhCadastro ($ug_dh_cadastro) {
		$this->ug_dh_cadastro = $ug_dh_cadastro;
	} // eof setDhCadastro 

	

	

	function SysUsuarioGrupoAR() {
		BaseAR::init('tb_sys_usuario_grupo',array('ug_usu_id','ug_gu_id'));
//		$this->setOID('gu_id','tb_sys_grupo_usuarios_gu_id_seq');
	}// eof SysUsuarioGrupoAR


} // eoc SysUsuarioGrupoAR



?>