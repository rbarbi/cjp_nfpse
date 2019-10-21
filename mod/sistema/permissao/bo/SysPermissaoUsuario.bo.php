<?php // $Rev: 152 $ - $Author: eduluz $ $Date: 2008-09-26 17:38:47 -0300 (Fri, 26 Sep 2008) $//

/**
 * Enter description here...
 *
 */
class SysPermissaoUsuarioBO extends SysPermissaoBO {


	 /**
	  * @var SysUsuarioBO usuario
	  */
	 private $usuario;


	 const TIPO = 'U';

//--------------------------------------------

	/**
	 * Retorna o valor de usuario
	 * @return SysUsuarioBO
	 */
	public function getUsuario () {
		if (is_null($this->usuario)) {
			$this->usuario = new SysUsuarioBO($this->getApp());
		}
		return $this->usuario;
	} // eof getUsuario


//--------------------------------------------

	/**
	 * Define o valor de usuario
	 * @param SysUsuarioBO $usuario
	 */
	public function setUsuario ($usuario) {
		$this->usuario = $usuario;
	} // eof setUsuario



//
//	function SysPermissaoUsuarioBO($app) {
//		$this->SysPermissaoBO($app);
//	}


	function getAR() {
		$ar = new SysPermissaoUsuarioAR();
		$ar->setID($this->getID());
		return $ar;
	}


	/**
	 * Insere um registro.
	 */
	function insert() {
		$bo = new SysPermissaoBO();
		$bo->setTransacao($this->getTransacao());
		$bo->setPermissao($this->getPermissao());
		$bo->setTipo(SysPermissaoUsuarioBO::TIPO);
		$bo->insert($bo->getPermissaoAR());
		$this->setID($bo->getID());
		parent::insert($this->getSysPermissaoUsuarioAR());
	} // eof insert


	function delete() {

		parent::delete();

		$bo = new SysPermissaoBO();
		$bo->setID($this->getID());
		$bo->delete();
	}


	function getSysPermissaoUsuarioAR() {
		$ar = new SysPermissaoUsuarioAR();
		$ar->setID($this->getID());
		$ar->setUsuario($this->getUsuario()->getID());
		return $ar;
	} // eof getSysPermissaoUsuarioAR


	function preInsert() {
		$this->verificaPermissaoDuplicada();
	}


	function verificaPermissaoDuplicada() {
		$sql = "SELECT COUNT(*) AS contador
				FROM tb_sys_permissao pe, tb_sys_permissao_usuario pu
				WHERE pe.pe_id = pu.pu_id
			      AND pe.pe_tr_id = ?
			      AND pu.pu_usu_id = ?";
		$resposta = $this->getCon()->GetArray($sql,array($this->getTransacao()->getID(),$this->getUsuario()->getID()));
		if ($resposta[0]['contador'] > 0) {
			$se = new SysException('Permissão já cadastrada',99);
			throw $se;
		} else {
			return true;
		}
	} // eof verificaPermissaoDuplicada






} // eoc SysPermissaoUsuarioBO


?>