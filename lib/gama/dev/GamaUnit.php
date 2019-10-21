<?php


class GamaUnit {


	public function getListaSuites($m,$u='',$modPath='./mod') {
		$path = $modPath.'/'.$m;

		if (strlen($u)>1) {
			$path .= '/' . $u;
		}


		$path .= '/testes';
		$lsClasses = get_declared_classes();
		if (file_exists($path)) {
			$d = dir($path);
			while (false !== ($entry = $d->read())) {
				include_once($path.'/'.$entry);
			}
			$lsSuites = array_diff(get_declared_classes(),$lsClasses);
			$d->close();
		}
		return $lsSuites;
	} // getListaSuites

	public function executaSuiteTestes($m,$u='',$suite='',$modPath='./mod') {
		$this->getListaSuites($m,$u);

		$r = new ReflectionClass($suite);

		$lsMetodos = $r->getMethods();


		$lsRespostas = array();
		foreach ($lsMetodos as $metodo) {
			$nome = $metodo->name;
			$classe = $metodo->class;


			$rm = new ReflectionMethod($suite,$nome);
			$descricao = $rm->getDocComment();
			$descricao = str_replace('/*','',$descricao);
			$descricao = str_replace('*/','',$descricao);
			$descricao = str_replace('*','',$descricao);
			$i = strpos($descricao,'@');
			if ($i > 0) {
				$descricao = substr($descricao,0,$i);
			}

			$obj = new GamaTest();
			$objSuite = new $classe();
			if (in_array($nome,$objSuite->getLsMetodosTeste())) {
				try {
					$t = new $classe();
					$t->$nome();
					$obj->setResultado(false);
					$obj->setCodigo(false);
				} catch (Exception $e) {
					$obj->setResultado($e->getMessage());
					if ($e->getCode() == 0) {
						$obj->setCodigo(false);
					} else {
						$obj->setCodigo($e->getCode());
					}
				}
				if (strpos($obj->getResultado(),'success: false') !== false) {
					$obj->setCodigo('?');
				}

				$obj->setNomeMetodo($nome);
				$obj->setDescricao($descricao);
				$lsRespostas[] = $obj;
			}
		}

		return $lsRespostas;

	} // executaSuiteTestes

}



class GamaTest {

	/**
	  * @var mixed nomeMetodo
	  */
	private $nomeMetodo;

	/**
	  * @var mixed descricao
	  */
	private $descricao;

	/**
	  * @var mixed parametros
	  */
	private $parametros;

	/**
	  * @var mixed resultado
	  */
	private $resultado;

	private $codigo;

	//--------------------------------------------

	/**
	 * Retorna o valor de nomeMetodo
	 * @return mixed
	 */
	public function getNomeMetodo () {
		return $this->nomeMetodo;
	} // eof getNomeMetodo

	/**
	 * Retorna o valor de descricao
	 * @return mixed
	 */
	public function getDescricao () {
		return $this->descricao;
	} // eof getDescricao

	/**
	 * Retorna o valor de parametros
	 * @return mixed
	 */
	public function getParametros () {
		return $this->parametros;
	} // eof getParametros

	/**
	 * Retorna o valor de resultado
	 * @return mixed
	 */
	public function getResultado () {
		return $this->resultado;
	} // eof getResultado


	/**
	 * Retorna o valor de codigo
	 * @return mixed
	 */
	public function getCodigo () {
		return $this->codigo;
	} // eof getResultado



	//--------------------------------------------

	/**
	 * Define o valor de nomeMetodo
	 * @param mixed $nomeMetodo
	 */
	public function setNomeMetodo ($nomeMetodo) {
		$this->nomeMetodo = $nomeMetodo;
	} // eof setNomeMetodo

	/**
	 * Define o valor de descricao
	 * @param mixed $descricao
	 */
	public function setDescricao ($descricao) {
		$this->descricao = $descricao;
	} // eof setDescricao

	/**
	 * Define o valor de parametros
	 * @param mixed $parametros
	 */
	public function setParametros ($parametros) {
		$this->parametros = $parametros;
	} // eof setParametros

	/**
	 * Define o valor de resultado
	 * @param mixed $resultado
	 */
	public function setResultado ($resultado) {
		$this->resultado = $resultado;
	} // eof setResultado


	/**
	 * Define o valor de codigo
	 * @param mixed $codigo
	 */
	public function setCodigo ($codigo) {
		$this->codigo = $codigo;
	} // eof setCodigo


}


class GamaTeste extends BaseAction  {


	/**
	  * @var mixed lsMetodosTeste
	  */
	private $lsMetodosTeste;

	//--------------------------------------------

	/**
	 * Retorna o valor de lsMetodosTeste
	 * @return mixed
	 */
	public function getLsMetodosTeste () {
		return $this->lsMetodosTeste;
	} // eof getLsMetodosTeste



	//--------------------------------------------

	/**
	 * Define o valor de lsMetodosTeste
	 * @param mixed $lsMetodosTeste
	 */
	public function setLsMetodosTeste ($lsMetodosTeste) {
		$this->lsMetodosTeste = $lsMetodosTeste;
	} // eof setLsMetodosTeste




	function __construct($lsMetodosTeste=array()) {
		parent::__construct(MainGama::getApp(),array(),array());
		$this->setLsMetodosTeste($lsMetodosTeste);
	}

	/*
	protected function getParms($nomeParm,$default=null) {
	return MainGama::getApp()->getParms($nomeParm,$default);
	}*/
}


?>