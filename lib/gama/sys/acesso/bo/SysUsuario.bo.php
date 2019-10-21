<?php // $Rev: 82 $ - $Author: eduluz $ $Date: 2008-09-01 13:22:45 -0300 (seg, 01 set 2008) $

/**
 * Classe que gerencia as opera��es de cadastro de usu�rios,
 * consist�ncia, autentica��o, etc.
 * @author eduluz
 * @version 1.0
 * @updated 10-jun-2008 16:54:19
 */
class SysUsuarioBO_ extends BasePersistenteBO
{

	/**
	 * Identificador do objeto.
	 */
	var $id;
	/**
	 * Nome completo do usu�rio. ('Display name').
	 */
	var $nome;
	/**
	 * Nome de acesso do usu�rio (username), usado para opera��es de login.
	 */
	var $username;
	/**
	 * Senha do usu�rio.
	 */
	var $senha;
	/**
	 * N�vel de acesso do usu�rio.
	 */
	var $nivelAcesso;
	/**
	 * Indicador de exclus�o l�gica. Registro ativo ter�o este campo com valor igual a
	 * 'A', enquanto que registros inativos possuir�o o valor 'I'.
	 *
	 * Outros valores poder�o ser definidos pelo padr�o da arquitetura em vigor.
	 *
	 */
	var $statusRegistro;



	/**
	 * Administrador do sistema, pode atuar em todas as
	 * atividades internas e configur�veis. Pode ser um
	 * Implantador ou t�cnico que dar� suporte/manuten��o
	 * ao sistema como um todo.
	 */
	const USER_NIVEL_ADMIN = 1;

	/**
	 * O Gestor � um usu�rio que possui alguns privil�gios
	 * como conceder direito de acesso a outros usu�rios,
	 * de m�dulos que est�o associados aos grupos que tem
	 * o usu�rio em quest�o como gestor.
	 */
	const USER_NIVEL_GESTOR = 10;

	/**
	 * Este � um usu�rio regular, com direitos exclusivamente
	 * definidos pelas permiss�es a ele concedidas.
	 */
	const USER_NIVEL_USUARIO = 50;

	/**
	 * Usu�rio n�o-cadatrado no sistema. �til para dar acesso
	 * a �reas puramente informativas, ou a subm�dulos que
	 * possuem instrumentos particulares de controle de acesso.
	 */
	const USER_NIVEL_ANONIMO = 100;


	/**
	 * Construtor da classe SysUsuarioBO.
	 *
	 * @return SysUsuarioBO
	 */
	function SysUsuarioBO_(&$app)
	{
		$this->BasePersistenteBO($app);
	}



	/**
	 * Identificador do objeto.
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * Identificador do objeto.
	 *
	 * @param id
	 */
	function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Nome completo do usu�rio. ('Display name').
	 */
	function getNome()
	{
		return $this->nome;
	}

	/**
	 * Nome completo do usu�rio. ('Display name').
	 *
	 * @param nome
	 */
	function setNome($nome)
	{
		$this->nome = $nome;
	}

	/**
	 * Nome de acesso do usu�rio (username), usado para opera��es de login.
	 */
	function getUsername()
	{
		return $this->username;
	}

	/**
	 * Nome de acesso do usu�rio (username), usado para opera��es de login.
	 *
	 * @param username
	 */
	function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * Senha do usu�rio.
	 */
	function getSenha()
	{
		return $this->senha;
	}

	/**
	 * Senha do usu�rio.
	 *
	 * @param senha
	 */
	function setSenha($senha)
	{
		$this->senha = $senha;
	}

	/**
	 * N�vel de acesso do usu�rio.
	 */
	function getNivelAcesso()
	{
		return $this->nivelAcesso;
	}

	/**
	 * N�vel de acesso do usu�rio.
	 *
	 * @param nivelAcesso
	 */
	function setNivelAcesso($nivelAcesso)
	{
		$this->nivelAcesso = $nivelAcesso;
	}

	/**
	 * Retorna o status do registro atual.
	 *
	 */
	function getStatusRegistro()
	{
		return $this->statusRegistro;
	}

	/**
	 * Define o valor do status do registro.
	 *
	 * @param statusRegistro
	 */
	function setStatusRegistro($statusRegistro)
	{
		$this->statusRegistro = $statusRegistro;
	}


	/**
	 * Retorna uma inst�ncia de SysUsuarioAR
	 *
	 * @return SysUsuarioAR
	 */
	function getUsuarioAR() {
		$ar = new SysUsuarioAR();
		$ar->usu_id = $this->getId();
		$ar->usu_nivel = $this->getNivelAcesso();
		$ar->usu_nome = $this->getNome();
		$ar->usu_senha = $this->getSenha();
		$ar->usu_status_registro = $this->getStatusRegistro();
		$ar->usu_username = $this->getUsername();
		return $ar;
	}


	function insert() {
		$ar = $this->getUsuarioAR();
		parent::insert($ar);
	}

	function update() {
		$ar = $this->getUsuarioAR();
		parent::update($ar);
	}


	function delete() {
		$ar = $this->getUsuarioAR();
		parent::delete($ar);
	}


	/**
	 * Realiza o mapeamento de um SysUsuarioAR para um SysUsuarioBO.
	 *
	 * @param SysUsuarioAR $ar
	 */
	function bind($ar) {
		$this->setId($ar->usu_id);
		$this->setNivelAcesso($ar->usu_nivel);
		$this->setNome($ar->usu_nome);
		$this->setSenha($ar->usu_senha );
		$this->setStatusRegistro($ar->usu_status_registro);
		$this->setUsername($ar->usu_username);
	}

	/**
	 * Recupera uma lista de SysUsuarios de acordo com os
	 * par�metros passados.
	 *
	 * @param string $filtroStatus
	 */
	function getListaUsuarios($numRegsPorPagina,$offset,$filtro) {
		$ar = new SysUsuarioAR();
		if (strlen($filtro) > 0){
			$rs = $ar->DB()->SelectLimit("SELECT * FROM tb_usuario WHERE usu_status_registro like ? ORDER BY usu_id", $numRegsPorPagina,$offset,array($filtro));
		} else{
			$rs = $ar->DB()->SelectLimit("SELECT * FROM tb_usuario ORDER BY usu_id", $numRegsPorPagina,$offset);
		}

		$arr = array();
		while ($linha = $rs->fetchRow()){
			$bo = new SysUsuarioBO($this->getApp());
			$bo->setId($linha['usu_id']);
			$bo->setNome($linha['usu_nome']);
			$bo->setUsername($linha['usu_username']);
			$bo->setNivelAcesso($linha['usu_nivel']);
			$bo->setSenha($linha['usu_senha']);
			$bo->setStatusRegistro($linha['usu_status_registro']);
			$arr[] = $bo;
		}
		return $arr;
	}// eof getListaUsuarios

	function getNumRegistros($filtro){
		$ar = new SysUsuarioAR();
		if (strlen($filtro) > 0){
			$rs = $ar->DB()->Execute("select count(usu_id) from tb_usuario where usu_status_registro like ?",array($filtro));
		} else{
			$rs = $ar->DB()->Execute("select count(usu_id) from tb_usuario");
		}
		$linha = $rs->fetchRow();
		return $linha[0];
	}

	function load() {
		$ar = $this->getUsuarioAR();
		parent::load($ar);
		$this->bind($ar);
	}



	/**
	 * Retorna a lista de status dispon�veis.
	 *
	 * @return array
	 */
	function getListaStatus() {
		return array(	SysUsuarioBO::ST_REG_ATIVO => 'Ativo',
		SysUsuarioBO::ST_REG_INATIVO  => 'Inativo');
	}

	/**
	 * Retorna a lista de status dispon�veis para filtragem nas listagens.
	 *
	 * @return array
	 */
	function getListaFiltroStatus() {
		$status = SysUsuarioBO::getListaStatus();
		$status[SysUsuarioBO::ST_REG_AMBOS] = 'Ambos';
		return $status;
	}




} // eoc SysUsuarioBO
?>
