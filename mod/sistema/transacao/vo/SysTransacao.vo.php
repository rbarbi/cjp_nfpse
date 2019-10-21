<?php // $Rev: $ $Author: $ $Date: $//

/**
 * Representaчуo simplificada de uma Transaчуo.
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.vo.transacao
 */
class SysTransacaoVO {

	private $id;
	
	
	 /**
	  * @var mixed transacaoID 
	  */
	 private $transacaoID;

	 /**
	  * @var mixed permissaoDefault 
	  */
	 private $permissaoDefault;

	 /**
	  * @var mixed m 
	  */
	 private $m;

	 /**
	  * @var mixed u 
	  */
	 private $u;

	 /**
	  * @var mixed a 
	  */
	 private $a;

	 /**
	  * @var mixed acao 
	  */
	 private $acao;


	 /**
	  * @var mixed permissao 
	  */
	 private $permissao;
	 
	 
	 
	private $transacaoAgregadoraID;

//--------------------------------------------


	public function getID() {
		return $this->id;
	}


	/**
	 * Retorna o valor de permissao
	 * @return mixed
	 */
	public function getPermissao () {
		return $this->permissao;
	} // eof getPermissao 






	/**
	 * Retorna o valor de transacaoID
	 * @return mixed
	 */
	public function getTransacaoID () {
		return $this->transacaoID;
	} // eof getTransacaoID 

	/**
	 * Retorna o valor de permissaoDefault
	 * @return mixed
	 */
	public function getPermissaoDefault () {
		return $this->permissaoDefault;
	} // eof getPermissaoDefault 

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
	 * Retorna o valor de transacaoAgregadoraID
	 * @return mixed
	 */
	public function getTransacaoAgregadoraID () {
		return $this->transacaoAgregadoraID;
	} // eof getTransacaoAgregadoraID 



//--------------------------------------------


	public function setID($id) {
		$this->id = $id;
	}

	/**
	 * Define o valor de transacaoAgregadoraID
	 * @param mixed $transacaoAgregadoraID
	 */
	public function setTransacaoAgregadoraID ($transacaoAgregadoraID) {
		$this->transacaoAgregadoraID = $transacaoAgregadoraID;
	} // eof setTransacaoAgregadoraID 


	/**
	 * Define o valor de permissao
	 * @param mixed $permissao
	 */
	public function setPermissao ($permissao) {
		$this->permissao = $permissao;
	} // eof setPermissao 
	
	/**
	 * Define o valor de transacaoID
	 * @param mixed $transacaoID
	 */
	public function setTransacaoID ($transacaoID) {
		$this->transacaoID = $transacaoID;
	} // eof setTransacaoID 

	/**
	 * Define o valor de permissaoDefault
	 * @param mixed $permissaoDefault
	 */
	public function setPermissaoDefault ($permissaoDefault) {
		$this->permissaoDefault = $permissaoDefault;
	} // eof setPermissaoDefault 

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

	
	
	function getAlias() {
		$s = SysTransacaoBO::formataAlias($this);
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
	
} // eoc SysTransacaoVO

?>