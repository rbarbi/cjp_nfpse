<?php // $Rev: 84 $ - $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $

/**
 * Classe que reњne mщtodos de tratamento, conversуo e formataчуo de
 * valores monetсreos.
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.utils.monetario
 */
class MonetarioUtil {


	function numerico2Monetario($numero,$filler='-') {
		if (is_null($numero)) {
			return $filler;
		} else {
			return 'R$ ' . number_format($numero,2,',','.');
		}
	}

}


/*
$n = 1012.4;


echo MonetarioUtil::numerico2Monetario($n);*/
?>