<?php // $Rev: 84 $ - $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $


/**
 * Classe que agrupa funчѕes de conversуo, tratamento e formataчуo
 * de valores numщricos.
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.utils.numerico
 */
class NumericoUtil {


	function str2Numerico($str) {
		$temPonto = (strpos($str,'.')>0)?true:false;
		$temVirgula = (strpos($str,',')>0)?true:false;
		if ((!$temPonto) && (!$temVirgula)) {
			return intval($str);
		} else if (($temPonto) && ($temVirgula)) {
			$str = str_replace('.','',$str);
			$str = str_replace(',','.',$str);
			return floatval($str);
		} else if ($temVirgula) {
			$str = str_replace(',','.',$str);
			return floatval($str);
		} else {
			return floatval($str);
		}
	}

}


?>