<?php
/**
 * Para o gama executar corretamente
 */
chdir(dirname(__FILE__));
date_default_timezone_set('America/Sao_Paulo');
include_once('./lib/gama/base/Main.php');

if (!function_exists("pre")) {
    /**
     * @param array $x
     * @param string $titulo
     * @param boolean $exit
     */
    function pre($x, $titulo = '', $exit = false)
    {
        ob_implicit_flush();
        $pid = getmypid();
        echo "<fieldset style='min-width: 50%; word-wrap: break-word; background-color: #FAFAFA; border: 2px groove #ddd !important; padding: 1.4em 1.4em 1.4em 1.4em !important;'>";
        if (!empty($titulo)) {
            echo "<legend style='color:rgb(0, 0, 123); padding: 3px 10px 3px 10px; font-weight: bold; font-size: 14px; text-transform: uppercase; border: 1px groove #ddd !important;'> $titulo </legend>";
        }
        echo "<pre>";
        echo "----------------------------\r\nProcesso PID: {$pid}\r\n----------------------------\r\n\r\n";
        print_r($x);
        echo "</pre>";
        echo "</fieldset>";
        ob_flush();
        flush();
        if ($exit) {
            exit;
        }
    }

    /**
     * @param array $x
     * @param string $titulo
     */
    function pred($x, $titulo = '')
    {
        pre($x, $titulo, true);
    }
}

$app = MainGama::getInstanceOf();
$_POST['m'] = "destaque";
$_POST['u'] = ".";
$_POST['a'] = "Publicacao";
$_POST['acao'] = "doCompara";
$_POST['data'] = "05/06/2017";
echo $app->exec($_GET,$_POST);
