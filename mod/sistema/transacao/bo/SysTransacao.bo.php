<?php //

/**
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package Gama3
 * @created 10-jun-2008 16:54:19
 */
class SysTransacaoBO extends BasePersistenteBO {


	/**
	  * @var mixed ID
	  */
	private $ID;

	/**
	  * @var mixed nome
	  */
	private $nome;

	/**
	  * @var mixed descricao
	  */
	private $descricao;

	/**
	  * @var string m
	  */
	private $m;

	/**
	  * @var string u
	  */
	private $u;

	/**
	  * @var string a
	  */
	private $a;

	/**
	  * @var string acao
	  */
	private $acao;

	/**
	  * @var int nivelMinimo
	  */
	private $nivelMinimo;

	/**
	  * @var char permissaoDefault
	  */
	private $permissaoDefault;

	/**
	  * @var char status
	  */
	private $status;


	/**
	  * @var int transacaoAgregadora
	  */
	private $transacaoAgregadora;


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
	 * Retorna o valor de descricao
	 * @return mixed
	 */
	public function getDescricao () {
		return $this->descricao;
	} // eof getDescricao

	/**
	 * Retorna o valor de m
	 * @return mixed
	 */
	public function getM () {
		return $this->m;
	} // eof getM

	/**
	 * Retorna o valor de u
	 * @return mixed
	 */
	public function getU () {
		return $this->u;
	} // eof getU

	/**
	 * Retorna o valor de a
	 * @return mixed
	 */
	public function getA () {
		return $this->a;
	} // eof getA

	/**
	 * Retorna o valor de acao
	 * @return mixed
	 */
	public function getAcao () {
		return $this->acao;
	} // eof getAcao

	/**
	 * Retorna o valor de nivelMinimo
	 * @return mixed
	 */
	public function getNivelMinimo () {
		return $this->nivelMinimo;
	} // eof getNivelMinimo

	/**
	 * Retorna o valor de permissaoDefault
	 * @return mixed
	 */
	public function getPermissaoDefault () {
		return $this->permissaoDefault;
	} // eof getPermissaoDefault

	/**
	 * Retorna o valor de status
	 * @return mixed
	 */
	public function getStatus () {
		return $this->status;
	} // eof getStatus


	/**
	 * Retorna o valor de transacaoAgregadora
	 * @return SysTransacaoBO
	 */
	public function getTransacaoAgregadora () {
		if (is_null($this->transacaoAgregadora)) {
			$this->transacaoAgregadora = new SysTransacaoBO();
		}
		return $this->transacaoAgregadora;
	} // eof getTransacaoAgregadora



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
	 * Define o valor de descricao
	 * @param mixed $descricao
	 */
	public function setDescricao ($descricao) {
		$this->descricao = $descricao;
	} // eof setDescricao

	/**
	 * Define o valor de m
	 * @param mixed $m
	 */
	public function setM ($m) {
		$this->m = $m;
	} // eof setM

	/**
	 * Define o valor de u
	 * @param mixed $u
	 */
	public function setU ($u) {
		$this->u = $u;
	} // eof setU

	/**
	 * Define o valor de a
	 * @param mixed $a
	 */
	public function setA ($a) {
		$this->a = $a;
	} // eof setA

	/**
	 * Define o valor de acao
	 * @param mixed $acao
	 */
	public function setAcao ($acao) {
		$this->acao = $acao;
	} // eof setAcao

	/**
	 * Define o valor de nivelMinimo
	 * @param mixed $nivelMinimo
	 */
	public function setNivelMinimo ($nivelMinimo) {
		$this->nivelMinimo = $nivelMinimo;
	} // eof setNivelMinimo

	/**
	 * Define o valor de permissaoDefault
	 * @param mixed $permissaoDefault
	 */
	public function setPermissaoDefault ($permissaoDefault) {
		$this->permissaoDefault = $permissaoDefault;
	} // eof setPermissaoDefault

	/**
	 * Define o valor de status
	 * @param mixed $status
	 */
	public function setStatus ($status) {
		$this->status = $status;
	} // eof setStatus



	/**
	 * Define o valor de transacaoAgregadora
	 * @param mixed $transacaoAgregadora
	 */
	public function setTransacaoAgregadora ($transacaoAgregadora) {
		$this->transacaoAgregadora = $transacaoAgregadora;
	} // eof setTransacaoAgregadora


	// ---------------------------------------------


	/**
	 * @return SysTransacaoAR
	 */
	function getAR() {
		$ar = new SysTransacaoAR();
		$ar->setID($this->getID());
		return $ar;
	} // eof getAR


	/**
	 * @return SysTransacaoAR
	 */
	function getTransacaoAR() {
		$ar = $this->getAR();
		$ar->setNome($this->getNome());
		$ar->setDescricao($this->getDescricao());
		$ar->setM($this->getM());
		$ar->setU($this->getU());
		$ar->setA($this->getA());
		$ar->setAcao($this->getAcao());
		$ar->setNivelMinimo($this->getNivelMinimo());
		$ar->setPermissaoDefault($this->getPermissaoDefault());
		$ar->setStatus($this->getStatus());
		return $ar;
	} // eof getTransacaoAR


	/**
	 * @param SysTransacaoAR $ar
	 */
	function bind($ar) {
		$this->setID($ar->getID());
		$this->setNome($ar->getNome());
		$this->setDescricao($ar->getDescricao());
		$this->setM($ar->getM());
		$this->setU($ar->getU());
		$this->setA($ar->getA());
		$this->setAcao($ar->getAcao());
		$this->setNivelMinimo($ar->getNivelMinimo());
		$this->setPermissaoDefault($ar->getPermissaoDefault());
		$this->setStatus($ar->getStatus());
	}

	function insert() {
		$this->setStatus(SysTransacaoBO::ST_REG_ATIVO);
		$ar = $this->getTransacaoAR();
		$ar->setID(null);
		parent::insert($ar);
		
		/*try {
			parent::insert($ar);
		} catch (SysException $e) {
			switch ($e->getCode()) {
				case BasePersistenteBO::ERRO_CAMPO_REQUERIDO: $mensagem = 'Campo requerido nao informado - ' . $e->getMessage(); break;
				case BasePersistenteBO::ERRO_CAMPO_DUPLICADO : $mensagem = 'Campo duplicado - ' . $e->getMessage(); break;
				default: $mensagem = 'Erro desconhecido na inclusao do registro - '. $e->getMessage();
			}
			$se = new SysException($mensagem,$e->getCode());
			$se->setDescricao($e->getMessage());
			throw $se;
		}*/
	} // eof insert




	function update() {
		$ar = $this->getTransacaoAR();
		parent::update($ar);
	}



	/**
	 * Retorna a representaчуo da requisiчуo, de mais fсcil compreensуo.
	 *
	 * @param array|SysTransacaoBO $obj
	 * @return string
	 */
	public function formataAlias($obj=null) {
		if (is_null($obj)) {
			$s = "/{$this->getM()}/{$this->getU()}/{$this->getA()}/{$this->getAcao()}";
		} else if (is_array($obj)) {
			list($m,$u,$a,$acao) = $obj;
			$s = "/$m/$u/$a/$acao";
		} else if (is_object($obj)) {
			$s = "/{$obj->getM()}/{$obj->getU()}/{$obj->getA()}/{$obj->getAcao()}";
		} else {
			$obj = MainGama::getApp();
			$s = "/{$obj->getM()}/{$obj->getU()}/{$obj->getA()}/{$obj->getAcao()}";
		}
		return $s;
	} // eof formataAlias


	
	public function getListaTodasTransacoesAtivas() {
		$lista = array();

		$q = new DBQuery();
		$q->addTable('tb_sys_transacao');
		$q->addWhere("tr_status = 'A'");
		$sql = $q->prepareSelect();
		$lista = array();

		$res = MainGama::getApp()->getCon()->GetArray($sql);
		foreach ($res as $registro) {
			$bo = new SysTransacaoBO();
			$bo->setID($registro['tr_id']);
			$bo->getTransacaoAgregadora()->setID($registro['tr_tr_agregadora']);
			$bo->setNome($registro['tr_nome']);
			$bo->setDescricao($registro['tr_descricao']);
			$bo->setNivelMinimo($registro['tr_nivel_min_usuario']);
			$bo->setM($registro['tr_m']);
			$bo->setU($registro['tr_u']);
			$bo->setA($registro['tr_a']);
			$bo->setAcao($registro['tr_acao']);
			$bo->setPermissaoDefault($registro['tr_permissao_default']);
			$bo->setStatus($registro['tr_status']);
			$lista[$bo->getID()] = $bo;
		}


		return $lista;
	}

	function getAlias() {
		$s = $this->formataAlias($this);
		return $s;
	}


	/**
	 * Monta uma representaчуo do objeto 
	 * que pode ser impressa.
	 *
	 * @return string
	 */
	function __tostring() {
		return $this->getAlias();
	}


} // eoc SysTransacaoBO


?>