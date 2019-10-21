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
function smarty_modifier_cep($string)
{
	$final = substr($string,-3);
	$numero = substr($string,0,-3);
    return sprintf("%s-%03d",$numero,$final);
}


?>
