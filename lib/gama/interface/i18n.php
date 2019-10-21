<?php // $Rev: 327 $ - $Author: eduluz $ $Date: 2009-02-03 13:20:59 -0200 (ter, 03 fev 2009) $//

/**
 * Classe destinada a gerenciar e operacionalizar as tarefas de
 * internacionalizaчуo, como a exibiчуo de mensagens na lingua
 * definida como a padrуo.
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.interface.i18n
 */
class I18N {


	/**
	  * @var array s
	  */
	public $s = array();

	/**
	  * @var string locale
	  */
	private $locale = 'pt_BR';


	/**
	 * Construtor da classe.
	 *
	 * @param MainGama $app
	 * @return I18N
	 */
	public function I18N(&$app=null) {
		if (is_null($app)) {
			$app &= MainGama::getApp();
		}
		$this->setLocale($app->getConfig('locale'));
		$this->loadGlobalMessages($app->getPathArqConf());
		$this->loadModuleMessages($app->getModPath(),$app->getM());
		if (!is_null($app->getU())) {
			$this->loadSubModuleMessages();
		}
	} // eof I18N


	/**
	 * Retorna o valor do termo identificado pela chave passada por parтmetro.
	 * @return mixed
	 */
	public function getS ($key=false) {
		if ($key) {
			if (isset($this->s[$key])) {
				return $this->s[$key];
			} else {
				return $key;
			}
		} else {
			return $this->s;
		}
	} // eof getS


	/**
	 * Retorna o valor de locale
	 * @return mixed
	 */
	public function getLocale () {
		return $this->locale;
	} // eof getLocale


	//--------------------------------------------

	/**
	 * Define o valor de s
	 * @param string|array $k
	 * @param string|boolean $s
	 */
	public function setS ($k,$v=false) {
		if ($v) {
			$this->s[$k] = $v;
		} else {
			$this->s = $k;
		}
	} // eof setS


	/**
	 * Define o valor de locale
	 * @param mixed $locale
	 */
	public function setLocale ($locale) {
		$this->locale = $locale;
	} // eof setLocale





	protected function loadGlobalMessages($path) {
		$path .= 'i18n/' . $this->getLocale().'/system.ini';
		if (file_exists($path)) {
			$arr = parse_ini_file($path);
			$this->merge($arr);
		}
	} // eof loadGlobalMessages



	protected function loadModuleMessages() {

	}


	protected function loadSubModuleMessages() {

	}




	function merge($arr) {
		$this->s = array_merge($this->getS(),$arr);
		//		print_r($arr);
	}


	public function _($id = null) {
		if (is_null($id)) {
			return '';
		}
		if (is_bool($id)) {
			switch ($id) {
				case false : return $this->getS('false');
				case true  : return $this->getS('true');
			}
		}
		$x = $this->getS($id);

		if (is_array($x)) {
			return $id;
		} else {
			return $this->getS($id);
		}
	} // eof _


} // eoc I18N


?>