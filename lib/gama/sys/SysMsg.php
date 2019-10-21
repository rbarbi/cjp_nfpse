<?php // $Rev: 84 $ - $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $

/**
 * Classe que gerencia mensagens trocadas entre as camadas das aplicacoes.
 *
 * @author Eduardo S. da Luz
 * @copyright Iasoft consultoria e sistemas LTDA
 * @package gama3.utils.msg
 */
class SysMsg {

	protected $codigo;
	protected $nivel;
	protected $msg;


	/**
	 * Construtor da classe SysMsg.
	 *
	 * @param integer $codigo
	 * @param string $msg
	 * @param Msg
	 */
	function SysMsg($codigo,$msg,$nivel=10) {
		$this->setCodigo($codigo);
		$this->setMsg($msg);
		$this->setNivel($nivel);
	} // eof SysMsg



	/**
	 * Retorna o valor de codigo
	 * @return undefined
	 */
	public function getCodigo () {
		return $this->codigo;
	} // eof getCodigo

	/**
	 * Retorna o valor de nivel
	 * @return undefined
	 */
	public function getNivel () {
		return $this->nivel;
	} // eof getNivel

	/**
	 * Retorna o valor de msg
	 * @return undefined
	 */
	public function getMsg () {
		return $this->msg;
	} // eof getMsg

	/**
	 * Define o valor de codigo
	 * @param undefined $codigo
	 */
	public function setCodigo ($codigo) {
		$this->codigo = $codigo;
	} // eof setCodigo

	/**
	 * Define o valor de nivel
	 * @param undefined $nivel
	 */
	public function setNivel ($nivel) {
		$this->nivel = $nivel;
	} // eof setNivel

	/**
	 * Define o valor de msg
	 * @param undefined $msg
	 */
	public function setMsg ($msg) {
		$this->msg = $msg;
	} // eof setMsg

} // eoc SysMsg


?>