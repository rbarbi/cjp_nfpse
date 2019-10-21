<?php // $Rev: 361 $ $Author: eduluz $ $Date: 2009-04-03 09:52:04 -0300 (sex, 03 abr 2009) $//



/**
 * Classe que gerencia o log de mensagens em tempo de execução
 *
 */
class G3Logger extends oDebug {

	protected static $jaEnviou = true;
	protected $lsLogs = array();

	public function addLog($log) {
		$item = new G3MsgLog();
		$item->setMensagem($log);
		$this->lsLogs[] = $item;
	}

	function set($nomeVar,$valor,$idConn='-') {

//		if (isset($this->$nomeVar)) {
			$this->$nomeVar = $valor;
//		} else {
//			die ("Propriedade inexistente: " . $nomeVar);
//		}
	}

	function get($nomeVar,$valorDefault=null,$idConn='-') {

		if (isset($this->$nomeVar)) {
			return $this->$nomeVar;
		} else {
			return $valorDefault;
		}
	}

	function append($nomeVar,$array,$idConn='-') {
//		echo "<br> ( $idConn ) Inserindo " . count($array) . " itens em $nomeVar ";
		$this->set($nomeVar,array_merge($this->get($nomeVar),$array));
//		echo " - agora são " . count($this->get($nomeVar)) . " itens...";
	}


	/**
     * HTML output generator
     *
     * @return HTML output
     * @since 0.6 20080827
     */
	public function renderHTML(){

		if ($this->allowDebug()) {
		$sOutPut=<<<eos
<div id="mainDebug" style="padding: 0;margin: 0;font-family: Arial, sans-serif;font-size: 12px;color: #333333;text-align:left;line-height: 12px;display:block;">
  <div style="position:absolute; margin: 0;padding: 1px 5px;right: 0px;top: 0px;opacity: 0.80;filter: alpha(opacity:80);z-index: 10000; background-color:#DDDDDD;display:block;height:20px;">
    <div style="float:left;text-align:center;display:block;" >
    	<a onclick="if (document.getElementById('menuDebug').style.display=='inline'){document.getElementById('menuDebug').style.display='none';document.getElementById('paletteLog').style.display='none';document.getElementById('paletteConfig').style.display='none';document.getElementById('paletteTime').style.display='none';document.getElementById('paletteSQL').style.display='none';}else{document.getElementById('menuDebug').style.display='inline';}; return false;" href="#" style="color:#000000;text-decoration:none;"><b>>>Dbg-G3</b></a></div>
    <ul style="display:none;padding:5px;margin-right:7px;-moz-padding-start:40px;list-style-type:disc;margin:1em 0;" id="menuDebug">
      <li style="border-right:1px solid #AAAAAA;display:inline;list-style-image:none;list-style-position:outside;list-style-type:none;margin:0;padding:0 5px;"> <a onclick="if (document.getElementById('paletteGama3').style.display=='inline'){document.getElementById('paletteGama3').style.display='none';}else{document.getElementById('paletteGama3').style.display='inline';document.getElementById('paletteTime').style.display='none';document.getElementById('paletteConfig').style.display='none';document.getElementById('paletteLog').style.display='none';document.getElementById('paletteSQL').style.display='none';}; return false;" href="#" style="color:#000000;text-decoration:none;">Gama3</a></li>
      <li style="border-right:1px solid #AAAAAA;display:inline;list-style-image:none;list-style-position:outside;list-style-type:none;margin:0;padding:0 5px;"> <a onclick="if (document.getElementById('paletteConfig').style.display=='inline'){document.getElementById('paletteConfig').style.display='none';}else{document.getElementById('paletteConfig').style.display='inline';document.getElementById('paletteGama3').style.display='none';document.getElementById('paletteTime').style.display='none';document.getElementById('paletteLog').style.display='none';document.getElementById('paletteSQL').style.display='none';}; return false;" href="#" style="color:#000000;text-decoration:none;"> vars & config</a></li>
      <li style="border-right:1px solid #AAAAAA;display:inline;list-style-image:none;list-style-position:outside;list-style-type:none;margin:0;padding:0 5px;"> <a onclick="if (document.getElementById('paletteLog').style.display=='inline'){document.getElementById('paletteLog').style.display='none';}else{document.getElementById('paletteLog').style.display='inline';document.getElementById('paletteGama3').style.display='none';document.getElementById('paletteConfig').style.display='none';document.getElementById('paletteTime').style.display='none';document.getElementById('paletteSQL').style.display='none';}; return false;" href="#" style="color:#000000;text-decoration:none;"> logs & msgs</a></li>
      <li style="border-right:1px solid #AAAAAA;display:inline;list-style-image:none;list-style-position:outside;list-style-type:none;margin:0;padding:0 5px;"> <a onclick="if (document.getElementById('paletteSQL').style.display=='inline'){document.getElementById('paletteSQL').style.display='none';}else{document.getElementById('paletteSQL').style.display='inline';document.getElementById('paletteGama3').style.display='none';document.getElementById('paletteConfig').style.display='none';document.getElementById('paletteLog').style.display='none';document.getElementById('paletteTime').style.display='none';}; return false;" href="#" style="color:#000000;text-decoration:none;"> SQL</a></li>
      <li style="display:inline;list-style-image:none;list-style-position:outside;list-style-type:none;margin:0;padding:0 5px;"> <a onclick="if (document.getElementById('paletteTime').style.display=='inline'){document.getElementById('paletteTime').style.display='none';}else{document.getElementById('paletteTime').style.display='inline';document.getElementById('paletteGama3').style.display='none';document.getElementById('paletteConfig').style.display='none';document.getElementById('paletteLog').style.display='none';document.getElementById('paletteSQL').style.display='none';}; return false;" href="#" style="color:#000000;text-decoration:none;"> Time</a> </li>
      <li style="display:inline;list-style-image:none;list-style-position:outside;list-style-type:none;margin:0;padding:0 5px;"> &nbsp;&nbsp;&nbsp;&nbsp; </li>
    </ul>
    <a onclick="document.getElementById('mainDebug').style.display='none'; return false;" href="#" style="color:#FF0000;text-decoration:none;font-weight:bold;font-size:20px"> X </a> </div>
  <div id="paletteGama3"  style="background-color:#EFEFEF; border-bottom:1px solid #AAAAAA;left:0;padding:10px;position:absolute;top:0;width:98%;z-index:9999;display: none;line-height:normal">{$this->renderPaletteGama3()}</div>
  <div id="paletteConfig"  style="background-color:#EFEFEF; border-bottom:1px solid #AAAAAA;left:0;padding:10px;position:absolute;top:0;width:98%;z-index:9999;display: none;line-height:normal">{$this->renderPaletteConfig()}</div>
  <div id="paletteLog" style="background-color:#EFEFEF; border-bottom:1px solid #AAAAAA;left:0;padding:10px;position:absolute;top:0;width:98%;z-index:9999;display: none;line-height:normal"> {$this->renderPaletteLog()}</div>
  <div id="paletteSQL" style="background-color:#EFEFEF; border-bottom:1px solid #AAAAAA;left:0;padding:10px;position:absolute;top:0;width:98%;z-index:9999;display: none;line-height:normal"> {$this->renderPaletteSQL()}</div>
  <div id="paletteTime" style="background-color:#EFEFEF; border-bottom:1px solid #AAAAAA;left:0;padding:10px;position:absolute;top:0;width:98%;z-index:9999;display: none;line-height:normal"> {$this->renderPaletteTime()}</div>
</div>

eos;

		echo $sOutPut;
		self::$jaEnviou = true;
		}
	}



	public function renderPaletteGama3() {
		$s = '<table border=1>';
		foreach ($this->lsLogs as $item) {
			$s .= "<tr style='font-size:10px;'>";
			$s .= "<td>";
			$s .= $item->getDhEvento();
			$s .= "<td>";
			$s .= "<td>";
			$s .= $item->getMensagem();
			$s .= "<td>";
			$s .= "<td><pre>";
			$s .= $item->getTrace();
			$s .= "</pre><td>";
			$s .= "</tr>";
		}
		$s .= '</table>';
		return $s;
	}



}

?>