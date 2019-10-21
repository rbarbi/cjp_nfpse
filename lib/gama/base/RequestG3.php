<?php // $Rev: 210 $ $Author: eduluz $ $Date: 2008-10-27 15:51:53 -0200 (seg, 27 out 2008) $//

/**
 * Classe que representa uma requisiзгo.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.main
 */
class RequestG3 {
	
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

//--------------------------------------------

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



//--------------------------------------------

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
	 * Recupera os dados da ъltima requsiзгo na representaзгo de um Array.
	 */
	public function getArray() {
		return array(	'm' => $this->getM(),
						'u' => $this->getU(),
						'a' => $this->getA(),
						'acao' => $this->getAcao());
	} // eof getArray

} // eoc  RequestG3


?>