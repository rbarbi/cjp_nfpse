<?php // $Rev: 500 $ - $Author: eduluz $ $Date: 2010-04-08 18:42:35 -0300 (Thu, 08 Apr 2010) $

/**
 * Usu�rio no sistema.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package Gama3
 * @created 10-jun-2008 16:54:19
 */
class SysUsuarioBO extends BasePersistenteBO {


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
	 * @required
	 * @var mixed username
	 */
	private $username;

	/**
	 * @var mixed senha
	 */
	private $senha;

	/**
	  * @var mixed nivel
	  */
	private $nivel;

	/**
	 * Administrador do sistema, pode atuar em todas as atividades
	 * internas e configur�veis. Pode ser um Implantador ou t�cnico
	 * que dar� suporte/manuten��o ao sistema como um todo.
	 *
	 * Faixa: 1 a 9
	 */
	const USER_NIVEL_ADMIN = 9;

	/**
	 * O Gestor � um usu�rio que possui alguns privil�gios como
	 * conceder direito de acesso a outros usu�rios, de m�dulos que
	 * est�o associados aos grupos que tem o usu�rio em quest�o como
	 * gestor.
	 *
	 * Faixa: 10 a 99
	 */
	const USER_NIVEL_GESTOR = 99;

	/**
	 * Este � um usu�rio regular, com direitos exclusivamente
	 * definidos pelas permiss�es a ele concedidas.
	 *
	 * Faixa: 100 a 999
	 */
	const USER_NIVEL_USUARIO = 999;

	/**
	 * Usu�rio n�o-cadatrado no sistema.
	 * �til para dar acesso a �reas puramente informativas, ou a
	 * subm�dulos que possuem instrumentos particulares de controle
	 * de acesso.
	 *
	 * Faixa: 1000 a 99999
	 */
	const USER_NIVEL_ANONIMO = 99999;


	/**
	 * Indicador de exclus�o l�gica. Registro ativo ter�o este campo com valor igual a
	 * 'A', enquanto que registros inativos possuir�o o valor 'I'.
	 *
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
	 * Retorna o valor de nome
	 * @return mixed
	 */
	public function getNome () {
		return $this->nome;
	} // eof getNome

	/**
	 * Retorna o valor de username
	 * @return mixed
	 */
	public function getUsername () {
		return $this->username;
	} // eof getUsername

	/**
	 * Retorna o valor de senha
	 * @return mixed
	 */
	public function getSenha () {
		return $this->senha;
	} // eof getSenha

	/**
	 * Retorna o valor de nivel
	 * @return mixed
	 */
	public function getNivel () {
		return $this->nivel;
	} // eof getNivel

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
	 * @param int $ID
	 */
	public function setID ($ID) {
		$this->ID = $ID;
	} // eof setID

	/**
	 * Define o nome do usu�rio
	 * @param string $nome
	 */
	public function setNome ($nome) {
		$this->nome = $nome;
	} // eof setNome

	/**
	 * Define o valor de username
	 * @param string $username
	 */
	public function setUsername ($username) {
		$this->username = $username;
	} // eof setUsername

	/**
	 * Define o valor de senha
	 * @param string   $senha
	 * @param boolean $raw
	 */
	public function setSenha ($senha, $raw = false) {
		if ($raw) {
			$this->senha = $senha;
		} else {
			$this->senha = $this->calculaSenha($senha);
		}
	} // eof setSenha

	/**
	 * Define o valor de nivel
	 * @param int $nivel
	 */
	public function setNivel ($nivel) {
		$this->nivel = $nivel;
	} // eof setNivel

	/**
	 * Define o valor de status
	 * @param int $status
	 */
	public function setStatus ($status) {
		$this->status = $status;
	} // eof setStatus



	/**
	 * Determina o valor do hash que ser� guardado no banco de dados.
	 *
	 * @param string $s
	 * @return string
	 */
	protected function calculaSenha($s) {
		return sha1 ($s);
	} // eof calculaSenha


	/**
	 * Construtor
	 *
	 * @param MainGama $app
	 * @return SysUsuarioBO
	 */
	function __construct() {
//		$this->BasePersistenteBO($app);
	} // SysUsuarioBO



    /**
     * Retorna um AR de Usuario vazio, apenas com o ID preenchido.
     *
     * @return SysUsuarioAR
     */
    function getAR() {
        return $this->getUsuarioAR();
    } // eof getAR

    /**
     * @return SysUsuarioAR
     */
    function getUsuarioAR() {
        $ar = new SysUsuarioAR();
        $ar->setID($this->getID());
        $ar->setNivel($this->getNivel());
        $ar->setNome($this->getNome());
        $ar->setSenha($this->getSenha());
        $ar->setStatus($this->getStatus());
        $ar->setUsername($this->getUsername());
        return $ar;
    } // eof getUsuarioAR

	/**
	 * @param SysUsuarioAR $ar
	 */
	function bind($ar) {
		$this->setID($ar->getID());
		$this->setNivel($ar->getNivel());
		$this->setNome($ar->getNome());
		$this->setSenha($ar->getSenha(),true);
		$this->setStatus($ar->getStatus());
		$this->setUsername($ar->getUsername());
	} // eof bind

	/**
	 * Inclui um registro de usu�rio.
	 *
	 */
	function insert() {
		$this->setStatus(SysUsuarioBO::ST_REG_ATIVO);
		$ar = $this->getUsuarioAR();
		$ar->setID(null);
		parent::insert($ar);
	} // eof insert


	/**
	 * Atualiza um registro de usu�rio
	 *
	 */
	function update() {
		$ar = $this->getUsuarioAR();
		parent::update($ar);
	} // eof update


	/**
	 * Tenta excluir o usu�rio, mas se o mesmo estiver associado a algum grupo
	 * ou permiss�o, ent�o desativa-o.
	 *
	 * @return boolean true=excluiu fisicamente; false=desativou logicamente
	 */
	function delete() {
		try {
			parent::delete();
			return true;
		} catch (Exception $e) {
			$this->load();
			$ar = $this->getUsuarioAR();
			$ar->setStatus(BasePersistenteBO::ST_REG_INATIVO);
			parent::update($ar);
			return false;
		}
	} // eof delete



	/**
	 * Retorna a lista de status dispon�veis.
	 *
	 * @return array
	 */
	function getListaStatus() {
		return array(	SysUsuarioBO::ST_REG_ATIVO => 'Ativo',
		SysUsuarioBO::ST_REG_INATIVO  => 'Inativo');
	} // eof getListaStatus

	/**
	 * Retorna a lista de status dispon�veis para filtragem nas listagens.
	 *
	 * @return array
	 */
	function getListaFiltroStatus() {
		$status = SysUsuarioBO::getListaStatus();
		$status[SysUsuarioBO::ST_REG_AMBOS] = 'Ambos';
		return $status;
	} // eof getListaFiltroStatus


	/**
	 * Verifica se a senha informada � a mesma da cadastrada para este usu�rio.
	 *
	 * @param string $senha
	 * @return boolean
	 */
	public function testaSenha($senha) {
		if ($this->calculaSenha($senha) == $this->getSenha()) {
			return true;
		} else {
			return false;
		}
	} // eof testaSenha


	public function recuperaUsuario($username, $senha) {
		$dao = new SysDAO();
		$vo = $dao->getUsuarioPorUsernameSenha($username,$this->calculaSenha($senha));
		$this->bind($vo);
	} // eof recuperaUsuario

} //  SysUsuarioBO