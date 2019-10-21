<? // $Rev: 11 $ - $Author: eduluz $ $Date: 2008-06-12 11:38:50 -0300 (qui, 12 jun 2008) $

/**
 * Classe que representa um Usu�rio do sistema.
 * Usu�rio do sistema, com um perfil de acesso,
 * um username, senha e um n�vel de acesso (administrador,
 * operador, etc).
 *
 * @author Eduardo Schmitt da Luz
 * @created 15-nov-2007 16:04:39
 * @copyright IASoft Desenvolvimento de Sistemas
 */
class SysUsuarioAR extends BaseAR {

	/**
	 * Identifica��o �nica e autoincremental do objeto.
	 *
	 * @var int
	 */
	public $usu_id;

	/**
	 * Nome completo do usu�rio. ('Display name')
	 *
	 * @var string
	 */
	public $usu_nome;

	/**
	 * Nome de acesso do usu�rio (username), usado para
	 * opera��es de login.
	 *
	 * @var string
	 */
	public $usu_username;

	/**
	 * Senha do usu�rio.
	 *
	 * @var string
	 */
	public $usu_senha;

	/**
	 * N�vel de acesso do usu�rio.
	 *
	 * @var integer
	 */
	public $usu_nivel;

	/**
	 * Indicador de exclus�o l�gica. Registro ativo
	 * ter�o este campo com valor igual a 'A', enquanto
	 * que registros inativos possuir�o o valor 'I'.
	 * Outros valores poder�o ser definidos pelo padr�o
	 * da arquitetura em vigor.
	 *
	 * @var char
	 */
	public $usu_status_registro;



	/**
	 * Construtor da classe.
	 *
	 * @return UsuarioAR
	 */
	function SysUsuarioAR() {
		BaseAR::init('tb_usuario',array('usu_id'));
	} // eof SysUsuarioAR

} // eoc SysUsuarioAR

?>