<?php 
error_reporting(E_ALL|E_STRICT); 
@date_default_timezone_set('UTC');
session_start();
$_SESSION['test']="test";

require_once('oDebug.class.php');
//mysql_connect("localhost","root","");

//mysql_select_db("gestionstock");


class oDB{															//sample simple class DB
	function query($sQuery){
		return "Ok - $sQuery";
//		return mysql_query($sQuery);
	}
}

//set option of debuging
$aOptions=array(
	'render_type'          => 'HTML',             					// Renderer type : 'HTML' or 'brut'
	'restrict_access'      => true,               					// Restrict or not the access : boolean
	'allowed_ip'           => array('127.0.0.1','192.168.40.2'),	// Authorized IP to view the debug when restrict_access is true
	'allow_url_access'     => true,               					// Allow to access the debug with a special parameter in the url
	'url_key'              => 'j_aime_la_choucroute',        		// Key for url instant access
	'url_pass'             => 'avec_une_bonne_biere',          		// Password for url instant access
        'url_unpass'           => 'sans_une_bonne_biere',          		// Password for url instant disable
	);
	
$oDB=new oDB();														//instanciation for db class
$oDB=new oDebug($oDB,$aOptions);									//instanciation for debuggin class.

/**
 * SAMPLE CODE
 */
$v=12;
$t++; 						//Warning 
$v=$v/0;					//Warning
$oDB->setTime();						//Set time inspector without name
echo $v;
is_a();						//Strict
$oDB->setTime("loop");					//Set time inspector with name
for ($i=0;$i<20000;$i++){
	$zzz.=".".$i;			//Warning the first time
}
$oDB->query("SELECT * FROM osc_countries");		//new query
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>TEST oDebug</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head><body></body></html>
<?php 
$oDB->setTime("pause"); 				//Set time inspector with name
sleep(3);

$oDB->close();				//Warning
?>

