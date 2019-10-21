<? // $Rev: 11 $ - $Author: eduluz $ $Date: 2008-06-12 11:38:50 -0300 (qui, 12 jun 2008) $

/**
 * Classe que representa um Usuário do sistema.
 * Usuário do sistema, com um perfil de acesso,
 * um username, senha e um nível de acesso (administrador,
 * operador, etc).
 *
 * @author Eduardo Schmitt da Luz
 * @created 15-nov-2007 16:04:39
 * @copyright IASoft Desenvolvimento de Sistemas
 */
class SysUsuarioAR extends BaseAR {

	/**
	 * Identificação única e autoincremental do objeto.
	 *
	 * @var int
	 */
	public $usu_id;

	/**
	 * Nome completo do usuário. ('Display name')
	 *
	 * @var string
	 */
	public $usu_nome;

	/**
	 * Nome de acesso do usuário (username), usado para
	 * operações de login.
	 *
	 * @var string
	 */
	public $usu_username;

	/**
	 * Senha do usuário.
	 *
	 * @var string
	 */
	public $usu_senha;

	/**
	 * Nível de acesso do usuário.
	 *
	 * @var integer
	 */
	public $usu_nivel;

	/**
	 * Indicador de exclusão lógica. Registro ativo
	 * terão este campo com valor igual a 'A', enquanto
	 * que registros inativos possuirão o valor 'I'.
	 * Outros valores poderão ser definidos pelo padrão
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