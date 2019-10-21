<?php // $Rev: 273 $ - $Author: eduluz $ $Date: 2008-12-05 17:22:10 -0200 (Fri, 05 Dec 2008) $//

/**
 * Classe onde é feito o mapeamento com a tabela no banco de dados.
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.permissao.persistencia
 */
class SysPermissaoAR extends BaseAR  {

	/**
	  * @var mixed pe_id
	  */
	public $pe_id;

	/**
	  * @var mixed pe_tr_id
	  */
	public $pe_tr_id;

	/**
	  * @var mixed pe_permissao
	  */
	public $pe_permissao;

	/**
	  * @var mixed pe_status
	  */
	public $pe_status;



	/**
	  * @var mixed pe_tipo
	  */
	public $pe_tipo;


	//--------------------------------------------

	/**
	 * Retorna o valor de pe_id
	 * @return mixed
	 */
	public function getID () {
		return $this->pe_id;
	} // eof getID


	/**
	 * Retorna o valor de pe_tr_id
	 * @return mixed
	 */
	public function getTransacao () {
		return $this->pe_tr_id;
	} // eof getTransacao

	/**
	 * Retorna o valor de pe_permissao
	 * @return mixed
	 */
	public function getPermissao () {
		return $this->pe_permissao;
	} // eof getPermissao

	/**
	 * Retorna o valor de pe_status
	 * @return mixed
	 */
	public function getStatus () {
		return $this->pe_status;
	} // eof getStatus


	/**
	 * Retorna o valor de pe_tipo
	 * @return mixed
	 */
	public function getTipo () {
		return $this->pe_tipo;
	} // eof getTipo



	//--------------------------------------------

	/**
	 * Define o valor de pe_id
	 * @param mixed $pe_id
	 */
	public function setID ($pe_id) {
		$this->pe_id = $pe_id;
	} // eof setID


	/**
	 * Define o valor de pe_tr_id
	 * @param mixed $pe_tr_id
	 */
	public function setTransacao ($pe_tr_id) {
		$this->pe_tr_id = $pe_tr_id;
	} // eof setTransacao

	/**
	 * Define o valor de pe_permissao
	 * @param mixed $pe_permissao
	 */
	public function setPermissao ($pe_permissao) {
		$this->pe_permissao = $pe_permissao;
	} // eof setPermissao

	/**
	 * Define o valor de pe_status
	 * @param mixed $pe_status
	 */
	public function setStatus ($pe_status) {
		$this->pe_status = $pe_status;
	} // eof setStatus


	/**
	 * Define o valor de pe_tipo
	 * @param mixed $pe_tipo
	 */
	public function setTipo ($pe_tipo) {
		$this->pe_tipo = $pe_tipo;
	} // eof setTipo




	/**
	 * Construtor da classe.
	 *
	 * @param string $idConn
	 * @return SysPermissaoAR
	 */
	public function SysPermissaoAR($idConn=false) {
		BaseAR::init('tb_sys_permissao',array('pe_id'),$idConn=false);
		$this->setOID('pe_id','tb_sys_permissao_pe_id_seq');
	} // eof SysPermissaoUsuarioAR


	/**
	 * @param SysPermissaoAR $ar
	 * @return SysPermissaoAR
	 */
	function getPermissaoAR($ar = null) {
		$novoAR = new SysPermissaoAR();
		if (!is_null($ar)) {
			$novoAR->setID($ar->getID());
			$novoAR->setPermissao($ar->getPermissao());
			$novoAR->setStatus($ar->getStatus());
			$novoAR->setTransacao($ar->getTransacao());
			$novoAR->setTipo($ar->getTipo());
		}
		return $novoAR;
	}

	/**
	 * Para incluir um
	 *
	 * @param unknown_type $byPass
	 */
	public function insert($byPass=false) {
			parent::insert();
			$this->setID($this->LastInsertID());
	}

} // eoc SysPermissaoUsuarioAR


?>