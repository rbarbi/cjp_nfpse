<?php

// $Rev: 348 $ $Author: eduluz $ $Date: 2009-05-05 14:43:05 -0300 (ter, 05 mai 2009) $//

if (!function_exists("pre")) {

    function pre($x, $titulo = '')
    {
        echo "<fieldset style='min-width: 50%; word-wrap: break-word; background-color: #FAFAFA; border: 2px groove #ddd !important; padding: 1.4em 1.4em 1.4em 1.4em !important;'>";
        if (!empty($titulo)) {
            echo "<legend style='color:rgb(0, 0, 123); padding: 3px 10px 3px 10px; font-weight: bold; font-size: 14px; text-transform: uppercase; border: 1px groove #ddd !important;'> $titulo </legend>";
        }
        echo "<pre>";
        print_r($x);
        echo "</pre>";
        echo "</fieldset>";
    }

    function pred($x, $titulo = '')
    {
        pre($x, $titulo);
        die();
    }
}

if (!function_exists("_debug")) {

    function _convertMemoria($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i    = floor(log($size, 1024)))), 2).' '.$unit[$i];
    }

    function _debug($x = "", $exit = false)
    {
        //echo "<pre>";
        try {
            throw new Exception;
        } catch (Exception $exc) {

            //pred($exc);
             $separadorTrace  = "|";
             $separadorFuncao = "";
             
            //            if (isset($_SERVER["HTTP_USER_AGENT"])) {
            //                $separadorTrace  = "&rArr;";
            //                $separadorFuncao = "&uArr;";
            //            } else {
            //                $separadorTrace  = "-->";
            //                $separadorFuncao = "!";
            //            }

            //pred($_SERVER);

            $arr            = $exc->getTrace();
            $arquivo        = basename($arr[0]["file"]);
            $linha          = $arr[0]["line"];
            $funcaoAnterior = isset($arr[1]) ? $arr[1]["function"] : "";

            print_r("\n".date("H:i:s")." {$separadorTrace} {$arquivo} {$separadorTrace} {$linha} {$separadorTrace} {$separadorFuncao}{$funcaoAnterior}() {$separadorTrace} "._convertMemoria(memory_get_usage(true))." | ");

            if (is_array($x)) {
                print_r("\n<br/></br>");
                print_r($x);
                print_r("\n<br/></br>");
            } else {
                print_r($x."<br/>");
            }

            if ($exit) {
                exit();
            }
        }
        //echo "</pre>";
    }
}

/**
 * Classe que customiza o comportamento do MinGama, podendo capturar e processar
 * as transacoes pre-definidas;
 *
 * @author Eduardo Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package IADoc
 */
class AutoExecGama extends MainGama
{

    function AutoExecGama()
    {
        $this->MainGama();
        spl_autoload_register(array($this, "novo_autoload"));

        if ($this->getParms('logout', false)) {
            $this->getSess()->setProfile(null);
        }
    }

// AutoExecGama

    function redirecionaIndex()
    {
        $_POST['m'] = 'intranet_integracao';
    }

// redirecionaIndex
    //metodo para ver se avariavel da data inicial esta na sessão, se nao pega do banco e coloca na sessão
    //depois coloca no config
    function preExec()
    {
        $this->getCon()->SetFetchMode(ADODB_FETCH_ASSOC);
    }

    function checkLogin()
    {
        $arrayAcoesPermitidas   = array();
        $arrayAcoesPermitidas[] = "showFormFiltroRelatorio";
        $arrayAcoesPermitidas[] = "geraRelatorio";

        if (in_array($this->getAcao(), $arrayAcoesPermitidas)) {
            return true;
        }

        if ($this->getParms('doLogin', false)) {
            try {

                $user['usu_id'] = $this->getParms('usu_id');
                $this->getApp()->getSess()->set('usuario', $user);

                $profile = new SysProfile();

                $profile->getUsuario()->setID($user['usu_id']);
                MainGama::getApp()->getSess()->setProfile($profile);

                ob_end_clean();

                $this->checkAutorizacao();

                return true;

                // COLOCAR AQUI O JSON DE SUCESSO
            } catch (Exception $e) {
                // COLOCAR AQUI O JSON DE ERRO
                ob_end_clean();
                echo "{success: false, errors: { reason: '" . $e->getMessage() . "' }}";
                //					$this->showFormLogin($e->getMessage());
            }
            exit; // garante que termina aqui
        } else {
            if ($this->isLogged()) {
                //parent::checkLogin();
                $this->checkAutorizacao();

                return true;
            } else {
                echo "Sem permissao de autenticação";
                exit;
            }
        }
    }
// checkLogin

    /**
     * Voltei a retornar sempre true, pois vamos controlar o acesso no primeiro
     * momento, via menu - o que nÃ£o Ã© permitido nÃ£o serÃ¡ exibido.
     *
     * @return boolean
     */
    function checkAutorizacao()
    {
        $arrayIdsCargoSetoresAcessamSequencia = array();

        return true;
    }

// checkAutorizacao

    protected function novo_autoload($nomeClasse)
    {
        $arr = array(
            'ConversorValorMonetario'       => './lib/gama/comum/ConvertUtils.php',
            'VarianteService'               => './mod/intranet_integracao/main/service/Variante.service.php',
            'VarianteDAO'                   => './mod/intranet_integracao/main/dao/Variante.dao.php',
            'UFService'                     => './mod/intranet_integracao/main/service/UF.service.php',
            'ClienteService'                => './mod/intranet_integracao/main/service/Cliente.service.php',
            'HomologacaoService'            => './mod/intranet_integracao/main/service/Homologacao.service.php',
            'AdvogadoService'               => './mod/intranet_integracao/main/service/Advogado.service.php',
            'ProdutoService'                => './mod/intranet_integracao/main/service/Produto.service.php',
            
            'UFDAO'                         => './mod/intranet_integracao/main/dao/UF.dao.php',
            'ClienteDAO'                    => './mod/intranet_integracao/main/dao/Cliente.dao.php',
            'HomologacaoDAO'                => './mod/intranet_integracao/main/dao/Homologacao.dao.php',
            'ProdutoDAO'                => './mod/intranet_integracao/main/dao/Produto.dao.php',
            
            'NovoPromadService'            => './mod/intranet_integracao/novoPromad/service/NovoPromad.service.php',
            'NovoPromadDAO'                => './mod/intranet_integracao/novoPromad/dao/NovoPromad.dao.php',
            'MailerExportacao'             => './mod/intranet_integracao/lib/mail/MailerExportacao.class.php'
            
        );

        if (isset($arr[$nomeClasse])) {
            $path = $arr[$nomeClasse];
            require_once($path);
        }
    }
// novo_autoload
}

$autoexec = new AutoExecGama();
return $autoexec;
