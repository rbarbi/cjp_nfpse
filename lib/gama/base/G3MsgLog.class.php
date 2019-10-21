<?php // $Rev: 268 $ $Author: eduluz $ $Date: 2008-11-28 11:14:34 -0200 (sex, 28 nov 2008) $//


class G3MsgLog {

	/**
	  * @var mixed dhEvento 
	  */
	private $dhEvento;

	/**
	  * @var mixed mensagem 
	  */
	private $mensagem;

	/**
	  * @var mixed trace 
	  */
	private $trace;

	/**
	  * @var mixed origem 
	  */
	private $origem;

	//--------------------------------------------

	/**
	 * Retorna o valor de dhEvento
	 * @return mixed
	 */
	public function getDhEvento () {
		return $this->dhEvento;
	} // eof getDhEvento

	/**
	 * Retorna o valor de mensagem
	 * @return mixed
	 */
	public function getMensagem () {
		return $this->mensagem;
	} // eof getMensagem


	/**
	 * Retorna o valor de origem
	 * @return mixed
	 */
	public function getTrace () {
		return $this->origem;
	} // eof getOrigem



	//--------------------------------------------

	/**
	 * Define o valor de dhEvento
	 * @param mixed $dhEvento
	 */
	public function setDhEvento ($dhEvento) {
		$this->dhEvento = $dhEvento;
	} // eof setDhEvento

	/**
	 * Define o valor de mensagem
	 * @param mixed $mensagem
	 */
	public function setMensagem ($mensagem) {
		$this->mensagem = $mensagem;
	} // eof setMensagem


	/**
	 * Define o valor de origem
	 * @param mixed $origem
	 */
	public function setTrace ($origem) {
		$this->origem = $origem;
	} // eof setOrigem

	public function __construct() {
		ob_start();
		debug_print_backtrace();
		$trace = ob_get_contents();
		ob_end_clean();
		$this->setDhEvento(date('d/m/Y h:i:s'));		
		$this->setTrace($trace);
	}
	
	

}

?>