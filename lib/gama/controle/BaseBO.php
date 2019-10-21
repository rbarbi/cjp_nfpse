<?php // $Rev: 268 $ - $Author: eduluz $ $Date: 2008-11-28 11:14:34 -0200 (sex, 28 nov 2008) $

/**
 * Classe base para as que encapsulam as regras de negócios do sistema.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.base.controle
 */
class BaseBO {

	
	protected $_db = false;


	/**
	 * Recupera a conexão com o servidor de bancos de dados.
	 *
	 * @return ADOConnection
	 */
	function &getCon() {
		return MainGama::getApp()->getCon($this->getDB());
	} // getCon
		

	/**
	 * Retorna a referência ao MainGama, que contém a conexão com o
	 * banco de dados, sessão, etc;
	 *
	 * @return MainGama
	 */
	function getApp() {
		return MainGama::getApp();
	}

//
//	/**
//	 * Retorna a referência ao objeto de conexão com o banco de dados
//	 *
//	 * @return ADOConnection
//	 */
//	function getCon() {
//		return $this->getApp()->getCon();
//	}

	/**
	 * Retorna a referência ao objeto de sessão.
	 *
	 * @return SessionGama
	 */
	function getSess() {
		return $this->getApp()->getSess();
	}


	/**
	 * Este método é usado para fins de debug.
	 * Ele imprime a estrutura do objeto.
	 */
	function print_pre() {
		echo '<pre>'; print_r($this); echo '</pre>';
	}
	

	
	function __construct($idConn=false) {
		$this->setDB($idConn);
	}

	
	/**
	 * Define o nome da identificação de conexão com o banco de dados.
	 *
	 * @param string $idConn
	 */
	public function setDB($idConn=false) {
		if (is_null($idConn) || ($idConn === false)) {
			$idConn = '-';
		}
		$this->_db = $idConn;
	} // eof setDB


	/**
	 * Recupera o alias da conexão auxiliar. 
	 *
	 * @return string
	 */
	public function getDB() {
		return $this->_db;
	} // eof getDB
			

} // eoc BaseBO
