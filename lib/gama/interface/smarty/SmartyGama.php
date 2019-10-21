<?php // $Rev: $ $Author: $ $Date: $//

class SmartyGama extends Smarty {

	protected $lsParms = array();

	public function setParm($nome,$valor=true) {
		$this->lsParms[$nome] = $valor;
	}

	public function getParm($nome,$default=null) {
		if (isset($this->lsParms[$nome])) {
			return $this->lsParms[$nome];
		} else {
			return $default;
		}
	}

	public function incContador($nomeContador) {
		if (!isset($this->lsParms[$nomeContador])) {
			$this->lsParms[$nomeContador] = 1;
		} else {
			$this->lsParms[$nomeContador]++;
		}
	}


	function __construct() {
		parent::__construct();
		$this->plugins_dir[] = './lib/Smarty/plugins';
	}


	/**
	 * Captura o display do Smarty, fazendo a verificação da existência ou
	 * não do arquivo de template;
	 *
	 * @param string $resource_name
	 * @param string $cache_id
	 * @param string $compile_id
	 */
        public function display($template = null, $cache_id = null, $compile_id = null, $parent = null){
		$this->checkResource($template);
		parent::display($template,$cache_id,$compile_id);
	} // eof display



	/**
	 * Recupera o conteúdo de um template processado.
	 *
	 * @param string $resource_name
	 * @param string $cache_id
	 * @param string $compile_id
	 * @param boolean $display
	 * @return string|boolean
	 */
        public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null){
		$this->checkResource($template);
		return parent::fetch($template, $cache_id, $compile_id, $parent);
	} // eof fetch


	/**
	 * Verifica se o template existe ou não - em caso de não existir, aborta;
	 *
	 * @param string $resource_name
	 * @throws SysException
	 */
	protected function checkResource($resource_name) {
		$path = $this->template_dir[0] . '/' . $resource_name;
		if (!file_exists($path)) {
			$e = new SysException("Template nao encontrado: $path",-1);
			throw $e;
		}

		if (!file_exists($this->compile_dir)) {
			mkdir($this->compile_dir);
		}

	} // eof checkResource




/*
	public function loadPluginAdicional($nome,$tipo,$path='./lib/gama/interface/smarty/plugins') {

	}

*/
}

?>
