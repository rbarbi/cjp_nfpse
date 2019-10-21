<?php // $Rev: $ $Author: $ $Date: $//

include_once('G3ZIP.php');
include_once('ZIPWin32.php');
include_once('ZIPFreeBSD.php');

/**
 * Classe que gerencia a criaчуo de objetos ZIP, de acordo com o sistema
 * operacional em que o script estс rodando.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.zip
 * 
 */
class ZIPFactory {

	protected static $instance;

	/**
	 * Enter description here...
	 *
	 * @return G3ZIP
	 */
	public static function getInstance() {
		if (is_null(self::$instance)) {
			$OSType = PHP_OS;
			switch (strtoupper(substr(PHP_OS, 0, 3))) {
				case 'WIN': self::$instance = new G3ZIPWin32(); break;
				default : self::$instance = new G3ZIPFreeBSD(); break;
			}
		}
		return self::$instance;
	}


}

?>