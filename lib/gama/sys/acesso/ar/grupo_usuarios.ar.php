<?php // $Rev: 11 $ - $Author: eduluz $ $Date: 2008-06-12 11:38:50 -0300 (qui, 12 jun 2008) $


/**
 * Conjunto de usu�rios, criado de acordo com
 * as necessidades de cada aplica��o.
 *
 * @author Eduardo S. da Luz
 * @package Gama3
 * @created 2008-06-10
 * @copyright IASoft Desenvolvimento de Sistemas
 */
class SysGrupoUsuariosAR extends BaseAR {

	/**
	 * Identificador �nico e auto-incremental.
	 *
	 * @var integer
	 */
	public $gu_id;

	/**
	 * Nome do grupo.
	 *
	 * @var string
	 */
	public $gu_nome;

	/**
	 * Descri��o do grupo em quest�o.
	 *
	 * @var string
	 */
	public $gu_descricao;

	/**
	 * C�digo do usu�rio respons�vel pelo grupo.
	 *
	 * @var integer
	 */
	public $gu_usuario_admin_id;

	/**
	 * Indicador da validade do registro.
	 *
	 * @var char
	 */
	public $gu_status_registro;

	/**
	 * Construtor da classe SysGrupoUsuariosAR.
	 *
	 * @return SysGrupoUsuariosAR
	 */
	function SysGrupoUsuariosAR() {
		$this->init('tb_grupo_usuarios',array('gu_id'));
	}


} // eoc SysGrupoUsuarios



?>