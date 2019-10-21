<?php // $Rev: 700 $ - $Author: eduluz $ $Date: 2015-03-12 10:43:16 -0300 (qui, 12 mar 2015) $

/**
 * Base para as classes Action.
 *
 * Interface do sistema com o usuario, eh responsavel pela recepcao
 * e tratamento das requisicoes de servicos, preparacao para o atendimento,
 * chamada dos componentes responsaveis pela execucao, preparacao do
 * retorno destes e depois a geracao da resposta para o usuario.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.base.interface
 */
class BaseAction {

	/**
	 * Diretório onde reside os templates.
	 * @var string
	 */
	protected $basePath = null;

	/**
	 * Lista de ações disponíveis no Action.
	 *
	 * @var array
	 */
	protected $listaActions = array();
	protected $listaFTEActions = array();


	protected $listaItensMenu = array();

	/**
	 * Gerenciador de templates
	 *
	 * @var Smarty
	 */
	protected $smarty = null;


	/**
	 * Objeto da aplicacao gerenciadora...
	 *
	 * @var MainGama
	 */
	protected $app = null;

	/**
	 * Parâmetros vindos da requisição do usuário.
	 *
	 * @var array
	 */
	protected $parms = array();


	 /**
	  * @var mixed formatoTratamentoErroPadrao
	  */
	 protected $formatoTratamentoErroPadrao;

//--------------------------------------------

	/**
	 * Retorna o valor de formatoTratamentoErroPadrao
	 * @return int
	 */
	public function getFormatoTratamentoErroPadrao () {
		return $this->formatoTratamentoErroPadrao;
	} // eof getFormatoTratamentoErroPadrao



//--------------------------------------------

	/**
	 * Define o valor de formatoTratamentoErroPadrao
	 * @param int $formatoTratamentoErroPadrao
	 */
	public function setFormatoTratamentoErroPadrao ($formatoTratamentoErroPadrao) {
		$this->formatoTratamentoErroPadrao = $formatoTratamentoErroPadrao;
	} // eof setFormatoTratamentoErroPadrao



	function setApp($app) {
		$this->app = $app;
	}

	function setBasePath($basePath) {
		$this->basePath = $basePath;
	}


	/**
	 * Construtor da classe.
	 *
	 * @param MainGama $app
	 * @param array    $GET
	 * @param array    $POST
	 * @param string   $basePath
	 * @return BaseAction
	 */
	function __construct($app,$GET=array(),$POST=array(), $basePath = './mod') {
		/*$GET = ((is_array($GET))&&(count($GET)>0))?$GET:$_GET;
		$POST = ((is_array($POST))&&(count($POST)>0))?$POST:$_POST;*/

		$this->setBasePath($basePath);
		$this->registraAcao('showIndex');
		$this->setFormatoTrataErroGama('showIndex',MainGama::FTE_HTML);

		if (is_null(MainGama::getApp())) {
			$arr = array_merge($GET,$POST);
			foreach ($arr as $k => $v) {
				$app->setParm($k,$v);
			}
			MainGama::setApp($app);
			$this->setApp($app);
		} else {
			$this->setApp(MainGama::getApp());
			//$this->parms = array_merge($GET,$POST,MainGama::getApp()->getParms());
		}

		$this->setFormatoTratamentoErroPadrao(MainGama::FTE_JSON);
		//		$this->setRootScript('index.php');
	} // BaseAction




	/**
	 * Executa a requisição. Verifica a ação passada por parâmetro e a executa.
	 *
	 * @param array $get
	 * @param array $post
	 */
	function exec($get=array(),$post=array()) {
		$get = (count($get)==0)?$_GET:$get;
		$post = (count($post)==0)?$_POST:$post;

		$acao = $this->getApp()->getAcao();
		//		$this->getParms('acao','showIndex');



		if (array_key_exists($acao,$this->listaActions)) {
			$this->defFormatoTrataErroGama($acao);
			$metodo = $this->listaActions[$acao];
			if (is_array($metodo)) {
				$listaMetodos = $metodo;
				$listaRetornos = array();
				foreach ($listaMetodos as $metodo) {
					(MainGama::getApp()->getConfig('debug',false))?MainGama::getApp()->getDebug()->log("Executando o método $metodo",'debug'):null;
					$listaRetornos[] = $this->_exec($metodo);
				}
				return $listaRetornos;
			} else {
				(MainGama::getApp()->getConfig('debug',false))?MainGama::getApp()->getDebug()->log("Executando o método $metodo",'debug'):null;
				$resp = $this->_exec($metodo);                                
				return $resp;
			}
		} else {
			$e = new SysException("ERRO_SYS_ACAO_INEXISTENTE - Acao invalida (".$this->getApp()->getAcao().")",90);
			$e->setM(MainGama::getApp()->getM());
			throw $e;
		}
	} // exec


	protected function _exec($nomeMetodo) {
		//		$this->__preExecGeral($nomeMetodo);                
		$resultado = $this->$nomeMetodo();
		//		$this->__posExecGeral($nomeMetodo);
		return $resultado;
	}



	/**
	 * Obtém uma instância de gerenciador de templates.
	 *
	 * @return Smarty
	 */
	function getSmarty() {
		if (is_null($this->smarty)) {
			$this->smarty = new SmartyGama();
			$this->smarty->compile_check = true;
			$this->smarty->caching = false;                        
			$this->smarty->template_dir =  $this->basePath . '/template';
			$this->smarty->compile_dir  =  $this->basePath . '/template/c';
			if (!file_exists($this->smarty->compile_dir)) {
				mkdir($this->smarty->compile_dir);
			}

			$this->smarty->assign('_rootScript',$this->getApp()->getRootScript());
			$this->smarty->assign('_adm_m',$this->getApp()->getModSysPath());
		}
		return $this->smarty;
	} // eof getSmarty


	/**
	 * Exibe a pagina inicial do modulo
	 *
	 * @param array $get
	 * @param array $post
	 */
	public function showIndex($get=array(),$post=array()) {
		echo '<pre>';
		debug_print_backtrace();
		die('Error - abstract method not implemented - showIndex');
	} // eof showIndex



	/**
	 * Inclui um item de menu na action
	 *
	 * @param BaseMenu $itemMenu
	 */
	function addAction($itemMenu) {
		$this->listaItensMenu[$itemMenu->getAction()] = $itemMenu;
	}



	/**
	 * Gera e retorna o texto HTML do menu com os itens deste Action.
	 *
	 * @return string texto HTML do menu.
	 */
	function geraMenu() {
		$s = '';
		foreach ($this->listaItensMenu as $k => $v) {
			$s .= $v->geraHTML();
		}
		return $s;
	}




	/**
	 * Retorna um parâmetro vindo do request.
	 *
	 * @param string $chave
	 * @param mixed $default
	 * @return mixed
	 */
	function getParms($chave = false,$default=null) {
		return MainGama::getParms($chave,$default);
	} // eof getParms



	function getParmsInt($chave = false,$default=null) {
		$valor = MainGama::getParms($chave,$default);
		if ($chave) {
			if (isset($valor)) {
				if (is_integer($valor)) {
					return $valor;
				} else {
					return $default;
				}
			} else {
				return $default;
			}
		} else {
			return $default;
		}
	} // eof getParms







	function setParm($chave,$valor) {
		MainGama::getApp()->setParm($chave,$valor);
	}

	/**
	 * Retorna a referência à aplicação
	 *
	 * @return MainGama
	 */
	function getApp() {
		return $this->app;
	} // eof getApp


	/**
	 * Registra a ação solicitada.
	 *
	 * Existem 3 formas de fazer funcionar este registro:
	 * 1. Apenas informar o nome da ação: consiste em colocar no parâmetro
	 *    nomeAcao a string com o nome do método da Action, que será disponibilizado
	 *    para "o mundo externo"; O Gama assume automaticamente que o nome do método
	 *    é o nome da ação.
	 * 2. Informar o nome da ação e o método que a atenderá; neste caso, quando uma
	 *    requisição externa invocar esta ação, o método em questão será executado.
	 * 3. Informar o nome da ação e um conjunto de métodos que deverá ser executado
	 *    na sequência. Assim, uma ação invocada resultará na execução do grupo de
	 *    métodos constantes no array. Veja que se uma excessão for lançada em um dos
	 *    métodos intermediários, durante a execução, esta será abortada imediatamente.
	 *
	 * @param string $nomeAcao
	 * @param null|string|array $metodo
	 */
	function registraAcao($nomeAcao,$metodo=null) {
		if (is_null($metodo)) {
			$this->listaActions[$nomeAcao] = $nomeAcao;
		} else {
			$this->listaActions[$nomeAcao] = $metodo;
		}
	} // eof registraAcao




	/**
	 * Define o formato que o tratador de erros do Gama vai assumir,
	 * para exibir as mensagens de erro.
	 *
	 * @param string $nomeAction
	 * @param int $fte
	 */
	protected function setFormatoTrataErroGama($nomeAction,$fte) {
		$this->listaFTEActions[$nomeAction] = $fte;
	} // setFormatoTrataErroGama


	/**
	 * Define o formato que o Gama assumirá para o tratamento dos próximos
	 * erros que ocorrerem, para a ação em questão, se ela tiver um formato
	 * definido para ela.
	 * Se a ação não tiver um formato de tratamento padrão, então assume o
	 * formato-padrão do BaseAction (JSON).
	 *
	 * @param string $nomeAction
	 */
	protected function defFormatoTrataErroGama($nomeAction) {
		if (key_exists($nomeAction,$this->listaFTEActions)) {
			MainGama::getApp()->setFormatoTratamentoErro($this->listaFTEActions[$nomeAction]);
		} else {
			MainGama::getApp()->setFormatoTratamentoErro($this->getFormatoTratamentoErroPadrao());
		}
	} // defFormatoTrataErroGama


	/**
	 * Limpa o buffer de exibicao.
	 *
	 * @param boolean $stop indicador de que deve parar a execução
	 * @param string $texto Texto a ser exibido
	 */
	function cleanup($stop=false,$texto=null) {
		ob_end_clean();
		if (!is_null($texto)) echo $texto;
		if ($stop) exit;
	} // eof cleanup

} // eoc  BaseAction


?>