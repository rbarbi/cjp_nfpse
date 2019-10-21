<?php // $Rev: 84 $ $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $



class GamaManager {

	/**
	  * @var mixed app 
	  */
	protected $app;

	//--------------------------------------------

//	/**
//	 * Retorna o valor de app
//	 * @return mixed
//	 */
//	public function getApp () {
//		return $this->app;
//	} // eof getApp
//
//
//
//	//--------------------------------------------
//
//	/**
//	 * Define o valor de app
//	 * @param mixed $app
//	 */
//	public function setApp ($app) {
//		$this->app = $app;
//	} // eof setApp



	function GamaManager($app) {
		$this->app = $app;
	}

	function __call($sName, $aArgs){
//		$arr = array('getarray','execute','getrow', 'getall');
//		if (in_array(strtolower($sName),$arr)) {
//			
//		}
		$s = " ( $sName )";
		error_log("\n\n $s",3,'debug.log');
		$saida = call_user_func_array(array(&$this->app, $sName), $aArgs);		
		$trace = debug_backtrace();
		$arr = array();
		foreach ($trace as $item) {
			if (isset($item['class'])) {
				$arr[] = $item['class'] . '.' . $item['function'] . ' ('.$item['line'].') ';
			}
		}
		error_log("\n".join($arr,"\n"),3,'debug.log');		
		return $saida;
	}


}



?>