<?php // $Rev: 689 $ - $Author: eduluz $ $Date: 2014-01-29 14:51:05 -0200 (qua, 29 jan 2014) $

$__versao_minima__ = '5.2';
if (version_compare(phpversion(),$__versao_minima__)<=0) {
	die('Vers&atilde;o incorreta. Minima = '.$__versao_minima__.'  /  Atual = '.phpversion() );
}

define('GM_TIPO_DIALOGO_NORMAL',1);
define('GM_TIPO_DIALOGO_MINIMA',2);
define('GM_TIPO_DIALOGO_RAW',	3);
define('GM_WEB_SERVICE',	4);

define('NUMERO_MAXIMO_SESSOES_POR_USUARIO','num_max_sessoes_usuario');
define('TEMPO_MAXIMO_SESSAO_POR_USUARIO','duracao_sessao');

define('INDICADOR_DEBUG_REMOTO','sys_debug_remoto_ativo');
define('REGISTRO_DEBUG_REMOTO','sys_transacao_debug_remoto');
/**
 * Classe principal, responsavel pela gerencia das requisicoes e atendimento das mesmas,
 * dando suporte as camadas inferiores do sistema.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas LTDA
 * @package gama3.main
 */
class MainGama {

    
	/**
	 * Vetor contendo os parametros de configuracao
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * String contendo a path do arquivo de configuracao, incluindo o
	 * nome do arquivo.
	 *
	 * @var string
	 */
	private $pathArqConf = "sys/";

	/**
	 * Diretorio onde se encontram os modulos.
	 *
	 * @var string
	 */
	private $modPath = "mod/";


	/**
	 * Raiz onde se encontra a aplica��o.
	 *
	 * @var string
	 */
	private $rootPath = '.';

	/**
	 * Nome do arquivo de inicializa��o padr�o que ser� procurado pelo
	 * Gama quando na inicaliza��o de um m�dulo.
	 *
	 * @var string
	 */
	protected $nomeINI = 'AutoExec.ini';

	/**
	 * @var G3Logger
	 */
	protected $g3Logger;


	/**
	 * Atributo que contem os dados da sessao
	 *
	 * @var SessionGama
	 */
	private $sess = null;

	/**
	 * Conexao com o banco de dados
	 *
	 * @var NewADOConnection
	 */
	private $conn;

	/**
	 * Mensagem da aplicacao que e' preenchido pelas camadas de servico/DAO
	 *
	 * @var Msg
	 */
	private $msg;

	/**
	 * Instancia do gerenciador de templates Smarty.
	 *
	 * @var Smarty
	 */
	private $smarty;


	/**
	  * Objeto gerenciador de mensagens internacionalizadas.
	  *
	  * @var I18N i18n
	  */
	private $i18n;


	/**
	 * Lista de conex�es auxiliares, usados quando � necess�rio acessar bancos de dados
	 * diferentes.
	 *
	 * @var array
	 */
	private $listaConexoes = array();


	const CONECTADO = "conectado";

	const REDIRECT = "clsb3DoRedirect";



	/**
	 * Refer�ncia ao Application - inst�ncia de MainGama 'global' para uma
	 * requisi��o.
	 *
	 * @var MainGama
	 */
	protected static $app;

	protected static $idRequest;



	/**
	 * Atributo est�tico que mant�m a inst�ncia do debugger
	 * usado para permitir o debug das aplica��es.
	 *
	 * @var GamaDebugBase
	 */
	protected static $debugger;



	/**
	 * Retorna a inst�ncia do obtejo de debug (baseado no FirePHP).
	 * @param boolean $force
	 * @return GamaDebugBase
	 */
	public function getDebug ($force=false) {
		if (!isset($this->debug) || is_null($this->debug)) {
			MainGama::$debugger = new GamaFireDebug();
		}
		MainGama::$debugger->setStatus($force || $this->getConfig('debug',false));
		return MainGama::$debugger;
	} // eof getDebug



	//--------------------------------------------

	/**
	 * Define o valor de debug
	 * @param FirePHP $debug
	 */
	public function setDebug ($debug) {
		$this->debug = $debug;
	} // eof setDebug


	/**
	 * Vari�vel est�tica que mant�m os par�metros que ser�o repassados para
	 * a pr�xima inst�ncia a ser executada pelo index...
	 *
	 * @var array
	 */
	public static $BASE_PARMS_REDIR = null;





	/**
	 * Nome do script que responde a todas as requisi��es (ex. index.php).
	 * @var string rootScript
	 */
	protected $rootScript;



	/**
	  * @var mixed logger
	  */
	private $logger;




	/**
	  * Parametros adicionais que podem ser manipulados pela aplicacao.
	  * @var mixed selfParms
	  */
	public static $selfParms = array();



	/**
	  * @var int status
	  */
	protected $status = 0;


	const ST_NONE 		= 0;
	const ST_PREPARACAO	= 1;
	const ST_EXECUCAO 	= 2;




	/**
	  * @var RequestG3 lastRequestG3
	  */
	private $lastRequestG3;


	/**
	  * @var array listaClassesAutoLoad
	  */
	protected $listaClassesAutoLoad;



	public static $lsComandosAdministrativos = array('RequestGamaInfo','MakeManifesto','ShowPainelAdministrativo','ShowJsonQuery','ShowPainelTest');


	const FTE_HTML = 1;
	const FTE_JSON = 2;
	const FTE_XML  = 3;

	/**
	  * @var mixed formatoTratamentoErro
	  */
	protected $formatoTratamentoErro;

	//--------------------------------------------

	/**
	 * Retorna o valor de formatoTratamentoErro
	 * @return mixed
	 */
	public function getFormatoTratamentoErro () {
		return $this->formatoTratamentoErro;
	} // eof getFormatoTratamentoErro



	//--------------------------------------------

	/**
	 * Define o valor de formatoTratamentoErro
	 * @param mixed $formatoTratamentoErro
	 */
	public function setFormatoTratamentoErro ($formatoTratamentoErro) {
		$this->formatoTratamentoErro = $formatoTratamentoErro;
	} // eof setFormatoTratamentoErro




	/**
	 * Retorna a lista de valores de listaClassesAutoLoad.
	 * Se tiver um nome de classe como par�metro, verifica se a classe est�
	 * referenciada na lista de classes - em caso afirmativo, retorna o seu
	 * valor (path onde se encontra a classe em quest�o); Caso a classe n�o
	 * esteja definida, retorna valor false.
	 *
	 * @param  string $chave Nome da classe - se necess�rio.
	 * @return array|string|false
	 */
	public function getListaClassesAutoLoad ($chave=null) {
		if (is_null($chave)) {
			return $this->listaClassesAutoLoad;
		} else if (is_array($this->listaClassesAutoLoad) && array_key_exists($chave,$this->listaClassesAutoLoad)) {
			return $this->listaClassesAutoLoad[$chave];
		} else {
			return false;
		}
	} // eof getListaClassesAutoLoad


	/**
	 * Define o valor de listaClassesAutoLoad
	 * @param array $listaClassesAutoLoad
	 */
	public function setListaClassesAutoLoad ($listaClassesAutoLoad) {
		$this->listaClassesAutoLoad = $listaClassesAutoLoad;
	} // eof setListaClassesAutoLoad



	/**
	 * Retorna o valor de lastRequestG3
	 * @return RequestG3
	 */
	public function getLastRequestG3 () {
		if (is_null($this->lastRequestG3)) {
			$this->lastRequestG3 = new RequestG3();
			$rq = $this->getSess()->get('lastRequestG3',false);
			if ($rq) {
				$this->lastRequestG3->setM($rq['m']);
				$this->lastRequestG3->setU($rq['u']);
				$this->lastRequestG3->setA($rq['a']);
				$this->lastRequestG3->setAcao($rq['acao']);
			}
		}
		return $this->lastRequestG3;
	} // eof getLastRequestG3



	//--------------------------------------------



	/**
	 * Retorna o valor de rootPath
	 * @return string
	 */
	public function getRootPath () {
		return $this->rootPath . '/';
	} // eof getRootPath





	/**
	 * Define o valor de rootPath
	 * @param string $rootPath
	 */
	public function setRootPath ($rootPath) {
		$this->rootPath = $rootPath;
	} // eof setRootPath


	//--------------------------------------------


	/**
	 * Retorna o valor de status
	 * @return int
	 */
	public function getStatus () {
		return $this->status;
	} // eof getStatus



	//--------------------------------------------

	/**
	 * Define o valor de status
	 * @param mixed $status
	 */
	public function setStatus ($status) {
		$this->status = $status;
	} // eof setStatus



	//--------------------------------------------

	/**
	 * Retorna o valor de selfParms
	 * @return mixed
	 */
	public function getSelfParms () {
		if (is_null(MainGama::$selfParms)) {
			MainGama::$selfParms = array();
		}
		return MainGama::$selfParms;
	} // eof getSelfParms



	//--------------------------------------------

	/**
	 * @param string $chave
	 * @param mixed $valor
	 */
	public function setParm ($chave,$valor) {
		MainGama::$selfParms[$chave] = $valor;
	} // eof setParm



	// ----------------------------------------------------

	/**
	 * Retorna o valor de app
	 * @return MainGama
	 */
	static public function getApp () {
		return MainGama::$app;
	} // eof getApp



	//--------------------------------------------

	/**
	 * Define o valor de app
	 * @param mixed $app
	 */
	public function setApp ($app) {
		self::$app = $app;
	} // eof setApp



	static public function getIDRequest() {
		if (is_null(self::$idRequest)) {
			self::$idRequest = rand(1,10000);
			//			(MainGama::getApp()->getConfig('debug',false))?MainGama::getApp()->getDebug()->log("NOVA REQUISICAO - ".self::$idRequest,'sys',"\n@@@@@@@@@@@@@@@@@@\n"):null;
		}
		return self::$idRequest;
	}




	/**
	 * Retorna o valor de rootScript
	 * @return string
	 */
	public function getRootScript () {
		return $this->rootScript;
	} // eof getRootScript




	/**
	 * Define o valor de rootScript
	 * @param string $rootScript
	 */
	public function setRootScript ($rootScript) {
		$this->rootScript = $rootScript;
	} // eof setRootScript


	//--------------------------------------------

	/**
	 * Retorna o valor de logger
	 * @return PHPLogger
	 * @deprecated
	 */
	public function getLogger () {
		return PHPLogger::getInstance();
	} // eof getLogger




	/**
	 * Retorna o valor de nomeINI
	 * @return mixed
	 */
	public function getNomeINI () {
		return $this->nomeINI;
	} // eof getNomeINI



	/**
	 * Retorna o valor de i18n
	 * @return I18N
	 */
	public function getI18N () {
		if (is_null($this->i18n)) {
			$this->i18n = new I18N($this);
		}
		return $this->i18n;
	} // eof getI18n



	/**
	 * Define o valor de i18n
	 * @param mixed $i18n
	 */
	public function setI18N ($i18n) {
		$this->i18n = $i18n;
	} // eof setI18n



	/**
	 * Define o valor de nomeINI
	 * @param mixed $nomeINI
	 */
	public function setNomeINI ($nomeINI) {
		$this->nomeINI = $nomeINI;
	} // eof setNomeINI



	public function _doLogErro($msg) {
		//		$this->getLogger()->write($msg, ERROR, "test Log");
		MainGama::getDebug()->log($msg,'ERRO');
	}

	public function _doLogDebug($msg) {
		MainGama::getDebug()->log($msg,'DEBUG');
//		if ($this->getConfig('debug',false)) {
			//			$this->getLogger()->write($msg, DEBUG, "test Log");
//		}
	}

	public function _doLogWarning($msg) {
		MainGama::getDebug()->log($msg,'WARNING');
		//		$this->getLogger()->write($msg, WARNING, "test Log");
	}

	public function _doLogInfo($msg) {
		MainGama::getDebug()->log($msg,'INFO');
		//		$this->getLogger()->write($msg, INFO, "test Log");
	}






	//--------------------------------------------

	/**
	 * Define o valor de logger
	 * @param mixed $logger
	 */
	public function setLogger ($logger) {
		$this->logger = $logger;
	} // eof setLogger




	/**
	 * M�todo construtor da classe
	 *
	 * @param string $rootPath
	 * @return MainGama
	 */
	function __construct($rootPath='.',$modPath='mod') {
		$this->initFase1($rootPath,$modPath);

		//register_shutdown_function("gravaTrace2");
		if (file_exists($this->getModPath().$this->getM().'/include.php')) {
			include_once($this->getModPath().$this->getM().'/include.php');
		}
		$this->initFase2();

		if ($this->getParms('gama_redir',false)) {
			$this->decodeParmsRedir($_GET['parms']);
		}

	} // eof MainGama


	protected function initFase1($rootPath='.',$modPath='mod') {

		$this->defHookTrataErro();

		$this->setStatus(MainGama::ST_NONE);

		$this->setRootPath($rootPath);
		$this->setModPath($modPath);

		$this->checkConfig();
		$this->loadConfig();
		$this->loadConfigINI();

		// Registro dos carregadores autom�ticos
		spl_autoload_register(array($this,"_autoload_base"));
		spl_autoload_register(array($this,"_autoload_mod"));

		$this->setRootScript($this->getConfig('rootScript','index.php'));

		// Incluido para verificar possiveis mudancas de configuracao
		$this->checkVirtualDirectoryRequest();

		$this->includes();
	}

	public function initFase2() {

		// Inicializando a sessao
		SessionGama::getSession($this);
		$this->sess =& $_SESSION['sess'];

		//set_error_handler(array('MainGama','trataErro'),E_ALL);

		//		$this->initDebug();

		$this->checkStatusRequest();

		MainGama::setApp($this);
                
                $this->checkStatusDebugRemoto();
                
	}


        protected function checkStatusDebugRemoto() {
            
        }

	/**
	 * Define a fun��o que far� o tratamento de erros.
	 *
	 */
	protected function defHookTrataErro() {
		set_error_handler(array('MainGama','hookTrataErro'),E_ALL);
		set_exception_handler(array('MainGama','hookTrataException'));
	} // defHookTrataErro


	/**
	 * Trata os erros cr�ticos
	 *
	 */
	public static function hookTrataErro($errno, $errstr, $errfile, $errline,$descricao=false) {

		$arrErros = array(	E_ERROR => 'ERRO',
		E_WARNING => 'WARNING',
		E_PARSE => 'PARSE',
		E_NOTICE  => 'NOTICE',
		E_CORE_ERROR  => 'CORE_ERROR',
		E_CORE_WARNING  => 'CORE_WARNING',
		E_COMPILE_ERROR  => 'COMPILE_ERROR',
		E_COMPILE_WARNING  => 'COMPILE_WARNING',
		E_USER_ERROR  => 'USER_ERROR',
		E_USER_WARNING  => 'USER_WARNING',
		E_USER_NOTICE  => 'USER_NOTICE',
		E_ALL  => 'ALL',
		E_STRICT  => 'STRICT');

		if (($errno == 8) || ($errno == 2)) return;

		echo '<pre>';
		print_r(MainGama::getApp());
		echo "\n\n $errstr";
		echo "\n $errfile ($errline)";
		exit;

		$listaParmConfig = MainGama::getApp()->getAllParmsConfig(MainGama::getApp());

		if (array_key_exists($errno,$arrErros)) {
			MainGama::getApp()->getSmarty()->assign('errtype',$arrErros[$errno]);
		} elseif ($descricao === false) {
			MainGama::getApp()->getSmarty()->assign('errtype',"EXCEPTION");
		} else {
			MainGama::getApp()->getSmarty()->assign('errtype',"ERRO #".$errno);
		}
		MainGama::getApp()->getSmarty()->assign('errno',$errno);
		MainGama::getApp()->getSmarty()->assign('errstr',$errstr);
		MainGama::getApp()->getSmarty()->assign('errfile',$errfile);
		MainGama::getApp()->getSmarty()->assign('errline',$errline);
		MainGama::getApp()->getSmarty()->assign('descricao',$descricao);
		MainGama::getApp()->getSmarty()->assign('app',MainGama::getApp());
		MainGama::getApp()->getSmarty()->assign('ver',MainGama::getApp()->getVersao());
		MainGama::getApp()->getSmarty()->assign('server',$_SERVER);
		MainGama::getApp()->getSmarty()->assign('lsParms',array_merge($_POST,$_GET));
		MainGama::getApp()->getSmarty()->assign('listaParmConfig',$listaParmConfig);

		switch (MainGama::getApp()->getFormatoTratamentoErro()) {
			case MainGama::FTE_XML :
				MainGama::getApp()->getSmarty()->display('gama_erro_xml.tpl');
				break;
			case MainGama::FTE_JSON:
				$errors = array();
				$errors[] = array('id' => 0, 'msg' => htmlspecialchars($errstr), 'code' => $errno, 'descricao' => htmlspecialchars($descricao));
				echo "{success: false, errors: ".json_encode($errors)."}";
				break;
			case MainGama::FTE_HTML :
			default:
				$txt = MainGama::getApp()->getSmarty()->fetch('gama_erro_all.tpl');
				MainGama::getApp()->getSmarty()->assign('txt',chunk_split(base64_encode($txt)));
				MainGama::getApp()->getSmarty()->display('gama_erro.tpl');
				break;
		}
		exit;
	} // hookTrataErro




	/**
	 * Captura e trata as exceptions n�o tratadas.
	 *
	 * @param SysException $e
	 */
	public  static function hookTrataException($e) {
		if (is_a($e,'SysException')) {
			MainGama::hookTrataErro($e->getCode(),$e->getMessage(),$e->getFile(),$e->getLine(),$e->getDescricao());
		} else {
			MainGama::hookTrataErro($e->getCode(),$e->getMessage(),$e->getFile(),$e->getLine());
		}
	} // hookTrataException


	/**
	 * Inicializa o recurso de debug.
	 *
	 */
	function initDebug($idConn='-') {
		if ($this->getStatus() != MainGama::ST_EXECUCAO) {
			return;
		}
		if ($this->getConfig('debug',false)) {
			@date_default_timezone_set('UTC');
			$aOptions = array (
			'render_type'          => 'HTML',             				// Renderer type : 'HTML' or 'brut'
			'restrict_access'      => true,               				// Restrict or not the access : boolean
			'allowed_ip'           => array($_SERVER['SERVER_ADDR']),	// Authorized IP to view the debug when restrict_access is true
			'allow_url_access'     => true,               				// Allow to access the debug with a special parameter in the url
			'url_key'              => '__debug_gama3__',        		// Chave para ativar o debug na URL
			'url_pass'             => 'ativo',          				// Senha para ativar o oDebug
			'url_unpass'           => 'inativo',          				// Senha para desativar o oDebug
			);

			$this->listaConexoes[$idConn] = new oDebug($this->listaConexoes[$idConn],array());
			$this->listaConexoes[$idConn]->idConn = $idConn;
		}
	} // eof initDebug



	function closeDebug() {
		//		$this->getODebug()->close();
	}


	function checkStatusRequest(){
//		$id = MainGama::getIDRequest();
		if ($this->getParms(MainGama::REDIRECT,false)) {
			$this->setStatus(MainGama::ST_PREPARACAO);
//			if (!$this instanceof AutoExecGama) {
//				$this->getDebug()->log("P�gina em estado de redirecionemanto - $id",'sys',"\n\n\n########################## ".MainGama::getIDRequest()." ##################\n");
//			}
		} else {
			$this->setStatus(MainGama::ST_EXECUCAO);
//			if (!$this instanceof AutoExecGama) {
//				$this->getDebug()->log("P�gina em estado de execu��o - $id",'sys',"\n\n\n########################## ".MainGama::getIDRequest()." ##################\n");
//			}
		}
	}


	/**
	 * Inclui alguns arquivos principais.
	 *
	 */
	function includes() {
		require_once($this->getRootPath() . 'lib/gama/persistencia/ADO.php');
		require_once($this->getRootPath() . 'lib/adodb/adodb.inc.php');
		require_once($this->getRootPath() . 'lib/adodb/adodb-active-record.inc.php');
		require_once($this->getRootPath() . 'lib/adodb/adodb-exceptions.inc.php');
		require_once(SMARTY_DIR.'/Smarty.class.php');
		require_once($this->getRootPath() . 'lib/gama/base/G3MsgLog.class.php');
		require_once($this->getRootPath() . 'lib/gama/base/G3Logger.class.php');
	} // eof includes




	/**
	 * @return SessionGama
	 */
	function getSess() {
		return $this->sess;
	}

	/**
	 * Metodo que retorna um atributo de configuracao...
	 * Se o mesmo n�o existe no arquivo de configura��o, e o par�metro 'default'
	 * � informado, assume-se este valor; caso contr�rio, retorna 'null'.
	 *
	 * @param string $chave
	 * @param boolean $default = false
	 * @return string
	 */
	public function getConfig($chave=false,$default=false) {
		if ($chave === false) {
			return $this->config;
		} elseif (is_array($chave)) {
			switch (count($chave)) {
				case 1: return $this->config[reset($chave)];
				case 2: return $this->config[reset($chave)][next($chave)];
			}
		} else {
			if (array_key_exists($chave,$this->config)) {
				if (array_key_exists('instancia_gama',$_ENV)) {
					$chaveAux = $_ENV['instancia_gama'];
					if ((array_key_exists($chaveAux,$this->config)) && array_key_exists($chave,$this->config[$chaveAux])) {
						return $this->config[$chaveAux][$chave];
					}
				}
				return $this->config[$chave];
			} else if ($default){
				return $default;
			} else {
				return null;
			}
		}
		return null;
	} // eof getConfig


	/**
	 * M�todo protegido, para ser usado apenas pelo MainGama e seus descendentes,
	 * que permite incluir/alterar chaves de configura��o da aplica��o, em
	 * tempo de execu��o.
	 *
	 * @param string $chave
	 * @param mixed $valor
	 */
	protected function setConfig($chave,$valor) {
		$this->config[$chave] = $valor;
	} // eof setConfig


	/**
	 * Metodo responsavel pela verificacao da existencia do arquivo de
	 * configuracao - caso este nao exista, o sistema e' abortado
	 * imediatamente.
	 */
	protected function checkConfig() {
		if (!file_exists($this->getPathArqConf() . '/gconf.cfg')) {
			echo $this->getPathArqConf() . '/gconf.cfg' . '<hr>';
			debug_print_backtrace();
			die ('Erro - arquivo de configuracao inexistente: "gconf.cfg" ');
		}
	} // eof checkConfig


	/**
	 * Metodo responsavel pela carga dos parametros de configuracao.
	 */
	protected function loadConfig() {
		$gconf = array();
		require($this->getPathArqConf() . '/gconf.cfg');
		foreach ($gconf as $k => $v) {
			$this->config[$k] = $v;
		}
		require($this->getPathArqConf() . '/smarty.cfg');
	} // eof loadConfig


	/**
	 * Retorna a identifica��o da inst�ncia na qual se est� rodando a aplica��o.
	 *
	 * @return string
	 */
	public function getInstanciaID() {

		$instancia = $_SERVER['SERVER_NAME'];

		if (get_cfg_var ('instancia')) {
			$instancia .= '.'. get_cfg_var ('instancia');
		} elseif (array_key_exists('instancia',$_ENV)) {
				                    
			$instancia .= '.'. $_ENV['instancia'];
		}
		return $instancia;
	} // getInstanciaID


	/**
	 * Verifica se existe arquivo de configura��o adicional,
	 * espec�fico para a aplica��o. Se existir, ent�o carrega.
	 */
	protected function loadConfigINI() {

		$path = $this->getModPath().$this->getM() . '/' . $this->getNomeINI();
		if (file_exists($path)) {
			$arr = parse_ini_file($path,true);
			$nomeKey = 'geral';
			$nomeKeyInstancia = $nomeKey . '.' . $this->getInstanciaID();
			if (array_key_exists ($nomeKey,$arr)) {
				$this->appendConfigParms($arr[$nomeKey]);
				unset($arr[$nomeKey]);
			}
			if (array_key_exists ($nomeKeyInstancia, $arr)) {
				$this->appendConfigParms($arr[$nomeKeyInstancia]);
				unset($arr[$nomeKeyInstancia]);
			}
			$this->config = array_merge($this->config,$arr);
		}
	} // eof loadConfigINI


	/**
	 * M�todo interno usado para iterar a lista de valores de um array
	 * associativo e atribu�-los como par�metros de configura��o da aplica��o.
	 *
	 * @param array $arrParms
	 */
	protected function appendConfigParms($arrParms=array()) {
		foreach ($arrParms as $k => $v) {
			$this->config[$k] = $v;
		}
	} // eof appendConfigParms


	/**
	 * Retorna o valor de modPath
	 * @return string
	 */
	public function getModPath () {
		return $this->getRootPath() . $this->modPath . '/';
	} // eof getModPath

	/**
	 * Define o valor de modPath
	 * @param string $modPath
	 */
	public function setModPath ($modPath) {
		$this->modPath = $modPath;
	} // eof setModPath


	/**
	 * Retorna o valor de pathArqConf
	 * @return string
	 */
	public function getPathArqConf () {
		return $this->getRootPath() . $this->pathArqConf;
	} // eof getPathArqConf

	/**
	 * Define o valor de pathArqConf
	 * @param string $pathArqConf
	 */
	public function setPathArqConf ($pathArqConf) {
		$this->pathArqConf = $pathArqConf;
	} // eof setPathArqConf


	/**
	 * Retorna o nome do m�dulo selecionado para execu��o.
	 *
	 * @return string
	 */
	function getM() {
		$m = $this->getParms('m',$this->getConfig('default_m'));
		$m = $this->sanitiza($m);
		return $m;
	} // getM

	/**
	 * Retorna o subm�dulo selecionado para ser executado.
	 *
	 * @return string
	 */
	function getU() {
		$u = $this->getParms('u',$this->getConfig('default_u',null));
		if ($u == '.') {
			$u = null;
		}
		$u = $this->sanitiza($u);
		return $u;
	} // getU

	/**
	 * Retorna o nome do Action selecionado para execu��o.
	 *
	 * @return string
	 */
	function getA() {
		$a = $this->getParms('a',$this->getConfig('default_a'));
		$a = $this->sanitiza($a);
		return $a;
	} // eof getA


	/**
	 * Verifica e trata o conte�do da vari�vel, para evitar a inje��o de algum
	 * c�digo malicioso.
	 *
	 * Usado apenas para os par�metros 'm', 'u' e 'a'.
	 *
	 * @param string $var
	 */
	protected static function sanitiza($var) {
		return $var;
		// Deu um erro (notice) e vejo que isso nunca funcionou.
		// Entao estou relaxando esta restri��o, por enquanto.
/*
		if (($item == '.') || (!preg_match('/[\\\.:\s%]/',$item))) {
			return $var;
		} else {
			die('Erro - valor invalido de parametro: ' . $var);
		}
*/
	} // sanitiza


	/**
	 * Retorna o nome da A��o requisitada.
	 *
	 * @param boolean $consideraConfig
	 * @return string
	 */
	function getAcao($consideraConfig=true) {
		$acao = $this->getParms('acao',false);
		if (!$acao && ($consideraConfig == true)) {
			$acao = $this->getConfig('default_acao','showIndex');
		}
		return $acao;
	} // eof getAcao


	/**
	 * Responsavel pela execucao e atendimento da requisicao, e' a espinha
	 * central do sistema.
	 *
	 * @param array $get - variaveis passados por metodo GET
	 * @param array $post - variaveis passadas por metodo POST
	 */
	function exec($GET=array(),$POST=array()) {


		//		 Conectando no banco de dados
                            
		$this->conectaDB();

                // Verifica as tabelas-padrao do sistema, e cria se nao houver.
                $this->checkDatabase();
                
                //$this->checkSessao(session_id());
                
		$this->checkAdminRequest();

		// @deprecated
		//$this->initDebug();

		$this->preExec();

		/**
		 * Se a chamada for de webservice, ent�o deve haver um tratamento especial
		 * para a autentica��o...
		 */
		$this->checkLogin();


		// AQUI FALTA FAZER O CONTROLE DE ACESSO - verificar as permissoes do usuario

		// Aqui estou pegando os parametros usados para definir o que sera executado
		// e mando para o metodo que gera a string com a path do script que encabecara
		// a chamada.
		$path = $this->geraPath();


		$this->preProcessa($GET,$POST);


		$resp = $this->processa($path,$GET,$POST);
                

		// ACHO QUE DAQUI N�O EST� PASSANDO...
		// ACHO QUE DAQUI N�O EST� PASSANDO...
		// ACHO QUE DAQUI N�O EST� PASSANDO...
		// ACHO QUE DAQUI N�O EST� PASSANDO...


		$this->posExec();

		//		$this->closeDebug();

		return $resp;
	} // eof exec


     /**
      * Verifica se as tabelas necessarias para o funcionamento da base do
      * sistema (Gama) existem, e se nao existem, cria. 
      */
     protected function checkDatabase() {
         
         $sqls = array();
         $sqls['tb_sys_usuario'] = "CREATE TABLE public.tb_sys_usuario ( usu_id BIGSERIAL, usu_nome VARCHAR(80), usu_username VARCHAR(64) NOT NULL, usu_senha VARCHAR(64), usu_nivel INTEGER DEFAULT 50, usu_status CHAR(1) DEFAULT 'A'::bpchar NOT NULL, CONSTRAINT pk_sys_usuario PRIMARY KEY(usu_id), CONSTRAINT tb_sys_unique_key UNIQUE(usu_username) )";
         $sqls['tb_sys_sessao'] = "CREATE TABLE public.tb_sys_sessao (  ss_id BIGSERIAL,   ss_usu_id BIGINT,   ss_dh_login TIMESTAMP WITHOUT TIME ZONE, ss_dh_expire TIMESTAMP WITHOUT TIME ZONE,  ss_ip VARCHAR(200),   ss_sid VARCHAR(80),   PRIMARY KEY(ss_id))";
         $sqls['tb_sys_debug_sessao'] = "CREATE TABLE public.tb_sys_debug_sessao (  ds_nivel INTEGER,   ds_usu_id BIGINT,   ds_status SMALLINT,   ds_dh_login TIMESTAMP WITHOUT TIME ZONE,   ds_dh_inicio_debug TIMESTAMP WITHOUT TIME ZONE,   ds_dh_termino_debug TIMESTAMP WITHOUT TIME ZONE,   ds_ip_cliente VARCHAR(15),   ds_ip_debug VARCHAR(15),   ds_sid VARCHAR(64),   ds_autorizacao_debug VARCHAR(512) ) WITHOUT OIDS; ";
         $sqls['tb_sys_log_debug_sessao'] = "CREATE TABLE public.tb_sys_log_debug_sessao (  lds_id BIGSERIAL,  lds_transacao BIGINT,  lds_tipo_log SMALLINT,  lds_dh_registro TIMESTAMP WITHOUT TIME ZONE,  lds_ds_sid VARCHAR(64),  lds_classe VARCHAR(128),  lds_titulo VARCHAR(128),  lds_conteudo VARCHAR(1024),  CONSTRAINT tb_sys_log_debug_sessao_pkey PRIMARY KEY(lds_id)) WITHOUT OIDS; ";

         try {
            $listaTabelas = $this->getCon()->MetaTables('TABLES');

            foreach ($sqls as $nomeTabela => $sql) {
                if (array_search($nomeTabela, $listaTabelas) === false) {
                    $this->getCon()->Execute($sql);
                }
            }            
         } catch (Exception $e) {
             echo $e->getTraceAsString();
             exit;
         }         
     }


     /**
      * Realiza o registro de uma sessao na tabela do sistema (tb_sys_sessao).
      * 
      * Regras a serem consideradas:
      * 1. Verificar se o par�metro de configura��o NUMERO_MAXIMO_SESSOES_POR_USUARIO - se estiver presente, e for superior a zero:
      * 1.1. Se o usu�rio $usuId j� possui mais sess�es do que o informado no parametro 'NUMERO_MAXIMO_SESSOES_POR_USUARIO', ent�o:
      * 1.1.1. Desloga as 'x' sess�es mais antigas, onde 'x' � o n�mero de sess�es excedidas;
      * 
      * @param int $usuId
      * @param string $sid
      * @param string $ip 
      * @return int Identificador da sessao (Gama Session ID = gsid)
      */
     public function abreSessao($usuId, $sid, $ip) {
         
         // Se n�o for informado o tempo, assume 20 horas como default 
         $expire = $this->getConfig(TEMPO_MAXIMO_SESSAO_POR_USUARIO,1200); 
         
         $sql = "INSERT INTO public.tb_sys_sessao (  ss_usu_id,  ss_dh_login,  ss_dh_expire,  ss_ip,  ss_sid
                ) VALUES ( $usuId, now(), now() + '{$expire} minutes', '{$ip}', '{$sid}'  )";

         $this->getCon()->Execute($sql);       

     }
     
     
     /**
      * 1. Verifica se a sess�o informada por par�metro � v�lida e est� ativa - se n�o estiver, cancela-a;
      * 2. Verifica se o usu�rio possui 'sess�es demais' abertas (maior que NUMERO_MAXIMO_SESSOES_POR_USUARIO, se esta for maior que zero), e fecha-as se for o caso.
      * 
      * @param type $ssid 
      */
     public function checkSessao($ssid) {         
         $sql = "SELECT s.*, ds.ds_status, ds.ds_sid,  
                        case when ss_dh_expire < now() then 'S' else 'N' end as ss_expirada , 
                        (select COALESCE(lds.lds_transacao) FROM tb_sys_log_debug_sessao lds WHERE  lds.lds_ds_sid = s.ss_sid ) as lds_transacao
         FROM tb_sys_sessao s
            LEFT JOIN tb_sys_debug_sessao ds ON ds.ds_sid = s.ss_sid 
         WHERE ss_sid = '{$ssid}' ";
         $regs = $this->getCon()->GetArray($sql);
         if (empty($regs)) {
//             $msg = '';
             if ($this->getSess()->getProfile()) {                 
                if ($this->getSess()->getProfile()->getUsuario()) {
                    if ($this->getSess()->getProfile()->getUsuario()->getID()) {
                        
                        
                         $usuId = $this->getSess()->getProfile()->getUsuario()->getID();
                         $sessoes = $this->getCon()->GetArray("SELECT * FROM tb_sys_sessao WHERE ss_usu_id = $usuId");

                         if ((count($sessoes)) > $this->getConfig(NUMERO_MAXIMO_SESSOES_POR_USUARIO,99999)) {
//                             $msg = 'Sess�es extra fechadas';
                            foreach ($sessoes as $sessao) {
                                $this->fechaSessao($sessao['ss_sid']);
                            }
                         }
                        
                        //$ip = 'x';
                        //$this->abreSessao($this->getSess()->getProfile()->getUsuario()->getID(), $ssid, $ip);    
                    }
                }
             }
             return false;
         } else {
             $req = reset($regs);
             $usuId = $req['ss_usu_id'];
            
             
             // Verifica se tem um arquivo com a transacao existente. 
             // Se tiver, deve abrir, processar todos os elementos, inserindo-os no banco de dados, e depois apagar o arquivo
             if (file_exists('./log/'.  session_id().'.'.$req['lds_transacao'] . '.log')) {
                 $linhas = file('./log/'.  session_id().'.'.$req['lds_transacao'] . '.log');
                 foreach ($linhas as $linha) {
                     MainGama::getApp()->getCon()->directExec($linha);
                 }
                 rmdir('./log/'.  session_id().'.'.$req['lds_transacao'] . '.log');
             }
             
             
             if (($req['ss_sid'] == $req['ds_sid']) && ( $req['ds_status'] == 1 )) {
                 //$req['lds_transacao'] = str_replace('.', '_', getGamaMicrotime(true));
                 $req['lds_transacao'] = getGamaMicrotime(true) * 10000;
                 
                 $sql = "UPDATE tb_sys_log_debug_sessao SET lds_transacao = '" . $req['lds_transacao'] . "' WHERE  lds_ds_sid = '" . $req['ss_sid'] . "'";
                 MainGama::getApp()->getCon()->directExec($linha);
                 
                 MainGama::getApp()->getSess()->set(INDICADOR_DEBUG_REMOTO,true);
                 MainGama::getApp()->getSess()->set(REGISTRO_DEBUG_REMOTO,$req['lds_transacao'] );
                 //error_log("\n REGISTRO_DEBUG_REMOTO = " . MainGama::getApp()->getSess()->get(REGISTRO_DEBUG_REMOTO) . "\n".var_export($req,true),3,'./log/geral.log');
                 //die('true');
             } else {
                 MainGama::getApp()->getSess()->set(INDICADOR_DEBUG_REMOTO,false);
                 MainGama::getApp()->getSess()->set(REGISTRO_DEBUG_REMOTO,false);
                 //die('false');
             }
             
             if ($req['ss_expirada'] == 'S') {
                 $this->fechaSessao($ssid);
                 $this->getSess()->setProfile(null);
                 $this->redirecionaActionLogin();
                 exit;
             } else if ($this->getConfig(NUMERO_MAXIMO_SESSOES_POR_USUARIO,99999) > 0) {
                 $sessoes = $this->getCon()->GetArray("SELECT * FROM tb_sys_sessao WHERE ss_usu_id = $usuId");
                 if (count($sessoes) > $this->getConfig(NUMERO_MAXIMO_SESSOES_POR_USUARIO,99999)) {
                    foreach ($sessoes as $sessao) {
                        if ($sessao['ss_sid'] != session_id()) {
                            $this->fechaSessao($sessao['ss_sid']);
                        }
                    }
                    //die('Excedeu o numero maximo de sessoes: ' . $numSessoes);
                }
             }
         }
         return true;
     }
     
     
     /**
      * Retorna um array com a lista de alias (m, u, a, acao) que pode iniciar 
      * o sistema, com a pagina inicial. 
      * Esta informa��o ser� usada para definir o procedimento de exibi��o da tela de login
      */
     public function getAcoesIniciais() {
         $alias = join('.',array($this->getM(),$this->getConfig('default_u'),$this->getConfig('default_a'),$this->getConfig('default_acao')));
         return array($alias);
     }

     public function getAcaoRequisitada() {
         return join('.',array($this->getM(),$this->getU(),$this->getA(),$this->getAcao()));
     }
     
     /**
      * Realiza o encerramento do registro de sessao na tabela do sistema.
      * @param string $ssid 
      */
     public function fechaSessao($ssid) {
         $sql = "DELETE FROM public.tb_sys_sessao WHERE ss_sid = '{$ssid}' ";
         $this->getCon()->Execute($sql);
     }
     
     
     
     


     protected function registraDadosRequest($arr) {
		foreach ($arr as $k => $v) {
			$this->_doLogDebug("$k = $v");
		}
	}



	/**
	 * Envia para o cliente uma string no formato JSON, com os
	 * dados e par�metros passados nos par�metros.
	 *
	 * @param string $tipo 	 identificador do tipo de mensagem.
	 * @param string $url  	 URL/comando/mensagem enviada para o cliente
	 * @param string $target Se diferente de false, � o nome do frame/window onde
	 *                       deve ser executado o comando.
	 */
	protected function sendJSON($tipo,$url,$target=false) {
		$obj = new MsgJSON();
		$obj->setTipo($tipo );
		$obj->setURL($url);
		if ($target) {
			$obj->setParmAdicional('_target',$target);
		}
		$s = $obj->asJSON();

		if (MainGama::getApp()->getConfig('debug',false)) {
//			MainGama::getApp()->getDebug()->log($s,'sendJSON');
			MainGama::getApp()->getSess()->set('dadosDebug',MainGama::getApp()->getDebug()->getLsMsgDebug());
		}
		ob_end_clean();
		echo $s;
//		return;
		exit;
	} // eof sendJSON


	public function sendJSON_URL($url,$target=false) {
		$this->sendJSON('url',$url,$target);
	} // eof sendJSON_URL



	public function sendJSON_ALERTA($msg) {
		$this->sendJSON('alerta',$msg);
	} // eof sendJSON_ALERTA



	public function sendJSON_COMANDO($comando) {
		$this->sendJSON('comando',$comando);
	} // sendJSON_COMANDO


	/**
	 * Executado antes de inicar a execu��o da requisi��o propriamente dita,
	 * este m�todo trata os dados vindos por par�metros de comando, podendo
	 * guardar os atributos da requisi��o da sess�o, e enviar um comando JSON
	 * que "manda" o browser redirecionar para uma URL via m�todo GET; Essa
	 * complica��o toda para permitir o uso do bot�o voltar do navegador.
	 *
	 * @param array $GET
	 * @param array $POST
	 */
	protected function preProcessa(&$GET,&$POST) {

            if ($this->getSess()->get(INDICADOR_DEBUG_REMOTO,false)) {
                $this->inicializaLogRemoto();
            }
            
            
		if (MainGama::getApp()->getConfig('debug',false)) {
			$lista = MainGama::getApp()->getSess()->get('dadosDebug');
			MainGama::getApp()->getDebug()->setLsMsgDebug($lista);
		}

	}



        protected function inicializaLogRemoto() {
            // Registra uma linha na tabela sys_log_debug_sessao, com os dados da chamada (grava os primeiros caracteres de cada vari�vel...)
           // $this->getSess()->set(INDICADOR_DEBUG_REMOTO   
           if (!file_exists('./log')) {
               mkdir('./log');
               chmod('./log', 755);
           }
           
           $this->log(1,'init',addslashes(var_export($this->getParms(),true)),'MainGama');    
           
        }
       
        
        public function log($tipo,$titulo,$conteudo=false,$classe=false) {
            if (MainGama::getApp()->getSess()->get(INDICADOR_DEBUG_REMOTO,false)) {
                if (is_object($this->listaConexoes['-'])) {
                    $transacao = MainGama::getApp()->getSess()->get(REGISTRO_DEBUG_REMOTO,1);

                    $SID = session_id();

                    $conteudo = base64_encode($conteudo);
                    $now = date('Y-m-d H:i:') . getGamaMicrotime();
                    
                    $nomeArquivo = session_id() . '.'.$transacao.'.log';
                    

                    $sql = "INSERT INTO tb_sys_log_debug_sessao (   lds_transacao,  lds_tipo_log,  lds_dh_registro,  lds_ds_sid,  lds_classe,  lds_titulo,   lds_conteudo ) VALUES (  $transacao,  $tipo,  '{$now}',  '{$SID}',  '{$classe}',  '{$titulo}',  '{$conteudo}');";
                    error_log("\n".$sql,3,'./log/'. $nomeArquivo);

                    //MainGama::getApp()->getCon()->gamaCache($sql);
                }
            }
        }
        
        
        
    public function gravaTrace2() {
        MainGama::getApp()->log(3, 'acabando com tudo','aa','bb');
    }


	/**
	 * M�todo usado para reconfigurar as vari�veis de execu��o em situa��es
	 * especiais.
	 */
	protected function preExec() {	} // eof preExec


	/**
	 * M�todo usado para realizar tarefas ap�s a execu��o de uma
	 * requisi��o.
	 */
	protected function posExec() { } // eof posExec




	/**
	 * Processa a requisi��o.
	 *
	 * 1. Envia a interface gr�fica quando:
	 * 1.1. Se estiver ativada a op��o de interface desktop (interfaceDesktop), e:
	 * 1.1.1. estiver no processo de login (vari�vel de sess�o 'init' = true)   OU
	 * 1.1.2. n�o haver nenhuma vari�vel requisitada via GET e POST
	 * 2. Sen�o processa normalmente
	 *
	 * @param string $path
	 * @param array $GET
	 * @param array $POST
	 */
	protected function processa($path,$GET,$POST) {
		if ($path) {
			$this->processaInterfaceNormal($path,$GET,$POST);
		} else {
			debug_print_backtrace();
			die("Path indisponivel: $path");
		}
	} // eof processa


	/**
	 * Retorna o rel�gio em milisegundos.
	 *
	 * @return float
	 */
	public static function getmtime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec) * 1000;
	} // eof getmtime



	/**
	 * Realiza o processamento da requisi��o, para um sistema no estilo web "normal",
	 *
	 * @param string $path
	 * @param array $GET
	 * @param array $POST
	 * ($this->getConfig('debug',false))?$this->getDebug()->log('','sys'):null;
	 */
	protected function processaInterfaceNormal($path,$GET,$POST) {
		ob_start();

		if (!file_exists($path)) {
			$e = new SysException('Erro - Arquivo inexistente ' . $path,9);
			throw $e;
		}
		require_once($path);
		$nomeAction = $this->getA() . "Action";

		$action = new $nomeAction($this,array(),array());


		MainGama::getApp()->getDebug()->preExecAction($this->getM(),$this->getU(),$this->getA(),$this->getAcao(),$this->getParms());

		$timeStart = MainGama::getmtime();
		$action->exec();
		$timeStop =  MainGama::getmtime();
		$timeElapsed = $timeStop - $timeStart;


		MainGama::getApp()->getDebug()->posExecAction(' Tempo decorrido '. $timeElapsed . ' ms ');

		$body = ob_get_contents();
		ob_end_clean();

		$this->getSmarty()->assign('gm_header','');
		$this->getSmarty()->assign('gm_body',$body);
		$this->getSmarty()->assign('gm_footer','');

		// AQUI � O �LTIMPO PONTO EM QUE POSSO USAR O gDebug
		// (MainGama::getApp()->getConfig('debug',false))?MainGama::getApp()->getDebug()->log('','sys'):null;
		$this->getSmarty()->display('normal.tpl');
	} // eof processaInterfaceNormal


	/**
	 * Inicialmente tenho apenas um tipo de janela, que � a normal, com cabe�alho, menu e
	 * rodap�... Futuramente posso separar isso, e por isso criei esta verifica��o. Podemos usar
	 * este indicador para implementar janelas tem o t�tulo, como popups e coisas do g�nero.
	 */
	function getTipoResultado() {
		return $this->getParms('t',GM_TIPO_DIALOGO_NORMAL);
	} // eof getTipoResultado



	/**
	 * Realiza a verifica��o da autentica��o, exibindo a p�gina de login caso o usu�rio n�o esteja
	 * conectado ao sistema, e testando os valores entrados por ele.
	 *
	 * Aqui tamb�m � feita a verifica��o da solicita��o de desconex�o, via solicita��o GET - se houver
	 * o par�metro 'doLogout', e o usu�rio est� conectado, ent�o � feita a desconex�o.
	 *
	 * @todo Registrar em log o hist�rico dos eventos de login com sucesso e insucesso, bem como os logout de cada usu�rio.
	 *
	 */
	function checkLogin() {
		if ($this->getParms('doLogout',false)) {
			if ($this->doLogout()) {
				$this->setException(new SysException('Usu�rio desconectado com sucesso',0));
			} else {
				$this->setException(new SysException('Erro na desconex�o do usu�rio',2));
			}
			$this->redirecionaActionLogin();
		} else if ($this->getAcao() === 'doLogin') {
			try {
				$this->autenticaUsuario();
			} catch (SysException $e) {
//				$this->getDebug()->log($e,'sys');
				$this->setException($e);
				$this->getSess()->del('dadosRequest');
				$this->reportaFalhaLogin();
				return;
			}
		} else 	if (!$this->isLogged()) {
//			$this->getDebug()->log('O usu�rio n�o est� logado - Redireciona para o formul�rio de login','sys');
			// antes de redirecionar, guarda os dados do request na sess�o
			//$this->getSess()->set('dadosRequest',$this->getRequest());
			$this->reconfiguraParaActionLogin();
		} else  {
			// Verifica as permiss�es do usu�rio
			$this->checkAutorizacao();
		}

	} // eof checkLogin


	/**
	 * Define o valor do objeto SysException
	 *
	 * @param SysException $e
	 * @deprecated
	 */
	function setException($e) {
		$this->getSess()->set('_exception',base64_encode(serialize($e)));
		//		MainGama::getApp()->getDebug()->log(array('setException',$e->getCode(),$e->getMessage(),$e->getDescricao()),'sys');
	} // eof setException


	/**
	 * Recupera o objeto de exception da sess�o;
	 *
	 * @param boolean $clear indicador de que deve excluir a exception ap�s sua recupera��o.
	 * @return SysException
	 * @deprecated
	 */
	function getException($clear=true) {
		$e = $this->getSess()->get('_exception',false);
//		MainGama::getApp()->getDebug()->log('Obtendo getException','sys');
		if ($e === false) {
//			MainGama::getApp()->getDebug()->log('Criando uma exception nova','sys');
			$e = new SysException('',0);
			$this->setException($e);
			//			$this->getSess()->set('_exception',unserialize($e));
		} else {
			$e = unserialize(base64_decode($e));
		}
		if ($clear) {
//			MainGama::getApp()->getDebug()->log('Limpando Exception','sys');
			$this->getSess()->del('_exception');
		}
		return $e;
	} // eof getException


	/**
	 * Realiza a desconex�o do usu�rio atual.
	 *
	 */
	function doLogout() {
		if (!is_null(MainGama::getApp()->getSess())) {
			$this->getSess()->del('dadosRequest');
			if ($this->isLogged()) {
				$this->getSess()->setProfile(null);
				return true;
			}
		}
		return false;
	} // eof doLogout



	/**
	 * Verifica se o usu�rio est� logado ou n�o.
	 *
	 * @return boolean
	 */
	function isLogged() {
		$isConectado = $this->getSess()->getProfile();
		if ($isConectado === false) {
			return false;
		} else {
			return true;
		}
	} // eof isLogged



	/**
         * M�todo que retorna a string de conex�o com o Banco de Dados
         * @return string DSN 
	 */
        public function getDSN($arrConfig=false) {

            if ($arrConfig) {
                $driver = $arrConfig['db_type'];
                $username = $arrConfig['db_user'];
                $password = $arrConfig['db_pass'];
                $hostname = $arrConfig['db_host'];
                $databasename = $arrConfig['db_database'];
            } else {            
		$driver = $this->getConfig('db_type');
		$username = $this->getConfig('db_user');
		$password = $this->getConfig('db_pass');
		$hostname = $this->getConfig('db_host');
		$databasename = $this->getConfig('db_database');
            }

//		$dsn = "$driver://$username:$password@$hostname/$databasename?persist";
		$dsn = "$driver://$username:$password@$hostname/$databasename";

            return $dsn;
        }
        


	/**
	 * Metodo responsavel pela conexao com o banco de dados, com os parametros
	 * definidos no arquivo gconf.cfg
	 *
	 */
	function conectaDB() {

                $dsn = $this->getDSN();

		MainGama::getApp()->addConnection('-',$dsn);

		@ADOdb_Active_Record::SetDatabaseAdapter(MainGama::getApp()->getCon('-'));

		if (is_null(MainGama::getApp()->getConfig('db_debug'))) {
			MainGama::getApp()->getCon()->debug = MainGama::getApp()->getConfig('db_debug');
		} else {
			MainGama::getApp()->getCon()->debug = false;
		}
		MainGama::getApp()->getCon()->SetFetchMode(ADODB_FETCH_ASSOC);

	} // eof conectaDB



	/**
	 * Atribui o valor do objeto de conex�o.
	 *
	 * @param ADOConnection $con
	 * @param string $idConn
	 */
	protected function setCon($con,$idConn='-') {
		$this->listaConexoes[$idConn] = $con;
//		$this->initDebug($idConn);
	}



	/**
	 * Retorna a refer�ncia ao objeto de conex�o com o banco de dados.
	 *
	 * @param string $idConn
	 * @return ADOConnection
	 */
	function &getCon($idConn='-') {

		if ($idConn === false) {
			$idConn = '-';
		}
		if (array_key_exists ($idConn,  $this->listaConexoes)) {
			return $this->listaConexoes[$idConn];
		} else {
			echo '<pre>';
			debug_print_backtrace();
			echo '<hr>';
			die ("Identificacao de conex�o inv�lida ($idConn)");
		}
	} // eof getCon





	/**
	 * Inclui uma conex�o, atribuindo a ela um alias (identificador) e usando
	 * uma string com a defini��o da conex�o.
	 *
	 * @param string $idConn
	 * @param string $dsn
	 */
	public function addConnection($idConn,$dsn) {

		MainGama::getApp()->listaConexoes[$idConn] = NewADOConnection($dsn);
            if (!MainGama::getApp()->listaConexoes[$idConn]) {
                $e = new SysException("Conexao '{$idConn}' nao pode ser criada",9999);
                $e->setDescricao(var_export(error_get_last(),true));
                throw $e;
            }
            
//		MainGama::getApp()->initDebug($idConn);
	} // eof addConnection






	/**
	 * Metodo que gera a path do script que deve atender a requisicao.
	 *
	 * @param array $get
	 * @param array $post
	 */
	function geraPath() {
		$a = $this->getA();
		$u = $this->getU();
		$m = $this->getM();

		$s = $this->getModPath() . $m . '/';
		if ($u !== null) {
			$s .=  $u  . '/';
		}
		$s .= $a . '.action.php';
		return $s;
	} // eof geraPath



	/**
	 * M�todo interno, usado para verificar se a transa��o requisitada �
	 * administrativa ou n�o.
	 *
	 * @param array $arr
	 * @return boolean true|false
	 * @deprecated
	 */
	function _isAdminTransaction($arr) {
		if ((array_key_exists ('admin',$arr)) && ($arr['admin'] == '1')) {
			return true;
		} else {
			return false;
		}
	} // _isAdminTransaction



	/**
	 * Define o valor da mensagem interna.
	 *
	 * @param SysMsg $objMsg
	 * @deprecated
	 */
	function setMsg($objMsg) {
		$this->msg = $objMsg;
	} // setMsg




	/**
	 * Retorna a inst�ncia principal do Smart para a aplica��o.
	 *
	 * @return Smarty
	 */
	protected function getMainSmarty() {
		$smarty = new SmartyGama();
		//$this->smarty->compile_check = false;
		$smarty->force_compile = true;
		$smarty->caching = false;
		$smarty->template_dir =   $this->getRootPath() . 'temas/' . MainGama::getApp()->getConfig('tema_padrao','padrao');
		$smarty->compile_dir  =   $this->getRootPath() . 'temas/'. MainGama::getApp()->getConfig('tema_padrao','padrao') . '/c';
		//		$smarty->register_modifier();
		if (!file_exists($smarty->compile_dir)) {
			mkdir($smarty->compile_dir);
		}
		return $smarty;
	} // eof getMainSmarty




	/**
	 * obtem uma instancia de gerenciador de templates
	 *
	 * @return Smarty
	 */
	function getSmarty($clone = false)  {
		if (is_null($this->smarty)) {
			$this->smarty = MainGama::getApp()->getMainSmarty();
		}
		if (!file_exists($this->smarty->compile_dir)) {
			mkdir($this->smarty->compile_dir);
		}
		if ($clone) {
			return clone $this->smarty;
		} else {
			return $this->smarty;
		}
	} // getSmarty



	/**
	 * Verifica se a classe cujo nome � passado por par�metro est� registrada
	 * na lista interna. Se estiver, faz a inclus�o da mesma.
	 *
	 * @param string $nomeClasse
	 */
	protected function _autoload_base($nomeClasse) {
		$arr = array(
		'SysTransacaoDAO' 	=> 	$this->getModPath().'/sistema/transacao/dao/SysTransacao.dao.php',
		'SysAutorizacaoBO' 	=> 	$this->getModPath().'/sistema/autorizacao/bo/SysAutorizacao.bo.php',
		'SysAutorizacaoAR' 	=> 	$this->getModPath().'/sistema/autorizacao/ar/SysAutorizacao.ar.php',
		'SysPermissaoDAO' 	=> 	$this->getModPath().'/sistema/permissao/dao/SysPermissao.dao.php',
		'SysUsuarioVO' 		=> 	$this->getModPath().'/sistema/usuario/vo/SysUsuario.vo.php',
		'SysUsuarioBO' 		=> 	$this->getModPath().'/sistema/usuario/bo/SysUsuario.bo.php',
		'SysUsuarioAR' 		=> 	$this->getModPath().'/sistema/usuario/ar/SysUsuario.ar.php',
		'SysPermissaoBO' 	=> 	$this->getModPath().'/sistema/permissao/bo/SysPermissao.bo.php',
		'SysPermissaoAR' 	=> 	$this->getModPath().'/sistema/permissao/ar/SysPermissao.ar.php',
		'SysTransacaoVO' 	=> 	$this->getModPath().'/sistema/transacao/vo/SysTransacao.vo.php',
		'SysTransacaoBO' 	=> 	$this->getModPath().'/sistema/transacao/bo/SysTransacao.bo.php',
		'SysTransacaoAR' 	=> 	$this->getModPath().'/sistema/transacao/ar/SysTransacao.ar.php',
		'SysDAO' 			=> 	$this->getModPath().'/sistema/dao/Sistema.dao.php',

		'SysRegistroAuditoriaAR' =>	$this->getRootPath() . 'lib/gama/comum/RegistroAuditoria.class.php',


		'oDebug' 			=> 	$this->getRootPath() . 'lib/gama/oDebug/oDebug.class.php',

		'RequestG3' 		=> 	$this->getRootPath() . 'lib/gama/base/RequestG3.php',

		'SYSErroBancoDados' => 	$this->getRootPath() . 'lib/gama/persistencia/TrataErroBancoDados.factory.php',
		'TrataErroBancoDadosFactory' => $this->getRootPath() . 'lib/gama/persistencia/TrataErroBancoDados.factory.php',
		'TrataErroBancoDados_postgres7' => $this->getRootPath() . 'lib/gama/persistencia/drivers/postgres7.trata_erros.php',
		'TrataErroBancoDados_mssql' => $this->getRootPath() . 'lib/gama/persistencia/drivers/mssql.trata_erros.php',

		'SysProfile' 		=> 	$this->getRootPath() . 'lib/gama/base/Profile.php',
		'I18N' 				=> 	$this->getRootPath() . 'lib/gama/interface/i18n.php',
		'PHPLogger' 		=> 	$this->getRootPath() . 'lib/PHPLogger/PHPLogger.php',
		'BaseDB' 			=> 	$this->getRootPath() . 'lib/gama/persistencia/BaseDB.php',
		'BaseAR' 			=> 	$this->getRootPath() . 'lib/gama/persistencia/BaseAR.php',
		'BaseDAO' 			=> 	$this->getRootPath() . 'lib/gama/persistencia/BaseDAO.php',
		'DBQuery' 			=> 	$this->getRootPath() . 'lib/gama/persistencia/DBQuery.php',
		'BaseAction' 		=> 	$this->getRootPath() . 'lib/gama/interface/BaseAction.php',
		'SmartyGama' 		=> 	$this->getRootPath() . 'lib/gama/interface/smarty/SmartyGama.php',
		'BaseService' 		=> 	$this->getRootPath() . 'lib/gama/controle/BaseService.php',
		'BaseBO' 			=> 	$this->getRootPath() . 'lib/gama/controle/BaseBO.php',
		'BasePersistenteBO' => 	$this->getRootPath() . 'lib/gama/controle/BasePersistenteBO.php',
		'SessionGama' 		=> 	$this->getRootPath() . 'lib/gama/base/Session.php',
		'SysMsg' 			=> 	$this->getRootPath() . 'lib/gama/sys/SysMsg.php',
		'SysErro' 			=> 	$this->getRootPath() . 'lib/gama/sys/SysErro.php',
		'SysException' 		=> 	$this->getRootPath() . 'lib/gama/sys/SysException.php',
		'SysSucesso' 		=> 	$this->getRootPath() . 'lib/gama/sys/SysSucesso.php',
		'DataUtil' 			=> 	$this->getRootPath() . 'lib/gama/comum/DataUtil.php',
		'RTFUtils' 			=> 	$this->getRootPath() . 'lib/gama/comum/RTFUtils.php',
		'MonetarioUtil' 	=> 	$this->getRootPath() . 'lib/gama/comum/MonetarioUtil.php',
		'NumericoUtil' 		=> 	$this->getRootPath() . 'lib/gama/comum/NumericoUtil.php',
		'Gama3Utils' 		=> 	$this->getRootPath() . 'lib/gama/comum/Gama3Utils.php',
		'ArrayAssociativo' 	=> 	$this->getRootPath() . 'lib/gama/comum/ArrayAssociativo.php',
		'WSGamaClient' 		=> 	$this->getRootPath() . 'lib/gama/base/WS.php',
		'MsgJSON' 			=> 	$this->getRootPath() . 'lib/gama/interface/MsgJSON.php',
		'VOGenerico' 		=> 	$this->getRootPath() . 'lib/gama/base/VOGenerico.php',

		'GamaDebugBase' 	=> 	$this->getRootPath() . 'lib/gama/base/MainGamaDebug.php',
		'GamaFireDebug' 	=> 	$this->getRootPath() . 'lib/gama/base/MainGamaDebug.php',

		'FirePHP' 			=> 	$this->getRootPath() . 'lib/FirePHPCore/FirePHP.class.php',
		'fb' 				=> 	$this->getRootPath() . 'lib/FirePHPCore/fb.php',

		'ZIPFactory' 		=> 	$this->getRootPath() . 'lib/gama/zip/ZIPFactory.php',
		'G3ZIPWin32' 		=> 	$this->getRootPath() . 'lib/gama/zip/ZIPWin32.php',
		'G3ZIP' 			=> 	$this->getRootPath() . 'lib/gama/zip/G3ZIP.php',
		'G3ZIPFreeBSD' 		=> 	$this->getRootPath() . 'lib/gama/zip/ZipFreeBSD.php',
		'ConversorValorMonetario' => 	$this->getRootPath() . 'lib/gama/comum/ConvertUtils.php',
		'ConversorDataHora' => 	$this->getRootPath() . 'lib/gama/comum/ConvertUtils.php',
		'ConversorCEP' 		=> 	$this->getRootPath() . 'lib/gama/comum/ConvertUtils.php',
		'G3Conversor' 		=> 	$this->getRootPath() . 'lib/gama/comum/ConvertUtils.php',

		'GamaUnit' 			=> 	$this->getRootPath() . 'lib/gama/dev/GamaUnit.php',
		'SimpleNxNAssociationDAO' => $this->getRootPath() . 'lib/gama/persistencia/SimpleNxNAssociation.dao.php',
		'GamaAdoPostgres8' => $this->getRootPath() . 'lib/gama/persistencia/drivers/postgres8.db.php',
		'GamaAdoMysqlI' => $this->getRootPath() . 'lib/gama/persistencia/drivers/mysqli.db.php',
		'String' => $this->getRootPath() . 'lib/gama/comum/Strings.class.php',
		'ConversorIncludeJS' => $this->getRootPath() . 'lib/gama/interface_web/ConversorIncludeJS.php'
		);

		if (array_key_exists ($nomeClasse, $arr)) {
			$path = $arr[$nomeClasse];
			require_once($path);
		} else {
			$path = MainGama::getListaClassesAutoLoad($nomeClasse);
			if ($path) {
				require_once($path);
			}
		}
	} // eof _autoload_base



	/**
	 * M�todo onde a lista de classes novas, definidas pelo usu�rio,
	 * podem ser adicionadas. Basta passar por par�metro o array
	 * associativo com a lista de nomes de classes (como chaves) e
	 * suas respectivas paths (como valores).
	 *
	 * @example $this->incluiClassesAutoLoad(array('Krono'=>$this->getRootPath() . 'lib/kronos/class.kronos.php'));
	 *
	 * Veja que em casos onde for necess�rio incluir muitas classes,
	 * pode ser mais interessante criar um m�todo no AutoExecGama e
	 * coloc�-lo na fila, com a fun��o 'spl_autoload_register' do PHP.
	 *
	 * @example spl_autoload_register(array($this,"novo_autoload"));
	 *
	 * @param array $arr
	 */
	protected function incluiClassesAutoLoad($arr) {
		$this->listaClassesAutoLoad = array_merge($this->listaClassesAutoLoad,$arr);
	} // eof incluiClassesAutoLoad



	/**
	 * Tenta "adivinhar" a path de onde se encontra as classes a serem inclu�das.
	 *
	 * A pol�tica de auto-carga funciona da seguinte maneira:
	 *
	 * 1. Se  o arquivo com a classe se encontra na pasta 'bo' de onde est� sendo
	 * feita a requisi��o ('m/u' ou apenas 'm'), ent�o carrega de 'm/u';
	 * 2. Se o arquivo est� no n�vel de 'm' mas a requisi��o de 'm/u', ent�o
	 * carrega de 'm'.
	 *
	 * Assim, se houverem duas classes com o mesmo nome, uma em 'm' e a outra
	 * em 'm/u', ent�o a prefer�ncia � para a que se encontra em 'm/u';
	 *
	 * @param string $nomeClasse
	 */
	private function _autoload_mod($nomeClasse) {
		//		$path = $this->getModPath();
		$m = ($this->getM()?$this->getM():null);
		$u = ($this->getU()?$this->getU():null);

		$ok = $this->_carrega_include($nomeClasse,$m,$u);

		if (!$ok && (!is_null($u))) {
			$this->_carrega_include($nomeClasse,$m);
		}
	} // eof _autoload_mod


	/**
	 * Executa o processo de pesquisa e carga da classe, cujo nome vai como
	 * par�metro. Os par�metros adicionais servem para dar as pistas para
	 * a procura do arquivo.
	 *
	 * @param string $nomeClasse
	 * @param string $m
	 * @param string $u
	 * @return boolean
	 */
	protected function _carrega_include($nomeClasse,$m,$u=null) {
		$path = $this->getModPath();
		$path .= ($m?$m . '/':'');
		$path .= ($u?$u . '/':'');


		if (strtolower(substr($nomeClasse,-2)) == 'bo') {
			$path .= 'bo/' . substr($nomeClasse,0,-2) . '.bo.php';
			if (file_exists($path)) {
				require_once($path);
			} else {
				return false;
			}
		} else if (strtolower(substr($nomeClasse,-2)) == 'ar') {
			$path .= 'ar/' . substr($nomeClasse,0,-2) . '.ar.php';
			if (file_exists($path)) {
				require_once($path);
			} else {
				return false;
			}
		} else if (strtolower(substr($nomeClasse,-2)) == 'vo') {
			$path .= 'vo/' . substr($nomeClasse,0,-2) . '.vo.php';
			if (file_exists($path)) {
				require_once($path);
			} else {
				return false;
			}
		} else if (strtolower(substr($nomeClasse,-3)) == 'dao') {
			$path .= 'dao/' . substr($nomeClasse,0,-3) . '.dao.php';
			if (file_exists($path)) {
				require_once($path);
			} else {
				return false;
			}
		} else if (strtolower(substr($nomeClasse,-6)) == 'action') {
			$path .= substr($nomeClasse,0,-6) . '.action.php';
			if (file_exists($path)) {
				require_once($path);
			} else {
				return false;
			}
		} else {
			return false;
		}
		return true;
	} // eof _carrega_include


	/**
	 * Retorna a inst�ncia da classe MainGama, ou de uma AutoExec
	 * do projeto.
	 *
	 * @param string $m
	 * @return MainGama
	 */
	public static function getInstanceOf($m = false,$rootPath='.',$modPath='mod') {

		$application = null;

		if (array_key_exists ('m',  $_POST)) {
			$m = self::sanitiza($_POST['m']);
		} else if (array_key_exists ('m',$_GET)) {
			$m = self::sanitiza($_GET['m']);
		} else if (array_key_exists('gama_redir',$_GET)) {
			$arr = self::decodeParmsRedir($_GET['parms'],true);
			if (array_key_exists('m',$arr)) {
				$m = $arr['m'];
			} else {
				die('Erro interno (1) - modulo nao informado (avise o suporte para verificar gconf)');
			}
		} else {

			$arr = self::stGetConfig(self::stCheckConfig());
			if (array_key_exists('default_m',$arr)) {
				$m = self::sanitiza($arr['default_m']);
			} else {
				die('Erro interno (2) - modulo nao informado (avise o suporte para verificar gconf)');
			}

		}




		//		$application->setStatus(MainGama::ST_NONE);
		if ($m) {
			//$path = $application->getModPath() . '/' . $m . '/';
			$path = './mod/' . $m . '/';
			$path .= 'AutoExec.class.php';
			//			echo $path;
			//$arr = MainGama::getApp()->getSess()->get('dadosRequest');
			$arr = array();
			if (file_exists($path)) {
				require_once($path);

//				$autoexec->getDebug()->log("Nova Requisicao (AutoExec) "  ,'sys');
//				$autoexec->getDebug()->log($arr  ,'sys');
				return $autoexec;
			} else {
//				$application->getDebug()->log("Nova Requisicao (MainGama)",'sys');
//				$application->getDebug()->log($arr,'sys');
                                die('Erro: modulo "' . $m . '" indexistente');
				return $application;
			}
		}           
                
	} // eof getInstanceOf



	/**
	 * Usado para verificar a existencia do arquivo de config, pelos metodos
	 * estaticos.
	 *
	 * Retorna a path com o nome do arquivo de config.
	 *
	 * @return string
	 */
	protected static function stCheckConfig() {
		//$path = self::$rootPath . '/' . self::$pathArqConf;
		$path = './sys/gconf.cfg';
		if (!file_exists($path)) {
			echo $path  . '<hr>';
			debug_print_backtrace();
			die ('Erro - arquivo de configuracao inexistente: "gconf.cfg" ');
		}
		return $path;
	} // eof checkConfig


	/**
	 * Metodo responsavel pela carga dos parametros de configuracao.
	 */
	protected static function stGetConfig($path) {
		$gconf = array();
		require($path);
		return $gconf;
	} // eof loadConfig





	/**
	 * Caso n�o tenha uma rotina padr�o de autentica��o, executa esta.
	 * Neste caso ela est� vazia e n�o vai autenticar, for�ando um login,
	 * mas quando for necess�rio, implementa-se uma rotina na classe AutoExec
	 * do projeto e sobrescreve-se esta rotina, "viciando" os valores para
	 * testes ou para assumir outro m�todo de autentica��o.
	 *
	 * 1. gravar a requisi��o inicial na sess�o
	 * 2. exibir o formul�rio de login
	 *
	 * 3. se os dados da requisi��o estiverem na sess�o, e a a��o for doLogin,
	 * ent�o tenta autenticar e depois redireciona para o destino desejado.
	 *
	 * Para fazer um bypass eficaz, coloque neste m�todo as defini��es do usu�rio
	 * na sess�o. Qualquer d�vida, veja como foi feito no m�todo 'autorizaAcesso()'.
	 *
	 */
	protected function autenticaUsuario() {
		// Verificando os dados de login
		if ($this->verificaDadosLogin()) {

			// O usu�rio entrou com sucesso
			$this->getSess()->setProfile(new SysProfile());

			// Autorizando acesso...
			$this->autorizaAcesso();

			// Redirecionando para o Index
			$this->redirecionaIndex();

		} else {
			// Erro no login
			$this->getSess()->set('msgErro','erro_login');
			$this->reportaFalhaLogin();
		}

	} // eof autenticaUsuario



	/**
	 * Realiza as opera��es para reportar a falha, e redirecionar para a
	 * transa��o apropriada.
	 *
	 */
	protected function reportaFalhaLogin() {
		$this->reconfiguraParaActionLogin();
	} // reportaFalhaLogin




	/**
	 * Testa os dados passados por par�metro e verifica se os mesmos
	 * d�o acesso ao sistema.
	 * Retorna true se os dados do usu�rio est�o corretos.
	 *
	 * @return boolean
	 */
	public function verificaDadosLogin() {
		try {
			$bo = new SysUsuarioBO();
			$bo->recuperaUsuario($this->getParms('username'),$this->getParms('senha'));
			return true;
		} catch (SysException  $se) {
			$this->setException($se);                        
			throw $se;
		}
		return false;
	} // eof verificaDadosLogin




	/**
	 * Atribui os valores necess�rios para as vari�veis do sistema,
	 * necess�rias para indicar que o usu�rio em quest�o est� apto
	 * a acessar.
	 *
	 */
	protected function autorizaAcesso() {
		$bo = new SysUsuarioBO();
		$bo->recuperaUsuario($this->getParms('username'),$this->getParms('senha'));

		$this->atualizaPermissoesAcesso($bo->getID());
		$this->getSess()->set('init',true);
		$this->getSess()->getProfile()->setUsuario($bo);

		//		$this->redirecionaActionLogin();
	} // eof autorizaAcesso



	/**
	 * Atualiza as permiss�es de acesso do usu�rio em quest�o.
	 *
	 * @param int $usuID
	 */
	public function atualizaPermissoesAcesso($usuID=null) {
		if (is_null($usuID)) {
			$usuario = MainGama::getApp()->getSess()->getProfile()->getUsuario();
		} else {
			$usuario = new SysUsuarioBO();
			$usuario->setID($usuID);
			$usuario->load();
		}
		//		print_r($usuario);
		//		echo $usuario->getNome();

		$autoriza = new SysAutorizacaoBO();
		$lista = $autoriza->getListaTransacoesPermitidasUsuario($usuario);
		$this->getSess()->getProfile()->setTransacoesPermitidas($lista);
	} // atualizaPermissoesAcesso


	/**
	 * Redefine as vari�veis para redirecionar a execu��o para a p�gina
	 * de login;
	 * Executando quando:
	 *   1) Erro de login
	 *   2) Login autorizado
	 *   3) Logout
	 *
	 */
	public function redirecionaActionLogin($m=false) {
		$this->_doLogDebug('For�ando o redirecionamento para a p�gina inicial.');
		$this->preRedirecionaActionLogin();
		ob_end_clean();
                if ($m) {
                    $mod = $m;
                } else {
                    $mod = $this->getM();
                }
		header('Location: '.$this->getRootScript() . '?m='.$mod);
		exit;
	} // eof redirecionaActionLogin


        
    /**
     * Abre o formul�rio de login.
     *
     * @param string $msg
     */
    public function showFormLogin($msg='') {
        $this->limpaBufferEnvio();
        header('HTTP/1.1 403 Forbidden');        
        if (in_array($this->getAcaoRequisitada(), $this->getAcoesIniciais())) {
            $path = $this->getModPath() . $this->getM() . '/template';
            $smarty = $this->getSmarty(true);
            $smarty->assign("m", $this->getM());
            $smarty->template_dir = $path;
            $smarty->compile_dir = $path . '/c';
            $smarty->assign('msg', $msg);
            $smarty->display('login.tpl');
            exit;            
        } else {            
            echo '{"success" : "false", "logout": "true", "m" : "'.$this->getM().'", "msg" : "'.  utf8_encode($msg).'"}';
            exit;   
        }        
    }
        
        
        
        
	protected function preRedirecionaActionLogin() { }

	/**
	 * Redefine as vari�veis para redirecionar a execu��o para a p�gina
	 * de login;
	 *
	 */
	protected function reconfiguraParaActionLogin() {
		$_POST['m'] = 'sistema';
		$_POST['u'] = 'autorizacao';
		$_POST['a'] = 'SysAutorizacao';
		$_POST['acao'] = 'showFormLogin';
	} // eof redirecionaActionLogin



	/**
	 * Altera o valor das vari�veis de requisi��o para acessar a p�gina apropriada.
	 */
	protected function redirecionaIndex() {
		$_POST['m'] = MainGama::getApp()->getM();
		$_POST['u'] = MainGama::getApp()->getU();
		$_POST['a'] = MainGama::getApp()->getA();
		$_POST['acao'] = MainGama::getApp()->getAcao();
	} // eof redirecionaIndex


	/**
	 * Recupera um array associativo com os dados da requisi��o original.
	 *
	 */
	public function getRequest($pArr=array()) {
		return array('_GET' => $_GET, '_POST' => $_POST, '_REQUEST' => $_REQUEST);
	} // eof getRequest


	/**
	 * Recupera um array associativo com os dados da requisi��o original.
	 *
	 */
	public function setRequest($arr) {
		$_GET = $arr['_GET'];
		$_POST = $arr['_POST'];
		$_REQUEST = $arr['_REQUEST'];
	} // eof setRequest



	/**
	 * Retorna a vari�vel vinda no request, nomeada conforme passado
	 * por par�metro.
	 * Se n�o for passado nenhum par�metro, retorna um array com todas as
	 * vari�veis do request.
	 *
	 * @param string $chave
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getParms($chave=false,$default=null) {
		$valor = $default;
		if (!$chave) {
			$arr = (is_array($_REQUEST))?$_REQUEST:array();
			//			$arr = array_merge($arr,$_GET);
			$arr = array_merge((is_array($arr)?$arr:array()),$_POST);
			$arr = array_merge((is_array($arr)?$arr:array()),MainGama::getApp()->getSelfParms());
			$valor = $arr;
		} else {

			if (array_key_exists($chave,$_GET)) {
				$valor = $_GET[$chave];
			}

			if (array_key_exists($chave,$_POST)) {
				$valor = $_POST[$chave];
			}

			if (MainGama::getSelfParm($chave)) {
				$valor = MainGama::getSelfParm($chave);
			}


//			$valor = (MainGama::getSelfParm($chave))?MainGama::getSelfParm($chave):(isset($_POST[$chave])) ? $_POST[$chave] : ((isset($_GET[$chave])) ? $_GET[$chave] : $default);

			if (  (is_string($valor) && (strlen(trim($valor))==0)) || ($valor===false) ) {
				$valor = $default;
			}
		}
		return $valor;
	} // eof getParms


	public static function getSelfParm($chave) {
		if (array_key_exists($chave,MainGama::$selfParms)) {
			return MainGama::$selfParms[$chave];
		} else {
			return false;
		}
	} // getSelfParm


	/**
	 * Retorna um par�metro ou a lista completa, vindas pelo m�todo POST.
	 *
	 * @param string $chave
	 * @param mixed $default
	 * @return string
	 */
	public function getParmPost($chave=false,$default=null) {
		$valor = $default;
		if (!$chave) {
			$valor = $_POST;
		} else {
			$valor = (array_key_exists ($chave, $_POST)) ? $_POST[$chave] : $default;
			if ( (strlen(trim($valor))==0) || ($valor===false) ) {
				$valor = $default;
			}
		}
		return $valor;
	} // eof getParmPost


	/**
	 * Verifica se o usu�rio em quest�o possui privil�gios para acessar uma
	 * determinada transa��o (m�dulo/sub-m�dulos/action/a��o).
	 *
	 * @return boolean
	 */
	function checkAutorizacao() {
		// Verificando a autoriza��o para a a��o
		try {
			$bo = new SysAutorizacaoBO();
			$id = $this->getSess()->getProfile()->getUsuario()->getID();
			$resp = $bo->estaAutorizado($id,$this->getM(),$this->getU(),$this->getA(),$this->getAcao());
		} catch (SysException $e) {
			if ($this->getStatus() == MainGama::ST_PREPARACAO) {
				$this->sendJSON_ALERTA($e->getMessage());
			} else {
				$this->setException($e);
				$this->showMensagemErro();
				//				$this->redirecionaActionLogin();
				//				echo 'Opa!!!';
			}
		}
	} // eof checkAutorizacao




	/**
	 * Exibe a mensagem de erro, informando dados adicionais como as vari�veis
	 * e par�metros do sistema.
	 *
	 * @param SysException $e
	 */
	function showMensagemErro($e=false) {
		if (!$e) {
			$e = $this->getException(true);
		}
		$this->getSmarty()->assign('exception',$e);

		$dump = array('exception' => $e,'parms' => $this->getParms());
		$this->getSmarty()->assign('dump',	chunk_split(base64_encode(var_export($dump,true))));

		$this->getSmarty()->assign('parms',	$this->getParms());
		$this->getSmarty()->assign('user',$this->getSess()->getProfile()->getUsuario());
		$this->getSmarty()->assign('debug',MainGama::getApp()->getConfig('debug',false));
		ob_end_clean();
		$this->getSmarty()->display('erro.tpl');
		print_r(MainGama::getApp()->getSess()->getProfile()->getTransacoesPermitidas());
		exit;
	} // eof showMensagemErro



	/**
	 * M�todo que verifica se existe alguma configura��o adicional para o mesmo sistema,
	 * em diret�rios virtuais (alias) diferentes, e os carrega.
	 */
	function checkVirtualDirectoryRequest() {
		$path = $this->pathArqConf . 'param.ini';

		$dados = parse_url($_SERVER['REQUEST_URI']);


		if (substr($dados['path'],0,-1) != '/') {
			$dados['path'] .= '/';
		}


		if(!strstr($dados['path'],$this->getRootScript())) {
			$dados['path'] .= $this->getRootScript();
		}



		$chave = substr(strrchr(dirname($dados['path']),'/'),1);

		//		$this->getDebug(true)->log($chave,'dbg');

		if (strlen($chave) > 0)	{
			if (file_exists($path)) {
				$arr = parse_ini_file($path,true);
				if (array_key_exists ($chave, $arr )) {
					foreach ($arr[$chave] as $k => $v) {
						$this->config[$k] = $v;
					}
					if (array_key_exists ('url_adicional', $this->config)) {
						$arr = explode('&',$this->config['url_adicional']);
						foreach ($arr as $aux) {
							list($k,$v) = explode(':',$aux);
							$this->setParm($k,$v);
						}
					}
				}
			}
		}
	} // eof checkVirtualDirectoryRequest


	/**
	 * Retorna o nome da pasta onde encontra-se o sub-sistema de controle
	 * de usu�rios, transa��es e permiss�es.
	 *
	 * @return string
	 */
	function getModSysPath() {
		return 'sistema';
	} // eof getModSysPath




	/**
	 * Define o valor da vari�vel est�tica $BASE_PARMS_REDIR.
	 *
	 * @param array $parms
	 */
	public static function setParmsRedir($parms) {
		self::$BASE_PARMS_REDIR = array_merge( self::getParms(), $parms);
	}

	/**
	 * Recupera o valor da vari�vel est�tica $BASE_PARMS_REDIR.
	 *
	 * @return array
	 */
	public static function getParmsRedir() {
		return self::$BASE_PARMS_REDIR;
	}


	/**
	 * Decodifica os par�metros passados pelo script "anterior"
	 *
	 * @param string $s Linha codificada com os par�metros
	 * @param boolean $apenasRetornaParms true, se for apenas para decodificar e retornar
	 * @return array
	 */
	public static function decodeParmsRedir($s,$apenasRetornaParms=false) {

		$s = base64_decode($s);

		$tokens = explode("&",$s);

		$arr = array();
		foreach ($tokens as $token) {
			list($k,$v) = explode("=",$token);
			if (!empty($k)) {
				$arr[$k] = $v;
			}
		}

		if ($apenasRetornaParms) {
			return $arr;
		}


		$arrIgnorar = array();
		$arrIgnorar[] = 'gama'.self::getApp()->getM();
		$arrIgnorar[] = '__utmmobile';

		// Para evitar a sobreposi��o de vari�veis, dou prefer�ncia para o que �
		// passado diretamente na URL, e n�o no bloco codificado...
		foreach ($arr as $k => $v) {
			if (!self::getApp()->getParms($k,false)) {
				if (!in_array($k,$arrIgnorar)) {
					self::getApp()->setParm($k,$v);
/*				} else {
					echo '<br> Ignorando '. $k;*/
				}
				//echo "<br> $k = $v ";
			}
		}

		return $arr;
	}



	/**

	 * Realiza o redirecionamento para o index desejado, passando como
	 * parametros:
	 *
	 * 1. Os parametros definidos atraves do metodo 'setParmsRedir' (array associativo estatico)
	 * 2. Os parametros passados atraves do $parmsAdicionais (string, no formato da URI)
	 *
	 * @param string $url
	 * @param string $parmsAdicionais
	 */
	public function doRedir($url,$parmsAdicionais=null) {
		$redir = "Location: http://$url?gama_redir=1";


		if (!is_null($parmsAdicionais)) {
			$redir .= "&" . $parmsAdicionais  ;
		}


		$parmsRedir = self::getParmsRedir();
		if (!empty($parmsRedir)) {
			$s = '';
			foreach ($parmsRedir as $k => $v) {
				$s .= $k . '=' . urlencode($v) . '&';
			}
			$redir .= "&parms=".base64_encode($s);
		}

		ob_end_clean();
		header($redir);
		exit;
	}


	/**
	 * Recupera a raiz do diret�rio onde est� instalado o sistema
	 *
	 * @return string
	 */
	public function getBasePath() {
		$basePath = dirname(__FILE__);
		$basePath = substr($basePath,0,-13);
		$basePath = str_replace("\\",'/',$basePath);

		return $basePath;
	} // eof getBasePath



	/**
	 * M�todo destrutor da classe
	 *
	 * @deprecated
	 */
	function __destruct(){
		/*if ($this->getStatus() == MainGama::ST_EXECUCAO) {
			if ($this->getConfig('debug',false)) {
				foreach ($this->listaConexoes as $k => $con) {
					$this->listaConexoes[$k]->defLogger3();
				}
				$this->getLogger3()->renderHTML();
				//				echo "TERMINANDO... " . get_class($this);
			}
		}*/
	} // __destruct



	/**
	 * Carrega o arquivo da classe, dados os par�metros
	 *
	 * @param array|string $addr
	 * @return boolean true se o arquivo foi informado e carregado
	 *
	 */
	public function loadClass($addr=false) {


		if ($addr === false) {
			return;
		} else if (is_array($addr)) {
			list($nomeArquivoClasse,$m,$u) = $addr;
		} else {
			$nomeArquivoClasse = $addr;
			$m = $this->getM();
			$u = $this->getU();
		}

		if ($nomeArquivoClasse) {
			$path = $this->getModPath();
			$path .= $m . '/';

			if (!is_null($u)) {
				$path .= $u . '/';
			}
			require_once($path . $nomeArquivoClasse);
		}
	} // eof getClass





	/**
	 * Carrega o objeto de uma classe, e instancia-a, devolvendo-a para o requisitante.
	 *
	 * @param string $nomeClasse
	 * @param array|mixed $parametros
	 * @param false|array $addr
	 * @return unknown
	 */
	function getObj($nomeClasse,$parametros,$addr=false) {

		($addr!==false)?$this->loadClass($addr):false;

		if (is_array($parametros)) {
			$ls = array_values($parametros);
			switch (count($parametros)) {
				case 1: $x = new $nomeClasse($ls[0]); break;
				case 2: $x = new $nomeClasse($ls[0],$ls[1]); break;
				case 3: $x = new $nomeClasse($ls[0],$ls[1],$ls[2]); break;
				case 4: $x = new $nomeClasse($ls[0],$ls[1],$ls[2],$ls[3]); break;
				case 5: $x = new $nomeClasse($ls[0],$ls[1],$ls[2],$ls[3],$ls[4]); break;
			}
		} else if (is_null($parametros)) {
			$x = new $nomeClasse();
		} else {
			$x = new $nomeClasse($parametros);
		}
		return $x;
	}

	/**
	 * Imprime o conte�do do objeto passado por par�metro, ou do MainGama, se
	 * este for omitido.
	 *
	 * @param mixed $obj
	 */
	public function print_r($obj=false) {
		echo '<pre>';
		if ($obj === false) {
			print_r($this);
		} else {
			print_r($obj);
		}
		echo '</pre>';
	} // eof print_r


	/**
	 * Retorna a revis�o e outras informa��es de configura��o.
	 *
	 * @return array
	 */
	public function getVersao() {
		$Rev = '$Rev: 689 $';
		$HeadURL = '$HeadURL: svn://192.168.150.165/base_gama3/branches/rev-4.0/lib/gama/base/Main.php $';
		$Id = '$Id: Main.php 689 2014-01-29 16:51:05Z eduluz $';

		$nRev = sscanf(substr($Rev,5),"%d");
		$url = substr($HeadURL,36,-2);

		return array('versao' => $nRev[0],'url' => $url);
	} // eof getVersao


	/**
	 * Verifica se h� algum comando administrativo.
	 *
	 */
	protected function checkAdminRequest() {
		//$this->getDebug(true)->log($_GET);

		switch ($_GET['cmdGamaAdmin']) {
			case 'RequestGamaInfo' 			: $this->showGamaInfo(); 				break;
			case 'MakeManifesto' 			: $this->makeManifesto(); 				break;
			case 'ShowPainelAdministrativo' : $this->showPainelAdministrativo(); 	break;
			case 'ShowJsonQuery' 			: $this->showPainelJsonQuery(); 		break;
			case 'ShowPainelTest' 			: $this->showPainelTeste(); 			break;
			case 'compactaJS' 				: $this->compactaJS(); 					break;
		}

	} // checkAdminRequest


	/**
	 * Verifica se o diret�rio existe, e se n�o existir, cria.
	 *
	 * @param unknown_type $path
	 */
	public function checkDir($path) {
		if (!is_dir($path)) {
			mkdir($path);
		}
	} // checkDir




	/**
	 * M�todo que compacta o c�digo JS do ExtJS.
	 *
	 */
	public function compactaJS(){
		$this->setFormatoTratamentoErro(MainGama::FTE_JSON);
		$sufixo = $this->getParms("sufixo") ? "-".$this->getParms("sufixo") : "";
		try{

			if(!$this->getParms("u",false)){
				echo "O Subm�dulo deve ser informado para a compacta��o dos Arquivos JS.";
				exit;
			}


			$pathToGama = MainGama::getApp()->getBasePath();
			$pathToJS = $this->getModPath() . $this->getM(). "/interface_web/temp";

			$conversor = new ConversorIncludeJS( $this->getModPath() . $this->getM(). "/interface_web/IncludeJS".$sufixo.".ini", $this->getSmarty(), $sufixo);


			//Cria Arquivo
			$fp = fopen("{$pathToGama}IncludeJS.jsb2", "w");
			$escreve = fwrite($fp, $conversor->toJSB2());
			fclose($fp);


			//Executa JAVA da compacta��o
			$handle = popen("java -jar {$pathToGama}lib/gama/interface_web/JSBuilder2.jar --projectFile {$pathToGama}IncludeJS.jsb2 --homeDir {$pathToGama} ", 'r');
			$read = fread($handle, 2096);
			pclose($handle);

			//Exclui arquivo
			unlink("{$pathToGama}IncludeJS.jsb2");

			die( "Arquivos compactados com sucesso.!");
		}catch(SysException $e){
			die($e->getMessage());
		}
	} // compactaJS



	protected function getValorVariavel($var) {
		if (is_bool($var)) {
			return ($var)?'true':'false';
		} else {
			return $var;
		}
	} // getValorVariavel


	/**
	 * Recupera a lista de par�metros de configura��o
	 *
	 * @param MainGama $gama
	 * @return array
	 */
	protected function getAllParmsConfig($gama) {
		$listaAux = $gama->getConfig();
		$listaParmConfig = array();
		foreach ($listaAux as $chave => $valor) {
			if (is_array($valor)) {
				foreach ($valor as $chave2 => $valor2) {
					if (substr(trim($chave2),0,1) != '#') {
						if ($chave2 != 'db_pass') {
							$listaParmConfig[$chave. '.' . $chave2] = $gama->getValorVariavel($valor2);
						}
					}
				}
			} else {
				if (substr(trim($chave),0,1) != '#') {
					if ($chave != 'db_pass') {
						$listaParmConfig[$chave] = $gama->getValorVariavel($valor);
					}
				}
			}
		}
		return $listaParmConfig;
	} // getAllParmsConfig




	/**
	 * Exibe informa��es da vers�o do Gama
	 *
	 */
	protected function showGamaInfo() {
		$listaParmConfig = $this->getAllParmsConfig($this);


		$pathArqManifesto = './mod/'.$this->getM().'/manifesto.gm';
		$listaParmManifesto = array();
		if (file_exists($pathArqManifesto)) {
			$listaParmManifesto = parse_ini_file($pathArqManifesto);
		}

		$listaParmRequest = $this->getParms();


		$this->getSmarty()->assign('app',$this);
		$this->getSmarty()->assign('ver',$this->getVersao());
		$this->getSmarty()->assign('server',$_SERVER);
		$this->getSmarty()->assign('listaParmConfig',$listaParmConfig);
		$this->getSmarty()->assign('listaParmRequest',$listaParmRequest);
		$this->getSmarty()->assign('listaParmManifesto',$listaParmManifesto);
		$this->getSmarty()->assign('lsComandosAdministrativos',self::$lsComandosAdministrativos);
		//		$this->getSmarty()
		$this->getSmarty()->display('gama_info.tpl');
		phpinfo();
		exit;
	} // showGamaInfo



	/**
	 * Cria um arquivo com a defini��o de configura��o do Gama e do m�dulo selecionado;
	 *
	 */
	protected function makeManifesto() {

		$lista = array();

		$lista['host.deploy'] = '"'.php_uname().'"';

		$lista['php.versao'] = phpversion();
		$lista['php.allow_call_time_pass_reference'] = ini_get('allow_call_time_pass_reference');
		$lista['php.allow_call_time_pass_reference'] = ini_get('allow_call_time_pass_reference');
		$lista['php.always_populate_raw_post_data'] = ini_get('always_populate_raw_post_data');
		$lista['php.auto_prepend_file'] = ini_get('auto_prepend_file');
		$lista['php.default_mimetype'] = ini_get('default_mimetype');
		$lista['php.display_errors'] = ini_get('display_errors');
		$lista['php.error_append_string'] = ini_get('error_append_string');
		$lista['php.error_log'] = ini_get('error_log');
		$lista['php.error_reporting'] = ini_get('error_reporting');
		$lista['php.file_uploads'] = ini_get('file_uploads');
		$lista['php.memory_limit'] = ini_get('memory_limit');
		$lista['php.post_max_size'] = ini_get('post_max_size');
		$lista['php.register_argc_argv'] = ini_get('register_argc_argv');
		$lista['php.register_globals'] = ini_get('register_globals');
		$lista['php.register_long_arrays'] = ini_get('register_long_arrays');
		$lista['php.report_memleaks'] = ini_get('report_memleaks');
		$lista['php.safe_mode'] = ini_get('safe_mode');
		$lista['php.sql.safe_mode'] = ini_get('sql.safe_mode');
		$lista['php.variables_order'] = ini_get('variables_order');


		$listaExtensoesCarregadas = get_loaded_extensions();

		foreach ($listaExtensoesCarregadas as $k => $v) {
			$kAux = '';
			if ($v == 'curl') {

				$v = '"' . $v;

				$aux = curl_version();
				$v .= '  v. ' . $aux['version'] ;
				$v .= ' libz='.$aux['libz_version'];
				$v .= ' ssl='.$aux['ssl_version'];
				$v .= '"';

			} elseif ($v == 'gd') {
				$aux = gd_info();
				$v = '"' . $v;
				$v .= '  v.' . $aux['GD Version'] . '"';
			} elseif ($v == 'mysql') {
				$v = '"' . $v;
				$v .= '  v. ' . mysql_get_client_info() . '"';
			} elseif ($v == 'mysqli') {
				$v = '"' . $v;
				$v .= '  v. ' . mysqli_get_client_info() . '"';
			}


			$lista['ext.'.($k+1).$kAux] = $v;
		}

		$tags = array('versao' => '$'.'Rev: $', 'url'=>'$'.'HeadURL: $');




		$this->getSmarty()->assign('tag',$tags);
		$this->getSmarty()->assign('lista',$lista);
		$this->getSmarty()->assign('listaApache',apache_get_modules());
		$this->getSmarty()->assign('ver',$this->getVersao());
		$texto = $this->getSmarty()->fetch('manifesto.tpl');

		$pathArqManifesto = './mod/'.$this->getM().'/manifesto.gm';
		$fp = fopen($pathArqManifesto,'w');
		fputs($fp,$texto);
		fclose($fp);
		echo 'Manifesto criado com sucesso';
		exit;
	} // makeManifesto


	protected function showPainelAdministrativo() {
		$this->getSmarty()->assign('m',$this->getM());
		$this->getSmarty()->assign('u',$this->getU());
		$this->getSmarty()->assign('a',$this->getA());
		$this->getSmarty()->assign('acao',$this->getAcao());
		$this->getSmarty()->display('admin/painel_administrativo.tpl');
		exit;
	}


	protected function showPainelJsonQuery() {
		$this->getSmarty()->assign('m',$this->getM());
		$this->getSmarty()->assign('u',$this->getU());
		$this->getSmarty()->assign('a',$this->getA());
		$this->getSmarty()->assign('acao',$this->getAcao());
		$this->getSmarty()->display('admin/painel_json_query.tpl');
		exit;
	}


	protected function showPainelTeste() {
		$this->getSmarty()->assign('m',$this->getM());
		$this->getSmarty()->assign('u',$this->getU());
		$this->getSmarty()->assign('a',$this->getA());
		$this->getSmarty()->assign('acao',$this->getAcao());

//		if ($this->getParms('doTestar')) {
			$suite = $this->getParms('suite');
			$lsSuites = array();

			$g = new GamaUnit();
			$lsSuites = $g->getListaSuites($this->getM(),$this->getU());

			$respostas = array();

			if ($this->getParms('doTestar','N') == 'S') {
				$respostas = $g->executaSuiteTestes($this->getM(),$this->getU(),$suite);
			}

			$this->getSmarty()->assign('respostas',$respostas);
			$this->getSmarty()->assign('suite',$suite);
			$this->getSmarty()->assign('lsSuites',$lsSuites);
			$this->getSmarty()->assign('msg','testando...');
//		}

		$this->getSmarty()->display('admin/painel_testes.tpl');
		exit;
	}


// �rea onde est� o que eu achava que n�o precisava, mas precisa...



	/**
	 *
	 * @param string $acao
	 * @param string $m
	 * @param string $u
	 * @param string $a
	 * @param array $parmsAdicionais
	 */
	function redireciona($acao=false,$m=false,$u=false,$a=false,$parmsAdicionais=array()) {

		$m = (($m)?$m:(is_null($m)?$this->getM():null));
		$u =  (($u)?$u:(is_null($u)?$this->getU():null));
		$a = (($a)?$a:(is_null($a)?$this->getA():null));
		$acao = (($acao)?$acao:(is_null($acao)?$this->getAcao():null));

		$url = $this->getRootScript() . '?';
		if (!is_null($m)) { $url .= "&m=$m"; }
		if (!is_null($u)) { $url .= "&u=$u"; }
		if (!is_null($a)) { $url .= "&a=$a"; }
		if (!is_null($acao)) { $url .= "&acao=$acao"; }


		foreach ($parmsAdicionais as $k => $v) {
			$url .= "&$k=$v";
		}

		//$this->limpaBufferEnvio();


		header('Location: ' . $url);
                //exit;

		//		$this->registerEndExec($m,$u,$a,$acao);
		echo "
		<html>
		<header>
		<meta http-equiv='refresh' content='0;url=$url'>
		</header>
		<body>
			<script>
				location.href='$url';
			</script>
		</body>
		</html>
		";
		exit;
	} // eof redireciona

        
        /**
         * Retorna a path absoluta de onde est� hospedado o sistema 
         * @return string
         */
        public function getServerRootDir() {
            return substr(dirname(__FILE__),0,-14);
        }

        
    /**
     * Realiza a limpeza de todas as sa�das
     */
    public function limpaBufferEnvio() {
        ob_end_clean();

        $i = ob_get_level();
        while ($i) {
            ob_end_clean();
            $i = ob_get_level();
        } // 
        ob_end_clean();
    }

} // EOC MainGama

function getGamaMicrotime($fullTime=false) 
{ 
    list($usec, $sec) = explode(" ", microtime()); 
    $s = (float) date('s');
    if ($fullTime) {
        return $s + ((float) $usec) ; 
    } else {
        return ((float)$usec + (float)$sec); 
    }
} 

?>