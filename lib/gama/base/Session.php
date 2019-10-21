<?php // $Rev: 690 $ $Author: eduluz $ $Date: 2014-01-29 20:49:12 -0200 (qua, 29 jan 2014) $

/**
 * Classe responsavel pelo gerenciamento da sessão
 * e dos objetos nela constantes.
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.utils.sessao
 */
class SessionGama {

	/**
	 * Vetor associativo que contém os objetos que ficarão na
	 * variável de sessão no servidor.
	 *
	 * @var array
	 */
	protected $_dados = array();


	 /**
	  * Instância de SysProfile, que agrupa os dados de um usuário logado em uma sessão.
	  *
	  * @var SysProfile profile
	  */
	 private $profile;

//--------------------------------------------

	/**
	 * Retorna o valor de profile.
	 * Se este não estiver definido, retorna 'false'.
	 *
	 * @return SysProfile | boolean
	 */
	public function getProfile () {
		if (is_null($this->profile)) {
			return false;
		}
		return $this->profile;
	} // eof getProfile



//--------------------------------------------

	/**
	 * Define o valor de profile
	 * @param SysProfile $profile
	 */
	public function setProfile ($profile) {
		$this->profile = $profile;
	} // eof setProfile



	/**
	 * Retorna o valor de um objeto armazenado na sessão, através
	 * da sua chave, ou o valor default, se
	 * este não existir.
	 *
	 * @param string $chave
	 * @param mixed $default
	 * @return mixed
	 */
	function get($chave,$default=null) {
		if (array_key_exists ($chave, $this->_dados )) {
			return $this->_dados[$chave];
		} else {
			return $default;
		}
	} // eof get


	/**
	 * Verifica se a variável de sessão existe ou não.
	 *
	 * @param string $nomeVar
	 * @return boolean
	 */
	public function isRegistered($nomeVar) {
		if (isset($this->_dados[$nomeVar])) {
			return true;
		} else {
			return false;
		}
	} // eof isRegistered



	/**
	 * Atribui a um elemento do vetor interno um objeto de sessão.
	 * Este objeto estará indexado pela chave passada
	 * como parâmetro.
	 *
	 * @param string $chave
	 * @param mixed $valor
	 */
	function set($chave,$valor) {
		$this->_dados[$chave] = $valor;
	} // eof set

	/**
	 * Metodo que inicializa uma sessao.
	 * É um método estático.
	 *
	 * @param MainGama $obj
	 */
	static function getSession($obj) {
		session_name('gama'.$obj->getM());
		if (ini_get('session.auto_start') > 0) {
			session_write_close();
		}
		$max_time = 0;

		// Tenta e recupera a path correta da URL base
		preg_match('_^(https?://)([^/]+)(:0-9]+)?(/.*)?$_i', $obj->getConfig('sys_base_url'), $url_parts);
		if (array_key_exists (4, $url_parts )) {
			$cookie_dir = $url_parts[4];
		} else {
			$cookie_dir = null;
		}
		if (substr($cookie_dir, 0, 1) != '/')
			$cookie_dir = '/' . $cookie_dir;
		if (substr($cookie_dir, -1) != '/')
			$cookie_dir .= '/';
		session_set_cookie_params($max_time, $cookie_dir);
		@session_start();
		if (version_compare(phpversion(),'5.3')<=0) {
                    session_register('sess');
                }
		// Se a sessão não estiver definida, então define-a.
                if (!array_key_exists('sess', $_SESSION) ) {
			$_SESSION['sess'] = new SessionGama();
		} elseif (empty ($_SESSION['sess'])) {
                    $_SESSION['sess'] = new SessionGama();
                }
	} // eof getSession

	/**
	 * Apaga a variável com o nome passado por parâmetro da lista interna da
	 * sessão.
	 *
	 * @param string $nome
	 */
	function del($nome) {
		if ($this->isRegistered($nome)) {
			unset($this->_dados[$nome]);
//			unset($_SESSION['sess']->_dados[$nome]);
		}
	} // eof del

} // eoc SessionGama

?>