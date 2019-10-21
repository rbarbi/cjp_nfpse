<?php
//
//class G3Debug  {
//
//	protected $listaLogsSQL = array();
//
//
//
//	/**
//	 * Objeto oDebug
//	 *
//	 * @var oDebug
//	 */
//	protected static $objeto;
//
//	/**
//	 * Retorna o valor de objeto
//	 * @return oDebug
//	 */
//	public function getObjeto () {
//		if (is_null(self::$objeto)) {
//			self::$objeto = new G3Debug();
//		}
//		return self::$objeto;
//	} // eof getObjeto
//
//
//
//	//--------------------------------------------
//
//	/**
//	 * Define o valor de objeto
//	 * @param oDebug $objeto
//	 */
//	public function setObjeto ($objeto) {
//		self::$objeto = $objeto;
//	} // eof setObjeto
//
//
//	public function renderHTML() {
//		self::getObjeto()->geraHTML();
//	}
//
//	public function geraHTML() {
//		print_r($this->listaLogsSQL);
//	}
//
//
//	public function addLogSQL($tipo,$log) {
//		$this->listaLogsSQL[$tipo][] = $log;
//	}
//
//
//}


/**
 * standalone class for php debug
 *
 * Features :
 * -See all used variables
 * -See all includes files
 * -See all errors in your scripts
 * -See generation page time
 *
 * With database access class (query method's name need to be "query()") features:
 * -See all db request with execution time.
 * -See sql/html&php proportions time
 *
 * @author Mathieu LAGANA
 * @copyright Mathieu LAGANA <mathieu.lagana{at}gmail.com>
 *
 * @version 0.9 20080919
 *
 */
class oDebug{


	public $idConn;

	/**
     * Tableau de résultats pour les requêtes sql
     *
     * @since 0.6 20080827
     * @var array
     */
	protected $aResultQuery=array();

	/**
     * Tableau de temps
     *
     * @since 0.9 20080919
     * @var array
     */
	protected $aResultTime=array();

	/**
     * Tableau des fichiers incluts dans la page
     *
     * @var array
     * @since 0.5 20080823
     */
	protected $aFilesRequired=array();

	/**
     * tableau des erreurs, warning, notices dans la page
     *
     * @var array
     * @since 0.8 20080823
     */
	protected $aErrors=array();

	/**
     * temps total d'exécution des requêtes sql
     *
     * @var int
     * @since 0.8 20080908
     */
	protected $timeSql=0;

	/**
	 * Nome do banco de dados.
	 *
	 * @var string
	 */
	public $databaseType;

	/**
     * Tableau des options par défaut de la classe
     *
     * @var array
     * @since 0.2 20080815
     * @update 0.7 20080903
     */
	protected $aDefaultOptions = array(

	//	$_SERVER['SERVER_ADDR']


	'render_type'          => 'HTML',             // Renderer type
	'restrict_access'      => true,               // Restrict or not the access
	'allowed_ip2'           => array('127.0.0.1'), // Authorized IP to view the debug when restrict_access is true
	'allowed_ip'           => array(), // Authorized IP to view the debug when restrict_access is true
	'allow_url_access'     => true,               // Allow to access the debug with a special parameter in the url
	'url_key'              => 'debug',        // Key for url instant access
	'url_pass'             => 'debug',          // Password for url instant access
	'url_unpass'           => 'nodebug',          // Password for url instant disable
	);

	/**
     * Tableau des options finales
     *
     * @var array
     * @since 0.2 20080815
     */
	protected $aOptionsODebug = array();

	/**
     * début et fin d'exécution du script
     *
     * @var float;
     * @since 0.1 20080908
     */
	protected $fStartTime;
	protected $fEndTime;

	/**
     * objet base de données
     *
     * @var objet
     * @since 0.6 20080827
     */
	protected $objet = null;




	/**
     * Constructor
     *
     * @param array $oObjet objet de base de données
     * @param array $aOptions
     *
     * @return void
     */
	function __construct($oObjet=null,$aOptions=array()){

		$this->objet = $oObjet;

		if (is_object($oObjet) && property_exists($oObjet,'databaseType')) {
			$this->databaseType = $oObjet->databaseType;
		}

		$this->fStartTime = $this->getMicroTime();
		$this->aOptionsODebug = array_merge($this->aDefaultOptions, $aOptions);

		error_reporting (E_ALL|E_STRICT);
		set_error_handler(array($this,'MonitoringError'),E_ALL|E_STRICT);
	}



	/**
     * call a member function
     *
     * @since 0.6 20080827
     * @var string $sName
     * @var array $aArgs
     * @update 0.9 20080919
     */
	function __call($sName, $aArgs){

		$arr = array('getarray','execute','getrow', 'getall','getone','selectlimit');
		//		error_log(var_export(array($sName,$aArgs),true),3,'sql.log');
		if (in_array(strtolower($sName),$arr)) {


			if (!empty($aArgs) ) {
				$sTypeQuery = strtoupper(trim(substr(trim($aArgs[0]), 0, 6)));
			}
			$sTypeQuery = $this->idConn;

			$query_start=microtime(true);

			$output = call_user_func_array(array(&$this->objet, $sName), $aArgs);


			if (!isset($this->aResultQuery[$sTypeQuery])){
				$this->aResultQuery[$sTypeQuery]=array();
			}

			$arrClassesExcludeTrace = array('oDebug','ADODB_Active_Record','BaseAR','BasePersistenteBO','BaseDAO','MainGama');
			$arr0 = debug_backtrace();
			$trace = '';
			foreach ($arr0 as $arr1) {
				if (isset($arr1['file'])) {
					if (!in_array($arr1['class'],$arrClassesExcludeTrace)) {
						$trace .= $arr1['file'] . ' (' . $arr1['line'] . ') ';
						$trace .= '<br>';
					}
				}
			}


			$arr1 = reset($arr0);

			$log = array(
			"query"=>$aArgs[0],
			"time_query"=>round((microtime(true) - $query_start),4),
			"memory_usage"=>function_exists ('memory_get_usage')?number_format(@memory_get_usage()/1024,2,',','.'):"-1",
			"trace"=>$trace
			);

			//			$oLog = new G3MsgLog();
			//			$oLog->setMensagem($log);
			//			$oLog->setOrigem($this->idConn);

			//			G3Debug::getObjeto()->aResultQuery[$sTypeQuery][sizeof(G3Debug::getObjeto()->aResultQuery[$sTypeQuery])]=$log;
			//			G3Debug::getObjeto()->addLogSQL($sTypeQuery,$log);
			$this->aResultQuery[$sTypeQuery][sizeof($this->aResultQuery[$sTypeQuery])]=$log;

		}elseif (strtolower($sName) === 'setTime' ) {
			$output = call_user_func_array($sName, $aArgs);
		} else {
			(MainGama::getApp()->getConfig('debug',false))?MainGama::getApp()->getDebug()->log($sName,'sName'):null;
			$output = call_user_func_array( array(&$this->objet, $sName), $aArgs);
		}
		return $output;
	}


	function defLogger3() {
		$this->fEndTime = $this->getMicroTime();
		$this->aFilesRequired = get_required_files();

		if (MainGama::getApp()->getStatus() == MainGama::ST_EXECUCAO) {

			MainGama::getApp()->getLogger3()->set('fEndTime',$this->fEndTime,$this->idConn);

			MainGama::getApp()->getLogger3()->append('aResultQuery',$this->aResultQuery,$this->idConn);
			MainGama::getApp()->getLogger3()->append('aResultTime',$this->aResultTime,$this->idConn);
			MainGama::getApp()->getLogger3()->set('aFilesRequired',get_required_files(),$this->idConn);
			MainGama::getApp()->getLogger3()->append('aErrors',$this->aErrors,$this->idConn);
		}
	}


	/**
	 * function destruct
	 *
	 * @return void
	 */
	function __destruct(){
		//		G3Debug::getObjeto()->fEndTime = G3Debug::getObjeto()->getMicroTime();
		//		G3Debug::getObjeto()->aFilesRequired = get_required_files();
		//		$this->fEndTime = $this->getMicroTime();
		//		$this->aFilesRequired = get_required_files();

		//		MainGama::getApp()->getLogger3()->set('fEndTime',$this->fEndTime,$this->idConn);
		//
		//		MainGama::getApp()->getLogger3()->append('aResultQuery',$this->aResultQuery,$this->idConn);
		//		MainGama::getApp()->getLogger3()->append('aResultTime',$this->aResultTime,$this->idConn);
		//		MainGama::getApp()->getLogger3()->set('aFilesRequired',get_required_files(),$this->idConn);
		//		MainGama::getApp()->getLogger3()->append('aErrors',$this->aErrors,$this->idConn);
		//
		$this->defLogger3();

		if ($this->allowDebug() ){
			//			G3Debug::getObjeto()->aOptionsODebug = $this->aOptionsODebug;
			switch ($this->aOptionsODebug['render_type']) {
				case 'HTML':

					//							$this->renderHTML();
					break;

				default:
					echo "<pre>".
					print_r(G3Debug::getObjeto()->aResultQuery,true).
					print_r(G3Debug::getObjeto()->aResultTime,true).
					print_r(G3Debug::getObjeto()->aFilesRequired,true).
					print_r(G3Debug::getObjeto()->aErrors,true).
					print_r($_COOKIE,true).
					print_r($_ENV,true).
					print_r($_FILES,true).
					print_r($_GET,true).
					print_r($_POST,true).
					print_r($_REQUEST,true).
					print_r($_SERVER,true).
					print_r($_SESSION,true).
					get_defined_constants(true).
					"</pre>";
					echo "Tempo de execucao: ".round( (G3Debug::getObjeto()->fEndTime - G3Debug::getObjeto()->fStartTime),4);
					/*echo "<pre>".
					print_r($this->aResultQuery,true).
					print_r($this->aResultTime,true).
					print_r($this->aFilesRequired,true).
					print_r($this->aErrors,true).
					print_r($_COOKIE,true).
					print_r($_ENV,true).
					print_r($_FILES,true).
					print_r($_GET,true).
					print_r($_POST,true).
					print_r($_REQUEST,true).
					print_r($_SERVER,true).
					print_r($_SESSION,true).
					get_defined_constants(true).
					"</pre>";
					echo "Tempo de execucao: ".round( ($this->fEndTime - $this->fStartTime),4);*/
					break;
			}
		}
	}


	/**
  *********************************************************
  *********************************************************
  *********************************************************
  ********          FONCTIONS PRIVEES       ***************
  *********************************************************
  *********************************************************
  *********************************************************
  */

	/**
	 * add time inspector
	 *
	 * @param string $sName
	 * @since 0.9 20080919
	 */
	public function setTime($sName=""){
		$this->aResultTime[]=array("time"=>$this->getMicroTime(),"name"=>$sName);
	}

	/**
     * will be output done ??
     *
     * @return boolean
     */
	protected function allowDebug(){
		$key = $this->aOptionsODebug['url_key'];
		if ((isset($_GET[$key])) && ($_GET[$key] == $this->aOptionsODebug['url_unpass'])) {
			return false;
		}
		if ($this->aOptionsODebug['restrict_access'] === true) {

			// Check if client IP is among the allowed ones
			if ((isset($this->aOptionsODebug['allowed_ip'])) && in_array($_SERVER['REMOTE_ADDR'],$this->aOptionsODebug['allowed_ip'])) {
				return true;
			} else if ($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR']) {
				return true;
			}
			// Check if instant access is allowed and test key and password
			elseif ($this->aOptionsODebug['allow_url_access'] == true) {


				if (!empty($_GET[$key])) {
					if ($_GET[$key] == $this->aOptionsODebug['url_pass']) {
						return true;
					} else {
						return false;
					}
				}
				else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			// Access is not restricted
			return true;
		}
	}

	/**
     * Return microtime from a timestamp
     *
     * @return float  Microtime of timestamp param
     * @since 0.1 20080908
     */
	protected  static function getMicroTime(){
		list($usec, $sec) = explode(' ', microtime());
		return (float)$usec + (float)$sec;
	}

	/**
     * MonitoringError
     *
     * @return boolean
     * @since 0.1 20080908
     */
	public function MonitoringError($iNumError, $sMsgError, $sScript, $iLine){

		$aBackTrace = debug_backtrace();
		$this->WriteTrace($aBackTrace, $iNumError, $sMsgError, $sScript, $iLine);
		return true;
	}

	/**
     * Log errors
     *
     * @return void
     * @since 0.1 20080908
     */
	protected function WriteTrace($aBackTrace, $iNumError, $sMsgError, $sScript, $iLine){

		$iError=sizeof($this->aErrors);
		$this->aErrors[$iError]["date"]=@date("d/m/Y H:i:s");
		$this->aErrors[$iError]["line"]=$iLine;
		if (isset($aBackTrace[$iError]["function"])){
			$Function = $aBackTrace[$iError]["function"];
			$this->aErrors[$iError]["function"]=$Function;
		}
		$this->aErrors[$iError]["erreur"] =$sMsgError;
		$this->aErrors[$iError]["num_error"] = $iNumError;
		$this->aErrors[$iError]["sScript"] = $sScript;
		//  $this->aErrors[$iError]["aBackTrace"] = $aBackTrace;

	}


	public function renderHTML() {

	}

	/**
     * HTML output generator
     *
     * @return HTML output
     * @since 0.6 20080827
     */
	public function renderHTML2(){
		$sOutPut=<<<eos
<div id="mainDebug" style="padding: 0;margin: 0;font-family: Arial, sans-serif;font-size: 12px;color: #333333;text-align:left;line-height: 12px;display:block;">
  <div style="position:absolute; margin: 0;padding: 1px 5px;right: 0px;top: 0px;opacity: 0.80;filter: alpha(opacity:80);z-index: 10000; background-color:#DDDDDD;display:block;height:20px;">
    <div style="float:left;text-align:center;display:block;" >
    	<a onclick="if (document.getElementById('menuDebug').style.display=='inline'){document.getElementById('menuDebug').style.display='none';document.getElementById('paletteLog').style.display='none';document.getElementById('paletteConfig').style.display='none';document.getElementById('paletteTime').style.display='none';document.getElementById('paletteSQL').style.display='none';}else{document.getElementById('menuDebug').style.display='inline';}; return false;" href="#" style="color:#000000;text-decoration:none;"> <b>>> oDebug</b> </a> </div>
    <ul style="display:none;padding:5px;margin-right:7px;-moz-padding-start:40px;list-style-type:disc;margin:1em 0;" id="menuDebug">
      <li style="border-right:1px solid #AAAAAA;display:inline;list-style-image:none;list-style-position:outside;list-style-type:none;margin:0;padding:0 5px;"> <a onclick="if (document.getElementById('paletteConfig').style.display=='inline'){document.getElementById('paletteConfig').style.display='none';}else{document.getElementById('paletteConfig').style.display='inline';document.getElementById('paletteTime').style.display='none';document.getElementById('paletteLog').style.display='none';document.getElementById('paletteSQL').style.display='none';}; return false;" href="#" style="color:#000000;text-decoration:none;"> vars & config</a></li>
      <li style="border-right:1px solid #AAAAAA;display:inline;list-style-image:none;list-style-position:outside;list-style-type:none;margin:0;padding:0 5px;"> <a onclick="if (document.getElementById('paletteLog').style.display=='inline'){document.getElementById('paletteLog').style.display='none';}else{document.getElementById('paletteLog').style.display='inline';document.getElementById('paletteConfig').style.display='none';document.getElementById('paletteTime').style.display='none';document.getElementById('paletteSQL').style.display='none';}; return false;" href="#" style="color:#000000;text-decoration:none;"> logs & msgs</a></li>
      <li style="border-right:1px solid #AAAAAA;display:inline;list-style-image:none;list-style-position:outside;list-style-type:none;margin:0;padding:0 5px;"> <a onclick="if (document.getElementById('paletteSQL').style.display=='inline'){document.getElementById('paletteSQL').style.display='none';}else{document.getElementById('paletteSQL').style.display='inline';document.getElementById('paletteConfig').style.display='none';document.getElementById('paletteLog').style.display='none';document.getElementById('paletteTime').style.display='none';}; return false;" href="#" style="color:#000000;text-decoration:none;"> SQL</a></li>
      <li style="display:inline;list-style-image:none;list-style-position:outside;list-style-type:none;margin:0;padding:0 5px;"> <a onclick="if (document.getElementById('paletteTime').style.display=='inline'){document.getElementById('paletteTime').style.display='none';}else{document.getElementById('paletteTime').style.display='inline';document.getElementById('paletteConfig').style.display='none';document.getElementById('paletteLog').style.display='none';document.getElementById('paletteSQL').style.display='none';}; return false;" href="#" style="color:#000000;text-decoration:none;"> Time</a> </li>
    </ul>
    <a onclick="document.getElementById('mainDebug').style.display='none'; return false;" href="#" style="color:#FF0000;text-decoration:none;font-weight:bold;font-size:20px"> X </a> </div>
  <div id="paletteConfig"  style="background-color:#EFEFEF; border-bottom:1px solid #AAAAAA;left:0;padding:10px;position:absolute;top:0;width:98%;z-index:9999;display: none;line-height:normal">{$this->renderPaletteConfig()}</div>
  <div id="paletteLog" style="background-color:#EFEFEF; border-bottom:1px solid #AAAAAA;left:0;padding:10px;position:absolute;top:0;width:98%;z-index:9999;display: none;line-height:normal"> {$this->renderPaletteLog()}</div>
  <div id="paletteSQL" style="background-color:#EFEFEF; border-bottom:1px solid #AAAAAA;left:0;padding:10px;position:absolute;top:0;width:98%;z-index:9999;display: none;line-height:normal"> {$this->renderPaletteSQL()}</div>
  <div id="paletteTime" style="background-color:#EFEFEF; border-bottom:1px solid #AAAAAA;left:0;padding:10px;position:absolute;top:0;width:98%;z-index:9999;display: none;line-height:normal"> {$this->renderPaletteTime()}</div>
</div>

eos;

		echo $sOutPut;
	}

	/**
     * Vars & config generator
     *
     * @return string
     * @since 0.6 20080827
     */
	protected function renderPaletteConfig(){
		$sOutput='<span style="font-size:20px;">Variables & Config</span><br/><br/>';
		$aVars=array(	'COOKIES'=>(isset($_COOKIE)) ? $_COOKIE : array("no cookie"),
		'ENV'=>array_merge($_ENV,array("php Version"=>phpversion()),ini_get_all()),
		'FILES'=>$_FILES,
		'GET'=>$_GET,
		'POST'=>$_POST,
		'SERVER'=>$_SERVER,
		'SESSION'=>(isset($_SESSION)) ? $_SESSION : array("no session"),
		'CONSTANTS'=>get_defined_constants(true));
		foreach ($aVars as $var=>$aVar){
			$sOutput.='<span style="font-size:12px;font-weight:bold"><a onclick="if (document.getElementById(\'paletteConfig'.$var.'\').style.display==\'block\'){document.getElementById(\'paletteConfig'.$var.'\').style.display=\'none\';}else{document.getElementById(\'paletteConfig'.$var.'\').style.display=\'block\';}; return false;" href="#" style="color:#000000;text-decoration:none;">'.$var.' : </a></span><table border="0" cellspacing="4" style="font-size:10px;display:none;" id="paletteConfig'.$var.'">';
			foreach ( $aVar as $k=>$v){
				$sOutput.= "<tr style=\"height:10px\"><td><b style=\"font-size:11px\">".$k."</b></td><td>=></td><td><pre>".print_r($v,true)."</pre></td></tr>";
			}
			$sOutput.="</table><br/>";
		}

		return $sOutput;
	}

	/**
     * Logs HTML generator
     *
     * @return string
     * @since 0.6 20080827
     */
	protected function renderPaletteLog(){
		$sOutput='<span style="font-size:20px;">Php Errors</span><br/><br/>';
		$sOutput.=<<<eos
    <table>
    <tr style="font-size:10px;">
        <td style="width:15px;background-color:#FFB32F;"></td><td style="width:100px">Warning</td>
        <td style="width:15px;background-color:#8FB8FF;"></td><td style="width:100px">Strict</td>
        <td style="width:15px;background-color:#FDFF1F;"></td><td style="width:100px">Notice</td>
        <td style="width:15px;background-color:#FFFFFF;"></td><td style="width:100px">Ok</td>
        </tr>
    </table>
	<table width="100%" border="1" cellspacing="0" cellpadding="2" summary="logs">
	  <tr>
	    <td></td>
	    <td><div align="center"><strong>After</strong></div></td>
	    <td><div align="center"><strong>File</strong></div></td>
	    <td><div align="center"><strong>Line</strong></div></td>
	    <td><div align="center"><strong>Function</strong></div></td>
	    <td><div align="center"><strong>Message</strong></div></td>
	  </tr>
eos;

		foreach ($this->aFilesRequired as $k=>$v){
			$sOutput.=<<<eos
            <tr style="font-size:10px;">
			    <td></td>
			    <td>Inclued file</td>
			    <td>$v</td>
			    <td></td>
			    <td></td>
			    <td></td>
		    </tr>
eos;

}

foreach ($this->aErrors as $k=>$v){
	switch ($v['num_error']){
		case 1:
			$sColor="FF5F65";
			break;
		case 2:
			$sColor="FFB32F";
			break;
		case 2048:
			$sColor="8FB8FF";
			break;
		case 8:
			$sColor="FDFF1F";
			break;
		default:
			$sColor="FFFFFF";
			break;

	}
	$temps=round( ($this->getMicroTime() - $this->fStartTime),4);
	if($k===0){$v['function']="";}
	$sOutput.=<<<eos
		        <tr style="font-size:10px;background-color:#$sColor;">
		            <td>$k</td>
		            <td>$temps ms</td>
		            <td>{$v['sScript']}</td>
		            <td>{$v['line']}</td>
		            <td>{$v['function']}</td>
		            <td>{$v['erreur']}</td>
		         </tr>
eos;

}

$sOutput.="</table>";
return $sOutput;
}


/**
     * SQL logs HTML generator
     *
     * @return string
     * @since 0.6 20080827
     */
protected function renderPaletteSQL(){
	$sOutput='<span style="font-size:20px;">SQL queries</span><br/><br/>';
	$sOutput.=<<<eos

		<table width="100%" border="1" cellspacing="0" cellpadding="2" summary="logs">
eos;

	foreach ($this->aResultQuery as $k=>$v){
		$sOutput.=<<<eos
        <tr style="font-size:12px;">
            <td colspan="3"><b>{$k}</b></td>
         </tr>
         <tr style="font-size:10px;">
            <td><div align="center"><strong>Queries</strong></div></td>
            <td><div align="center"><strong>Exec time</strong></div></td>
            <td><div align="center"><strong>Memory usage</strong></div></td>
            <td><div align="center"><strong>Trace</strong></div></td>
          </tr>
eos;
		foreach ($v as $vv){
			$sOutput.=<<<eos

          <tr style="font-size:10px;">
            <td>{$vv['query']}</td>
            <td>{$vv['time_query']} ms</td>
            <td>{$vv['memory_usage']} kb</td>
            <td>{$vv['trace']}</td>
         </tr>
eos;

			$this->timeSql+=$vv['time_query'];
		}
	}

	$sOutput.="</table>";
	return $sOutput;
}


/**
     * Time logs HTML generator
     *
     * @return string
     * @since 0.6 20080827
     */
protected function renderPaletteTime(){
	$totalTime=round( ($this->fEndTime - $this->fStartTime),4);
	$phpTime=$totalTime-$this->timeSql;
	$percentphp=round($phpTime/$totalTime*100,2);
	$percentsql=100-$percentphp;
	$sOutput='<span style="font-size:20px;">Timer</span><br/><br/>';
	$sOutput.=<<<eos

		<table width="50%" border="1" cellspacing="0" cellpadding="2" summary="logs" style="font-size:10px;">
		 <tr style="font-size:12px;">
		    <td><div align="center"><strong>Type</strong></div></td>
		    <td><div align="center"><strong>Elapsed time (ms)</strong></div></td>
		    <td><div align="center"><strong>Percent</strong></div></td>
		  </tr>
		  <tr>
		    <td><div align="center">Global</div></td>
		    <td><div align="center">$totalTime</div></td>
		    <td><div align="center">100 %</div></td>
		  </tr>
		  <tr>
		    <td><div align="center">PHP/HTML</div></td>
		    <td><div align="center">$phpTime</div></td>
		    <td><div align="center">$percentphp %</div></td>
		  </tr>
		  <tr>
		    <td><div align="center">SQL</div></td>
		    <td><div align="center">{$this->timeSql}</div></td>
		    <td><div align="center">$percentsql %</div></td>
		  </tr>
		  </table>
		  <br />
		  <table width="50%" border="1" cellspacing="0" cellpadding="2" summary="logs" style="font-size:10px;">
		 <tr style="font-size:12px;">
		    <td><div align="center"><strong>Code Part</strong></div></td>
		    <td><div align="center"><strong>Exec time</strong></div></td>
		  </tr>
eos;

	foreach ($this->aResultTime as $k=>$v){
		if($k===sizeof($this->aResultTime)-1){
			$iTime=round( ($this->fEndTime - $v['time']),4);
		}else{
			$iTime=round( ($this->aResultTime[$k+1]['time'] - $v['time']),4);
		}
		$sName=(empty($v['name']))?$k+1:$v['name'];
		$sOutput.=<<<eos
		 <tr style="font-size:12px;">
		    <td><div align="center"><strong>$sName</strong></div></td>
		    <td><div align="center"><strong>$iTime ms</strong></div></td>
		  </tr>
eos;


}$sOutput.="</table>";

return $sOutput;
}






}