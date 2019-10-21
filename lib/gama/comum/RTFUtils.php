<?php // $Rev: $ $Author: $ $Date: $//

/**
 * Classe que concentra métodos que auxiliam nos tratamentos de tags RTF.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.utils
 */
class RTFUtils {
	
	
	
	function nl2br ($s) {
		$s = str_replace('{',"\{",$s);
		$s = str_replace('}',"\}",$s);
		$s = str_replace("\r\n","\line ",$s);
		$s = str_replace("<br>","\line ",$s);
		$s = str_replace("<br />","\line ",$s);
		$s = str_replace('-----','',$s);
		return $s;
	}
	
	
}


?>