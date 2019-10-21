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
function smarty_function_calendario_header($params, &$smarty)
{

	return '<style type="text/css">@import url(./lib/gama/interface/jscalendar/calendar-win2k-1.css);</style>
<script type="text/javascript" src="./lib/gama/interface/jscalendar/calendar.js"></script>
<script type="text/javascript" src="./lib/gama/interface/jscalendar/jscal_index.js?x=4"></script>
<script type="text/javascript" src="./lib/gama/interface/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="./lib/gama/interface/jscalendar/calendar-setup.js"></script>';

/*	$smarty->_trigger_fatal_error('[plugin] fetch cannot read file \'' . $params['file'] .'\'');


	if (!empty($params['assign'])) {
		$smarty->assign($params['assign'],$content);
	} else {
		return $content;
	}*/
}

/* vim: set expandtab: */

?>
