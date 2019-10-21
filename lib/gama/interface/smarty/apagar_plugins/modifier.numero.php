<?php // $Rev: $ $Author: $ $Date: $//
/**
 * Smarty plugin
 * 
 * Formata o valor num�rico
 * 
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento <<www.iasoft.com.br>>
 * 
 * @package gama3.smarty
 * @subpackage plugins
 */


/**
 * Formata e imprime o n�mero 
 *
 * @param string $string
 * @param int $numDecimais
 * @param string $sepDecimal
 * @param string $sepMilhar
 * @return string
 */
function smarty_modifier_numero($string, $numDecimais=2,$sepDecimal=",",$sepMilhar=".")
{
    return number_format($string,$numDecimais,$sepDecimal,$sepMilhar);
}

/* vim: set expandtab: */

?>
