<?php // $Rev: 82 $ - $Author: eduluz $ $Date: 2008-09-01 13:22:45 -0300 (seg, 01 set 2008) $


/**
 * Serviзo disponнvel no sistema, que corresponde
 * a um formulбrio ou funcionalidade (aзгo), que
 * pode estar em um item de menu.
 *
 * @author Eduardo S. da Luz
 * @package Gama3
 * @created 2008-06-10
 * @copyright IASoft Desenvolvimento de Sistemas
 */
class SysTransacaoAR extends BaseAR {

	/**
	 * Identificador ъnico do objeto - auto-incremental.
	 *
	 * @var integer
	 */
	public $tr_id;

	/**
	 * Nome simbуlico da transaзгo. Usada para label
	 * de menus.
	 *
	 * @var string
	 */
	public $tr_nome;


	/**
	 * Descriзгo simplificada, usada para tooltips.
	 *
	 * @var string
	 */
	public $tr_descricao;

	/**
	 * Mуdulo onde se encontra a transaзгo.
	 *
	 * @var string
	 */
	public $tr_m;

	/**
	 * Submуdulo da transaзгo.
	 *
	 * @var string
	 */
	public $tr_u;

	/**
	 * Action que responde pela transaзгo.
	 *
	 * @var string
	 */
	public $tr_a;

	/**
	 * Identificador do nome do serviзo solicitado,
	 * mapeado dentro do Action correspondente.
	 *
	 * @var string
	 */
	public $tr_acao;

	/**
	 * Menor nнvel necessбrio para se permitir a
	 * execuзгo da transaзгo.
	 *
	 * @var integer
	 */
	public $tr_nivel_min_usuario;


	/**
	 * Caracter que indica o comportamento padrгo do
	 * acesso a esta transaзгo: '+' = normalmente
	 * aberto; '-' = normalmente fechado;
	 *
	 * Quando associar um usuбrio a uma transaзгo,
	 * sem informar o tipo de permissгo (aberta ou
	 * fechada) , prevalece a da transaзгo.
	 *
	 * @var char
	 */
	public $tr_permissao_default;


	/**
	 * Indicador de validade do registro.
	 *
	 * @var char
	 */
	public $tr_status;


//--------------------------------------------

	/**
	 * Construtor da classe SysTransacaoAR.
	 *
	 * @return SysTransacaoAR
	 */
	function SysTransacaoAR() {
		$this->init('tb_sys_transacao',array('tr_id'));
	} // eof SysTransacaoAR

//--------------------------------------------


	/**
	 * Retorna o valor de tr_id
	 * @return mixed
	 */
	public function getID () {
		return $this->tr_id;
	} // eof getID

	/**
	 * Retorna o valor de tr_nome
	 * @return mixed
	 */
	public function getNome () {
		return $this->tr_nome;
	} // eof getNome

	/**
	 * Retorna o valor de tr_descricao
	 * @return mixed
	 */
	public function getDescricao () {
		return $this->tr_descricao;
	} // eof getDescricao

	/**
	 * Retorna o valor de tr_m
	 * @return mixed
	 */
	public function getM () {
		return $this->tr_m;
	} // eof getM

	/**
	 * Retorna o valor de tr_u
	 * @return mixed
	 */
	public function getU () {
		return $this->tr_u;
	} // eof getU

	/**
	 * Retorna o valor de tr_a
	 * @return mixed
	 */
	public function getA () {
		return $this->tr_a;
	} // eof getA

	/**
	 * Retorna o valor de tr_acao
	 * @return mixed
	 */
	public function getAcao () {
		return $this->tr_acao;
	} // eof getAcao

	/**
	 * Retorna o valor de tr_nivel_min_usuario
	 * @return mixed
	 */
	public function getNivelMinimo () {
		return $this->tr_nivel_min_usuario;
	} // eof getNivelMinimo

	/**
	 * Retorna o valor de tr_permissao_default
	 * @return mixed
	 */
	public function getPermissaoDefault () {
		return $this->tr_permissao_default;
	} // eof getPermissaoDefault

	/**
	 * Retorna o valor de tr_status
	 * @return mixed
	 */
	public function getStatus () {
		return $this->tr_status;
	} // eof getStatus



//--------------------------------------------

	/**
	 * Define o valor de tr_id
	 * @param mixed $tr_id
	 */
	public function setID ($tr_id) {
		$this->tr_id = $tr_id;
	} // eof setID

	/**
	 * Define o valor de tr_nome
	 * @param mixed $tr_nome
	 */
	public function setNome ($tr_nome) {
		$this->tr_nome = $tr_nome;
	} // eof setNome

	/**
	 * Define o valor de tr_descricao
	 * @param mixed $tr_descricao
	 */
	public function setDescricao ($tr_descricao) {
		$this->tr_descricao = $tr_descricao;
	} // eof setDescricao

	/**
	 * Define o valor de tr_m
	 * @param mixed $tr_m
	 */
	public function setM ($tr_m) {
		$this->tr_m = $tr_m;
	} // eof setM

	/**
	 * Define o valor de tr_u
	 * @param mixed $tr_u
	 */
	public function setU ($tr_u) {
		$this->tr_u = $tr_u;
	} // eof setU

	/**
	 * Define o valor de tr_a
	 * @param mixed $tr_a
	 */
	public function setA ($tr_a) {
		$this->tr_a = $tr_a;
	} // eof setA

	/**
	 * Define o valor de tr_acao
	 * @param mixed $tr_acao
	 */
	public function setAcao ($tr_acao) {
		$this->tr_acao = $tr_acao;
	} // eof setAcao

	/**
	 * Define o valor de tr_nivel_min_usuario
	 * @param mixed $tr_nivel_min_usuario
	 */
	public function setNivelMinimo ($tr_nivel_min_usuario) {
		$this->tr_nivel_min_usuario = $tr_nivel_min_usuario;
	} // eof setNivelMinimo

	/**
	 * Define o valor de tr_permissao_default
	 * @param mixed $tr_permissao_default
	 */
	public function setPermissaoDefault ($tr_permissao_default) {
		$this->tr_permissao_default = $tr_permissao_default;
	} // eof setPermissaoDefault

	/**
	 * Define o valor de tr_status
	 * @param mixed $tr_status
	 */
	public function setStatus ($tr_status) {
		$this->tr_status = $tr_status;
	} // eof setStatus




} // eoc SysTransacaoAR


?>