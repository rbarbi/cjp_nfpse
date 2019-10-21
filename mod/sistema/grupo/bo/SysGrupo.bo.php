<?php // $Rev: 154 $ - $Author: eduluz $ $Date: 2008-09-26 17:57:01 -0300 (sex, 26 set 2008) $

/**
 * Usuário no sistema.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package Gama3
 * @created 10-jun-2008 16:54:19
 */
class SysGrupoBO extends BasePersistenteBO {


	/**
	  * @var mixed ID
	  */
	private $ID;

	/**
	 * @required
	 * @var mixed nome
	 */
	private $nome;

	 /**
	  * @var mixed descricao 
	  */
	 private $descricao;

	/**
	 * Indicador de exclusão lógica. Registro ativo terão este campo com valor igual a
	 * 'A', enquanto que registros inativos possuirão o valor 'I'.
	 *
	 * @var char status
	 */
	private $status;

	
	 /**
	  * Usuário que administra o grupo
	  * @var SysUsuarioBO usuarioAdmin 
	  */
	 private $usuarioAdmin;


	/**
	 * Lista de membros deste grupo.
	 *
	 * @var array lista de SysUsuarioBO
	 */
	private $lsMembros = array();
		

//--------------------------------------------

	/**
	 * Retorna a lista de membros
	 *
	 * @return unknown
	 */
	function getLsMembros() {
		return $this->lsMembros;
	} // eof getLsMembros


	/**
	 * Retorna o valor de descricao
	 * @return string
	 */
	public function getDescricao () {
		return $this->descricao;
	} // eof getDescricao 

	/**
	 * Retorna o valor de usuarioAdmin
	 * @return SysUsuarioBO
	 */
	public function getUsuarioAdmin () {
		if (is_null($this->usuarioAdmin)) {
			$this->usuarioAdmin = new SysUsuarioBO();
		}
		return $this->usuarioAdmin;
	} // eof getIdUsuarioAdmin 



//--------------------------------------------

	/**
	 * Define o valor de descricao
	 * @param mixed $descricao
	 */
	public function setDescricao ($descricao) {
		$this->descricao = $descricao;
	} // eof setDescricao 

	
	/**
	 * Define o valor de usuarioAdmin
	 * @param SysUsuarioBO $usuarioAdmin
	 */
	public function setUsuarioAdmin ($usuarioAdmin) {
		if (is_numeric($usuarioAdmin)) {
			$this->getUsuarioAdmin()->setID($usuarioAdmin);
		} else {
			$this->usuarioAdmin = $usuarioAdmin;
		}
	} // eof setIdUsuarioAdmin 

	
	
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
	 * Retorna o valor de status
	 * @return mixed
	 */
	public function getStatus () {
		return $this->status;
	} // eof getStatus



	//--------------------------------------------

	
	/**
	 * Atribui a lista passada como parâmetro à lista interna.
	 *
	 * @param array $ls
	 */
	public function setLsMembros($ls) {		
		$this->lsMembros = $ls;
	} // eof setLsMembros
	
	
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
	 * Define o valor de status
	 * @param mixed $status
	 */
	public function setStatus ($status) {
		$this->status = $status;
	} // eof setStatus



	/**
	 * Retorna um AR de Grupo vazio, apenas com o ID preenchido.
	 *
	 * @return SysGrupoAR
	 */
	function getAR() {
		return $this->getGrupoAR();
//		$ar = new SysGrupoAR();
//		$ar->setID($this->getID());
//		return $ar;
	} // eof getAR

	/**
	 * @return SysGrupoAR
	 */
	function getGrupoAR() {
//		$ar = $this->getAR();
		$ar = new SysGrupoAR();
		$ar->setID($this->getID());		
		$ar->setNome($this->getNome());
		$ar->setDescricao($this->getDescricao());
		$ar->setIdUsuarioAdmin($this->getUsuarioAdmin()->getID());
		$ar->setStatus($this->getStatus());
		return $ar;
	} // eof getGrupoAR

	
	/**
	 * @param SysGrupoAR $ar
	 */
	function bind($ar) {
		$this->setID($ar->getID());
		$this->setNome($ar->getNome());
		$this->setDescricao($ar->getDescricao());
		$this->getUsuarioAdmin()->setID($ar->getIdUsuarioAdmin());
		$this->setStatus($ar->getStatus());
	} // eof bind

	/**
	 * Inclui um registro de usuário.
	 *
	 */
	function insert() {
		$this->setStatus(SysGrupoBO::ST_REG_ATIVO);
		
//		$ar = $this->getGrupoAR();
//		$ar->setID(null);
		parent::insert($ar);
	} // eof insert


	/**
	 * Atualiza um registro de usuário
	 *
	 */
	function update() {
		$ar = $this->getGrupoAR();
		parent::update($ar);
	} // eof update


	/**
	 * Tenta excluir o usuário, mas se o mesmo estiver associado a algum grupo
	 * ou permissão, então desativa-o.
	 *
	 * @return boolean true=excluiu fisicamente; false=desativou logicamente
	 */
	function delete() {
		try {
			parent::delete();
			return true;
		} catch (Exception $e) {
			$this->load();
			$ar = $this->getGrupoAR();
			$ar->setStatus(BasePersistenteBO::ST_REG_INATIVO);
			parent::update($ar);
			return false;
		}
	} // eof delete




	/**
	 * Realiza a inclusão do usuário especificado neste grupo
	 *
	 * @param int $usuID
	 * @param int $nivel
	 */
	function incluiUsuarioGrupo($usuID, $nivel = SysUsuarioBO::USER_NIVEL_USUARIO ) {
		$ar = new SysUsuarioGrupoAR();
		$ar->setIdUsuario($usuID);
		$ar->setNivel($nivel);
		$ar->setIdGrupo($this->getID());
		$ar->setIdUsuarioCadastrante($this->getApp()->getSess()->getProfile()->getUsuario()->getID());
		$ar->setDhCadastro(date('Y-m-d h:i:s'));
		$ar->insert();
		
		$bo = new SysUsuarioBO();
		$bo->setID($usuID);
		$this->addMembroGrupo($bo);
	} // eof incluiUsuarioGrupo


	/**
	 * Inclui um item de SysUsuario na lista interna de membros
	 *
	 * @param SysUsuarioBO $bo
	 */
	function addMembroGrupo($bo) {
		$this->lsMembros[$bo->getID()] = $bo;
	} // eof addMembroGrupo

	
	/**
	 * Remove um SysUsuarioBO da lista interna de membros deste grupo.
	 *
	 * @param int $usuID
	 */
	function delMembroGrupo($usuID) {
		unset($this->lsMembros[$usuID]);
	} // eof delMembroGrupo
	

	/**
	 * Realiza a inclusão do usuário especificado neste grupo
	 *
	 * @param int $usuID
	 */
	function excluiUsuarioGrupo($usuID) {
		$ar = new SysUsuarioGrupoAR();
		$ar->setIdUsuario($usuID);
		$ar->setIdGrupo($this->getID());
		$ar->delete();
		
		$this->delMembroGrupo($usuID);
		
	} // eof incluiUsuarioGrupo


	/**
	 * Carrega o grupo, juntamente com a lista de usuários associados.
	 *
	 */
	function load() {
		parent::load();
		$this->setLsMembros($this->getListaSimplesUsuarios());
	} // eof load
	
	
	/**
	 * Recupera a lista de IDs e nomes dos usuários que pertencem a este grupo.
	 *
	 * @return array
	 */
	function getListaSimplesUsuarios() {
		$dao = new SysDAO();
		$ls = $dao->getListaUsuariosGrupo($this->getID());
		
		foreach ($ls as $vo) {
			$bo = new SysUsuarioBO();
			$bo->setID($vo->getUsuarioID());
			$bo->setNome($vo->getNomeUsuario());
			$lista[$vo->getUsuarioID()] = $bo;
		}
		
		return $lista;
	} // eof getListaSimplesUsuarios
	
	
	
} //  SysGrupoBO

?>