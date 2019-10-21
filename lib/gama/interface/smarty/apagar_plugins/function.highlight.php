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
 * @param int $numDecimais
 * @param string $sepDecimal
 * @param string $sepMilhar
 * @return string
 */
function smarty_function_highlight($params, &$smarty)
{

        return '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:'.$zindex.';"></div>' . "\n"
         . '<script type="text/javascript" language="JavaScript" src="'.$params['src'].'"></script>' . "\n";
}

/* vim: set expandtab: */

?>