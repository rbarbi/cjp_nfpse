<?php // $Rev: 141 $ - $Author: eduluz $ $Date: 2008-09-16 17:15:08 -0300 (ter, 16 set 2008) $

/**
 * Grupo de Usuбrios que possui algum significado ou papel nos processos de
 * negуcio da aplicaзгo.
 * @author eduluz
 * @version 1.0
 * @updated 11-jun-2008 18:02:11
 */
class SysGrupoUsuariosBO extends BaseBO
{

	/**
	 * Identificador ъnico, autoincremental.
	 */
	var $id;

	/**
	 * Nome do grupo.
	 */
	var $nome;

	/**
	 * Descriзгo do grupo de usuбrios.
	 */
	var $descricao;

	/**
	 * Usuбrio que й responsбvel pela administraзгo do grupo.
	 */
	var $usuarioAdmin;

	/**
	 * Indicador de validade do registro.
	 */
	var $statusRegistro;

	/**
	 * Construtor da classe SysGrupoUsuariosBO
	 *
	 * @param MainGama
	 * @return SysGrupoUsuariosBO
	 */
	function SysGrupoUsuariosBO(&$app)
	{
		$this->BaseBO($app);
	}



	/**
	 * Identificador ъnico, autoincremental.
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * Identificador ъnico, autoincremental.
	 *
	 * @param id
	 */
	function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Nome do grupo.
	 */
	function getNome()
	{
		return $this->nome;
	}

	/**
	 * Nome do grupo.
	 *
	 * @param nome
	 */
	function setNome($nome)
	{
		$this->nome = $nome;
	}

	/**
	 * Descriзгo do grupo de usuбrios.
	 */
	function getDescricao()
	{
		return $this->descricao;
	}

	/**
	 * Descriзгo do grupo de usuбrios.
	 *
	 * @param descricao
	 */
	function setDescricao($descricao)
	{
		$this->descricao = $descricao;
	}

	/**
	 * Usuбrio que й responsбvel pela administraзгo do grupo.
	 *
	 * @return SysUsuarioBO
	 */
	function getUsuarioAdmin()
	{
		if (is_null($this->usuarioAdmin)) {
			$this->usuarioAdmin = new SysUsuarioBO($this->getApp());
		}
		return $this->usuarioAdmin;
	}

	/**
	 * Usuбrio que й responsбvel pela administraзгo do grupo.
	 *
	 * @param SysUsuarioBO $usuario
	 */
	function setUsuarioAdmin(SysUsuarioBO $usuario)
	{
		if (is_numeric($usuario)) {
			$this->getUsuarioAdmin()->setId($usuario);
		} else if (is_object($usuario)) {
			$this->usuarioAdmin = $usuario;
		}
	}

	/**
	 * Indicador de validade do registro.
	 */
	function getStatusRegistro()
	{
		return $this->statusRegistro;
	}

	/**
	 * Indicador de validade do registro.
	 *
	 * @param statusRegistro
	 */
	function setStatusRegistro($statusRegistro)
	{
		$this->statusRegistro = $statusRegistro;
	}




	/**
	 * Retorna um objeto de SysGrupoUsuariosAR
	 *
	 * @return SysGrupoUsuariosAR
	 */
	function getGrupoUsuariosAR() {
		$ar = new SysGrupoUsuariosAR();
		$ar->gu_id = $this->getId();
		$ar->gu_descricao = $this->getDescricao();
		$ar->gu_nome = $this->getNome();
		$ar->gu_status_registro = $this->getStatusRegistro();
		$ar->gu_usuario_admin_id = $this->getUsuarioAdmin()->getId();
		return $ar;
	}


}


?>