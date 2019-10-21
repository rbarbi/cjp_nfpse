<?php // $Rev: 244 $ - $Author: eduluz $ $Date: 2008-11-19 11:20:08 -0200 (Wed, 19 Nov 2008) $//

/**
 * Classe que agrupa as regras de negѓcio referente a permissѕes
 * no sistema. Щ responsсvel tambщm pela delegaчуo das operaчѕes
 * de persistъncia/ atualizaчуo e recuperaчуo de uma instтncia
 * especэfica.
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.permissao.controle
 */
class SysPermissaoBO extends BasePersistenteBO {


	/**
	  * @var mixed ID
	  */
	private $ID;


	/**
	  * @var mixed transacao
	  */
	private $transacao;


	/**
	  * @var mixed permissao
	  */
	private $permissao;



	 /**
	  * @var mixed tipo
	  */
	 private $tipo;


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
	 * Retorna o valor de transacao
	 * @return SysTransacaoBO
	 */
	public function getTransacao () {
		if (is_null($this->transacao)) {
			$this->transacao = new SysTransacaoBO($this->getApp());
		}
		return $this->transacao;
	} // eof getTransacao


	/**
	 * Retorna o valor de permissao
	 * @return mixed
	 */
	public function getPermissao () {
		return $this->permissao;
	} // eof getPermissao



	/**
	 * Retorna o valor de tipo
	 * @return mixed
	 */
	public function getTipo () {
		return $this->tipo;
	} // eof getTipo



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
	 * Define o valor de transacao
	 * @param mixed $transacao
	 */
	public function setTransacao ($transacao) {
		$this->transacao = $transacao;
	} // eof setTransacao

	/**
	 * Define o valor de permissao
	 * @param mixed $permissao
	 */
	public function setPermissao ($permissao) {
		$this->permissao = $permissao;
	} // eof setPermissao

	const PERM_CONCEDIDA = 'S';
	const PERM_NEGADA    = 'N';
	const PERM_DEFAULT   = 'D';

	/**
	 * Define o valor de tipo
	 * @param mixed $tipo
	 */
	public function setTipo ($tipo) {
		$this->tipo = $tipo;
	} // eof setTipo



	/**
	 * Define o valor de status
	 * @param mixed $status
	 */
	public function setStatus ($status) {
		$this->status = $status;
	} // eof setStatus 
	
	

	/**
	 * Retorna a lista de tipos de permissѕes disponэveis
	 *
	 * @param string $padrao
	 * @return ArrayAssociativo
	 */
	public function getArrayPermissoesDisponiveis($padrao=SysPermissaoBO::PERM_DEFAULT) {
		$lista = new ArrayAssociativo();
		$lista->addItem(SysPermissaoBO::PERM_CONCEDIDA ,'Autorizado');
		$lista->addItem(SysPermissaoBO::PERM_DEFAULT  ,'Default');
		$lista->addItem(SysPermissaoBO::PERM_NEGADA ,'Desautorizado');
		$lista->setChave($padrao);
		return $lista;
	} // eof getArrayPermissoesDisponiveis


	/**
	 * Recupera uma instтncia de SysPermissaoAR vazia.
	 *
	 * @return SysPermissaoAR
	 */
	function getAR () {
		$ar = new SysPermissaoAR();
		$ar->setID($this->getID());
		return $ar;
	} // eof getAR



	function getPermissaoAR() {
		$ar = $this->getAR();
		$ar->setTransacao($this->getTransacao()->getID());
		$ar->setPermissao($this->getPermissao());
		$ar->setStatus(BasePersistenteBO::ST_REG_ATIVO);
		$ar->setTipo($this->getTipo());
		return $ar;
	} // eof getPermissaoAR

	
	/**
	 * @param SysPermissaoAR $ar
	 */
	function bind($ar) {
		$this->setID($ar->getID());
		$this->setPermissao($ar->getPermissao());
		$this->setTipo($ar->getTipo());
		$this->getTransacao()->getID();
	} // eof bind
	
	
	
	public function getListaTodasPermissoesUsuario($usuID) {
		$lista = array();
		
		$q = new DBQuery();
		$q->addTable('tb_sys_permissao','pe');
		$q->addTable('tb_sys_permissao_usuario','pu');
		
		$q->addWhere("pe.pe_status = 'A'");
		$q->addWhere("pe.pe_id = pu.pu_id");
		$q->addWhere("pu.pu_usu_id = ?");
		$sql = $q->prepareSelect();
		$lista = array();
		
		$res = MainGama::getApp()->getCon()->GetArray($sql,array($usuID));
		foreach ($res as $registro) {
			$bo = new SysPermissaoBO();

			$bo->setID($registro['pe_id']);
			$bo->setPermissao($registro['pe_permissao']);
			$bo->setTipo($registro['pe_tipo']);
			$bo->getTransacao()->setID($registro['pe_tr_id']);
			$bo->setStatus($registro['pe_status']);
			$lista[$bo->getID()] = $bo;
		}
		
		return $lista;
			
	}

} // eoc SysPermissaoBO

?>