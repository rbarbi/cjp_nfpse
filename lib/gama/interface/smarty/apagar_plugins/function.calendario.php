<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {fetch} plugin
 *
 * Type:     function<br>
 * Name:     fetch<br>
 * Purpose:  fetch file, web or ftp data and display results
 * @link http://smarty.php.net/manual/en/language.function.fetch.php {fetch}
 *       (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string|null if the assign parameter is passed, Smarty assigns the
 *                     result to a template variable
 */
function smarty_function_calendario($params, &$smarty)
{
//	print_r($params);
	
	if (empty($params['nome'])) {
		$smarty->_trigger_fatal_error('Erro - nome do campo de formulario nao informado');
	}
	
	if (empty($params['formulario'])) {
		$smarty->_trigger_fatal_error('Erro - nome do formulario nao informado');
	}
	
	$nome = $params['nome'];
	$formulario = $params['formulario'];
	
	$s = "<input type='hidden' id='calendario_$nome' name='calendario_$nome' value='' />";
	$s .= "<input type='text' onChange=\"setDate('$formulario', '$formulario_$nome');\" class='text' style='width:120px;' id='$formulario_$nome' name='$formulario_$nome' value='' />";
//	$s .= "<a onclick=\"return showCalendar('$formulario_$nome', '%d/%m/%Y %I:%M %p', '$formulario', '12', true)\" href=\"#\">";
	$s .= "<a onclick=\"return showCalendar('$formulario_$nome', '%d/%m/%Y', '$formulario', '12', true)\" href=\"#\">";
//	$s .= "<img src='./lib/gama/interface/jscalendar/calendar.gif' width='24' height='12' alt='Calendar' border='0' /></a>";
	$s .= "<img src='./lib/gama/interface/jscalendar/img.gif' width='26' height='20' alt='Calendar' border='0' /></a>";
	
	

	return $s;

/*	$smarty->_trigger_fatal_error('[plugin] fetch cannot read file \'' . $params['file'] .'\'');


	if (!empty($params['assign'])) {
		$smarty->assign($params['assign'],$content);
	} else {
		return $content;
	}*/
}

/* vim: set expandtab: */

?>
