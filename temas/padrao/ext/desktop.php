<?php
header('content-type: text/javascript');

//readfile('./desktop.tjs');

$lista = split(",",$_GET['js']);



$basePath = '../../../mod/';
foreach ($lista as $js) {
	if (strlen($js)>1) {
		echo "\n// Incluindo o arquivo $js";
		readfile($basePath . $js);
	}
}

?>