<?php // $Rev: 152 $ - $Author: eduluz $ $Date: 2008-09-26 17:38:47 -0300 (sex, 26 set 2008) $//

/**
 * Enter description here...
 *
 */
class SysPermissaoGrupoBO extends SysPermissaoBO {


	 /**
	  * @var SysGrupoBO grupo
	  */
	 private $grupo;


	 const TIPO = 'G';

//--------------------------------------------

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


//--------------------------------------------

	/**
	 * Define o valor de grupo
	 * @param SysGrupoBO $grupo
	 */
	public function setGrupo ($grupo) {
		$this->grupo = $grupo;
	} // eof setGrupo





	function getAR() {
		$ar = new SysPermissaoGrupoAR();
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
		$bo->setTipo(SysPermissaoGrupoBO::TIPO);
		$bo->insert($bo->getPermissaoAR());
		$this->setID($bo->getID());
		parent::insert($this->getSysPermissaoGrupoAR());
	} // eof insert


	function delete() {

		parent::delete();

		$bo = new SysPermissaoBO();
		$bo->setID($this->getID());
		$bo->delete();
	}


	function getSysPermissaoGrupoAR() {
		$ar = new SysPermissaoGrupoAR();
		$ar->setID($this->getID());
		$ar->setGrupo($this->getGrupo()->getID());
		return $ar;
	} // eof getSysPermissaoGrupoAR


	function preInsert() {
		$this->verificaPermissaoDuplicada();
	}


	function verificaPermissaoDuplicada() {
		$sql = "SELECT COUNT(*) AS contador
				FROM tb_sys_permissao pe, tb_sys_permissao_grupo pg
				WHERE pe.pe_id = pg.pg_id
			      AND pe.pe_tr_id = ?
			      AND pg.pg_gu_id = ?";
		$resposta = $this->getCon()->GetArray($sql,array($this->getTransacao()->getID(),$this->getGrupo()->getID()));
		if ($resposta[0]['contador'] > 0) {
			$se = new SysException('Permissão já cadastrada',99);
			throw $se;
		} else {
			return true;
		}
	} // eof verificaPermissaoDuplicada






} // eoc SysPermissaoGrupoBO


?>