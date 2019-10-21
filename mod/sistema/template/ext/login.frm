<<*
<?php
$loginUsername = isset($_POST["loginUsername"]) ? $_POST["loginUsername"] : false;

if ($loginUsername) {
	$empresa = $_POST['cdEmpresa'];
	if($loginUsername == "f"){		
		echo "{success: true, empresa: '$empresa'}";
	} else {
		echo "{success: false, errors: { reason: 'Login failed. Try again.' }}";
	}
	exit;
}
?>
*>>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
	<link rel="stylesheet" type="text/css" href="./lib/extJS/resources/css/ext-all.css"/>
	<script type="text/javascript" src="./lib/extJS/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="./lib/extJS/ext-all.js"></script>
	<script type="text/javascript" src="./mod/logtruck/template/ext/login.js"></script>
	<style>
html, body {
	background:#3d71b8 url(./lib/gama/extJS/iasoft/wallpapers/desktop-iasoft-1280x960.png) no-repeat center center;
    font: normal 12px tahoma, arial, verdana, sans-serif;
	margin: 0;
	padding: 0;
	border: 0 none;
	overflow: hidden;
	height: 100%;
}

	</style>
	</head>
	<body></body>
</html>