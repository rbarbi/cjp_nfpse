<?php // $Rev: $ $Author: $ $Date: $//

/**
 * Classe que serve de interface entre a camada de controle/persistência e a estrutura 
 * de banco de dados.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.permissao
 */
class SysPermissaoGrupoAR extends BaseAR  {


	 /**
	  * @var mixed pg_id
	  */
	 public $pg_id;

	 /**
	  * @var mixed pg_gu_id
	  */
	 public $pg_gu_id;


//--------------------------------------------

	/**
	 * Retorna o valor de pg_id
	 * @return mixed
	 */
	public function getID () {
		return $this->pg_id;
	} // eof getID

	/**
	 * Retorna o valor de pu_usu_id
	 * @return mixed
	 */
	public function getGrupo () {
		return $this->pg_gu_id;
	} // eof getGrupo


//--------------------------------------------

	/**
	 * Define o valor de pg_id
	 * @param int $pg_id
	 */
	public function setID ($pg_id) {
		$this->pg_id = $pg_id;
	} // eof setID

	/**
	 * Define o valor de pu_usu_id
	 * @param int $pg_gu_id
	 */
	public function setGrupo ($pg_gu_id) {
		$this->pg_gu_id = $pg_gu_id;
	} // eof setGrupo


	public function SysPermissaoGrupoAR($idConn=false) {
		BaseAR::init('tb_sys_permissao_grupo',array('pg_id'),$idConn);
	} // eof SysPermissaoGrupoAR


//	public function insert() {
//		$ar = $this->getPermissaoAR($this);
//		$ar->insert();
//		$this->setID($ar->getID());
//		parent::insert();
//	}

} // eoc SysPermissaoGrupoAR


?>