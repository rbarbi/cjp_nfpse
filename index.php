<?php

include_once ('./lib/gama/base/Main.php');
$app = MainGama::getInstanceOf();
if(isset($_POST['amp;dadosNota'])){
    $_POST['dadosNota'] = $_POST['amp;dadosNota'];
    $_POST['amp;dadosNota'] = null;
}
echo $app->exec($_GET, $_POST);