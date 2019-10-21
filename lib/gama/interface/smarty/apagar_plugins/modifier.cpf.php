<?php // $Rev: $ $Author: $ $Date: $//
/**
 * Smarty plugin
 * 
 * Formata o valor numérico
 * 
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento <<www.iasoft.com.br>>
 * 
 * @package gama3.smarty
 * @subpackage plugins
 */


/**
 * Formata e imprime o número 
 *
 * @param string $string
 * @return string
 */
function smarty_modifier_cpf($string)
{
	$dv = substr($string,-2);
	$numero = substr($string,0,-2);
    return sprintf("%s-%02d",number_format($numero,0,'','.'),$dv);
}


?>
