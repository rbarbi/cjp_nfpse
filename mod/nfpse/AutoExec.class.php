<?php
setlocale(LC_ALL, "pt_BR.ISO8859-1");
if (!function_exists("pre")) {

    function pre($x, $titulo = '', $exit = false)
    {
        ob_implicit_flush();
        $pid = getmypid();
        echo "<fieldset style='min-width: 50%; word-wrap: break-word; background-color: #FAFAFA; border: 2px groove #ddd !important; padding: 1.4em 1.4em 1.4em 1.4em !important;'>";
        if (!empty($titulo)) {
            echo "<legend style='color:rgb(0, 0, 123); padding: 3px 10px 3px 10px; font-weight: bold; font-size: 14px; text-transform: uppercase; border: 1px groove #ddd !important;'> $titulo </legend>";
        }
        echo "<pre>";
        echo "----------------------------\r\nProcesso PID: {$pid}\r\n----------------------------\r\n\r\n";
        print_r($x);
        echo "</pre>";
        echo "</fieldset>";
        ob_flush();
        flush();
        if ($exit) {
            exit;
        }
    }

    function pred($x, $titulo = '')
    {
        pre($x, $titulo, true);
    }
}
class AutoExecGama extends MainGama
{

    /**
     * @return AutoExecGama
     */
    final function __construct()
    {

        // inicia a aplica��o
        parent::__construct();

        // Define a constante que ser� utilizada na sess�o de usu�rio
        $this->_createSessionKey();

        // Registra o Autoload para carregar o que for de extra para o webservice
        $this->_registerSelfAutoload();

        // Defini��o de constante que o administrativo do gama precisa
        define("MOSTRAR_ERRO", 999);
    }

    /**
     * CHAVE_SESSAO � a base para o sistema de seguran�a
     * Mesmo se tratando de uma constante, por ter uma import�ncia chave na aplica��o, possui um m�todo exclusivo
     */
    private final function _createSessionKey()
    {
        if (!defined("CHAVE_SESSAO")) {
            define("CHAVE_SESSAO", md5($_SERVER["HTTP_HOST"]."�".$_SERVER["SCRIPT_NAME"]));
        }
    }

    /**
     * Registra um autoload pr�prio
     */
    private final function _registerSelfAutoload()
    {
        // Carrega as classes padr�o que forem sendo instanciadas
        spl_autoload_register(array($this, "autoloadNfpse"));
    }

    /**
     * Sobrescrevendo, pois este m�dulo precisa desse m�todo
     * @return boolean
     */
    public function gravaTrace2()
    {
        return true;
    }

    /**
     * Sobrescrevendo, pois este m�dulo n�o pode chamar este m�todo original devido ao mesmo usar o Smarty
     * @return boolean
     */
    protected function defHookTrataErro()
    {
        return true;
    }

    /**
     * Sobrescrevendo, pois este m�dulo n�o precisa tentar carregar VO, BO, etc
     * @return boolean
     */
    private function _autoload_mod()
    {
        return true;
    }

    /**
     * Sobrescrevendo, pois este m�dulo n�o precisa fazer checagem extra de configura��es INI
     * @return boolean
     */
    public function checkVirtualDirectoryRequest()
    {
        return true;
    }

    /**
     * Sobrescrevendo, pois este m�dulo n�o precisa fazer a inclus�o do Smarty nem das classes ADODB
     * @return boolean
     */
    public function includes()
    {
        return true;
    }

    /**
     * Sobrescrevendo, pois este m�dulo n�o far� nenhuma conex�o com o banco
     * @return boolean
     */
    public function conectaDB()
    {
        return true;
    }

    /**
     * Sobrescrevendo, pois este m�dulo n�o precisa desse m�todo
     * @return boolean
     */
    protected function checkAdminRequest()
    {
        return true;
    }

    /**
     * Sobrescrevendo, pois este m�dulo n�o far� nenhuma conex�o com o banco
     * @return boolean
     */
    protected function checkDatabase()
    {
        return true;
    }

    /**
     * Sobrescrever m�todos de autentica��o do gamma, pois a autentica��o ser� refeita em n�vel do webservice
     * @return boolean
     */
    public function checkLogin()
    {
        return true;
    }

    /**
     * Sobrescrevendo para caso o mesmo for invocado n�o incluir arquivos ou qualquer
     * outra a��o referente ao Smarty, no escopo do m�dulo WS
     * @return boolean
     */
    public function getSmarty($clone = false)
    {
        return true;
    }

    /**
     * Sobrescrevendo, pois o m�todo original utiliza o Smarty, por�m esse m�dulo ser� s� para processar requisi��es e retornar JSON
     * @param type $path
     * @param type $GET
     * @param type $POST
     */
    protected function processaInterfaceNormal($path, $GET, $POST)
    {
        //pre($GET, '_get no AutoExec', false);
        //pre($POST, '_post no AutoExec', false);
        //pre(MainGama::getApp()->getParms(), 'parms no AutoExec', false);
        //pred("path no AutoExec: {$path}");

        require_once($path);
        $nomeAction = $this->getA()."Action";
        $action     = new $nomeAction($this, $GET, $POST);
        $action->exec();
    }

    /**
     * M�todo invocado pelo interpretador, quando a classe requisitada n�o est� carregada.
     * 
     * - Por padr�o procurar� as classes no array $arr;
     * - Se n�o encontrar, passa para a sequ�ncia do IF para ver se o nome passado � uma classe v�lida;
     * - o $className � passado pelo interpretador do PHP, quando se tenta instanciar uma classe
     *   sendo que esta ainda n�o foi carregada.
     *
     * @param string $className
     */
    final function autoloadNfpse($className)
    {
        /**
         * Para classes extra ------------------------------------------------------------------------------------------------------
         * Exemplo caso for adicionar: 'NomeDaClasse' => './mod/'.MainGama::getApp()->getM().'/caminhoDoArquivo.abc.php'
         */
        $arr = array();

        /*
         * Para classes padr�o do sistema ------------------------------------------------------------------------------------------
         */
        if (key_exists($className, $arr)) {
            $path = $arr[$className];
            require_once($path);
        } else if ($this->_requireServiceDaoActionClassFile($className)) {
            return;
        } else if ($this->_requireClassFile(array('./mod/'.MainGama::getApp()->getM().'/lib/'.$className.'.class.php'))) {
            return;
        }

        /*
         * Para classes que usam namespaces ----------------------------------------------------------------------------------------
         */
        $arrNamespaces = array(
            'NFePHP\\Common\\' => array(__DIR__.'/lib/assinatura-digital-xml/src')
        );

        foreach ($arrNamespaces as $indNamespace => $caminhoArquivo) {
            $lengthNamespace = strlen($indNamespace);

            if (strpos($className, '\\') !== false) {
                $classFile = $caminhoArquivo[0].DIRECTORY_SEPARATOR.substr(str_replace('\\', '/', $className), $lengthNamespace).'.php';

                if (file_exists($classFile)) {
                    require_once($classFile);
                }
            }
        }
    }

    /**
     * Autoload para namespaces da assinatura digital do XML
     * @param string $className
     * @return type
     */
    final function autoloadNamespaces($className)
    {
        $arr = array(
            'NFePHP\\Common\\' => array(__DIR__.'/lib/assinatura-digital-xml/src')
        );
        //pred($arr, false);

        foreach ($arr as $indNamespace => $caminhoArquivo) {
            $lengthNamespace = strlen($indNamespace);
            //pred($className, false);
            //pred($caminhoArquivo[0], false);
            
            if (strpos($className, '\\') !== false) {
                $classFile = $caminhoArquivo[0].DIRECTORY_SEPARATOR.substr($className, $lengthNamespace).'.php';
                if (!is_file($classFile)) {
                    require_once($classFile);
                }
            }
        }
    }

    /**
     * Carregar� automaticamente uma classe que for instanciada antes de ser carregada
     * @param string $classPaths
     * @return boolean
     */
    private function _requireClassFile($classPaths)
    {
        foreach ($classPaths as $classPath) {
            clearstatcache();
            if (file_exists($classPath)) {
                require_once $classPath;
                return true;
            }
        }
        return false;
    }

    /**
     * Carregar� automaticamente as classes do "ws" (Services) e
     * do "apoio_forense" (Action, DAO, etc), quando uma delas for instanciada
     *
     * @param string $className
     * @return boolean
     */
    private function _requireServiceDaoActionClassFile($className)
    {
        if (strpos($className, "Service") !== false) {
            /**
             * Carrega os Services do m�dulo "nfpse"
             */
            $className = str_replace("Service", "", $className);

            $classPaths = array(
                './mod/nfpse/main/service/'.$className.'.service.php'
            );
            return $this->_requireClassFile($classPaths);
        } else if (strpos($className, "Action") !== false) {
            $className  = str_replace("Action", "", $className);
            $classPaths = array(
                './mod/nfpse/main/'.$className.'.action.php'
            );
            return $this->_requireClassFile($classPaths);
        } else if (strpos($className, "DAO") !== false || strpos($className, "Dao") !== false) {
            $className  = str_replace("DAO", "", str_replace("Dao", "", $className));
            $classPaths = array(
                './mod/nfpse/main/dao/'.$className.'.dao.php'
            );
            return $this->_requireClassFile($classPaths);
        } else {
            return false;
        }
    }
}
$autoexec = new AutoExecGama();
return $autoexec;
