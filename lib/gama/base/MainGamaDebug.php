<?php // $Rev: $ $Author: $ $Date: $//

/**
 * Classe de debug
 *
 */
class GamaDebugBase {

	function __construct() {

	}

	/**
	 * @var FirePHP
	 */
	private $debug;

	protected $status;


	protected $lsLogs = array();




	/**
	 * Retorna o valor de debug
	 * @return FirePHP
	 */
	public function getDebug () {
		return $this->debug;
	} // eof getDebug



//--------------------------------------------

	/**
	 * Define o valor de debug
	 * @param FirePHP $debug
	 */
	public function setDebug ($debug) {
		$this->debug = $debug;
	} // eof setDebug


	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
	}


	public function preExecAction($m,$u,$a,$acao,$parms) {	}

	public function posExecAction($parms) {}

	public function log($msg,$titulo='log',$pre="") {}

	protected function _log($dados,$titulo,$meta=array()) {
		if ($this->getStatus() === false) {
			return false;
		}

		$trace = debug_backtrace();
		$reg = reset($trace);
		$reg = next($trace);

		if (count($meta) == 0) {
			$arquivo = isset($reg['file'])?$reg['file']:'';
			$linha = isset($reg['line'])?$reg['line']:'';
			$time = date('H:i:s.') . ceil(MainGama::getmtime() / 1000);
			$meta['time'] = $time;
			$meta['linha'] = $linha;
			$meta['arquivo'] = $arquivo;
		}
		$this->lsLogs[] = array($dados,$titulo,$meta);

		if (is_array($dados)) {
			$dados['_meta_'] = $meta;
			$this->getDebug()->log($dados,$titulo);
		} else {
			$this->getDebug()->log(array($dados,'_meta_' => $meta),$titulo);
		}
	} // eof _log



	public function resetLsMsgDebug() {
		$this->lsLogs = array();
	} // eof resetLsMsgDebug



	public function getLsMsgDebug($clear = true) {
		$resposta = $this->lsLogs;
		if ($clear) {
			$this->resetLsMsgDebug();
		}
		return $resposta;
	} // eof getLsMsgDebug



	public function setLsMsgDebug($lsLogs) {
		foreach ($lsLogs as $log) {
			$this->_log($log[0],$log[1],$log[2]);
		}
	} // eof setLsMsgDebug


} // eoc GamaDebugBase










class GamaFireDebug extends GamaDebugBase {

	function __construct() {
		$this->setDebug(FirePHP::getInstance(true));
	}


	public function preExecAction($m,$u,$a,$acao,$parms) {
		unset($parms['m']);
		unset($parms['u']);
		unset($parms['a']);
		unset($parms['acao']);
		$dados = array('m'=>$m,'u'=>$u,'a'=>$a,'acao'=>$acao,'parms'=>$parms);
		$this->_log($dados,'preExecAction');
	}

	public function posExecAction($parms) {
		$this->_log($parms,'posExecAction');
	}

	public function log($msg,$titulo='log',$pre="") {
		if ($this->getStatus() === false) { // já abreviei para diminuir trabalho (do _log).
			return;
		}
/*		$id = sprintf("%05d",rand(1,100000));
		$texto = $pre.  "\n". date('Y-m-d h:i:s') . ' [' . $id . '] - ('. $titulo . ') = '  . var_export($msg,true);

		ob_start();
		debug_print_backtrace();
		$s = ob_get_contents();
		ob_end_clean();
		$texto2 = "\n\n--------------------------------------------------------\n[{$id}] - " . $s;
*/
//		error_log($texto,3,'./log/gama3.log');
//		error_log($texto2,3,'./log/gama3_backtrace.log');
		$this->_log($msg,$titulo);
	}

} // eoc GamaFireDebug


?>