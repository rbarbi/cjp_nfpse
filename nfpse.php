<?php
//exit(phpinfo());
error_reporting(E_ALL);

if (!function_exists("pre")) {

    /**
     * @param array $x
     * @param string $titulo
     * @param boolean $exit
     */
    function pre($x, $titulo = '', $exit = false)
    {
        ob_implicit_flush();

        echo "<fieldset style='min-width: 50%; word-wrap: break-word; background-color: #FAFAFA; border: 2px groove #ddd !important; padding: 1.4em 1.4em 1.4em 1.4em !important;'>";
        if (!empty($titulo)) {
            echo "<legend style='color:rgb(0, 0, 123); padding: 3px 10px 3px 10px; font-weight: bold; font-size: 14px; text-transform: uppercase; border: 1px groove #ddd !important;'> $titulo </legend>";
        }
        echo "<pre>";
        print_r($x);
        echo "</pre>";
        echo "</fieldset>";
        ob_flush();
        flush();
        if ($exit) {
            exit;
        }
    }

    /**
     * @param array $x
     * @param string $titulo
     */
    function pred($x, $titulo = '')
    {
        pre($x, $titulo, true);
    }
}
try {
    require_once('./lib/gama/base/Main.php');

    /*
     * Array com dados de um POST para testes.
     * Assim que iniciar os testes no Postman ou via sistemas das empresas, comentar este.
     */
    if (isset($_GET['acao']) && $_GET['acao'] == 'gerarNotaAction') {
        $_POST = array(
            'emp' => "emedaux",
            'dadosNota' => array(
                'emp'                        => 'emedaux',
                'tipo_nota'                  => 'N',
                'cod_empresa'                => 4485,
                'tipo_saep'                  => 1,
                'cst'                        => 0,
                'cfps'                       => 9201,
                'cmc_inscr_municipal'        => '0885258',
                'ali_desconto'               => 5,
                'nome_nota'                  => 'Ricardo Barbi dos Santos',
                'tipo_pessoa'                => 'F',
                'cpf_cnpj'                   => '00601432908',
                'observacao'                 => '',
                'uf'                         => 'SC',
                'cidade'                     => 'Florianopolis',
                'bairro'                     => 'Itaguaçú',
                'logradouro'                 => 'Lauro Bustamante',
                'numero'                     => '10',
                'complemento'                => '',
                'cep'                        => '88085-590',
                'cod_ibge'                   => 4205407,
                'email'                      => 'ricardobarbi@gmail.com',
                'telefone'                   => '',
                'descricao'                  => 'Taxa ADM. 2018 Lote B4/96',
                'codigo_boleto'              => 1,
                'data_vencimento'            => '2018-10-30 00:00:00',
                'pagamento_data_retorno'     => '2018-10-30 08:08:02',
                'valor_unitario'             => 997.00,
                'vencimento_original_boleto' => '2018-10-30 00:00:00'
        ));
    }

    // Seta somente o m(módulo), pois o resto default é definido no AutoExec.ini (u, a, acao)
    $_GET["m"]  = "nfpse";
    $_POST["m"] = "nfpse";

    /**
     * Recupera a instância do AutoExec.class, porque estou passando o _GET["m"] (módulo)
     * para buscar esta classe, incluir e instanciar conforme o módulo passado, caso contrário
     * retorna uma instância ao MainGama que tem login, conexão ao banco e tudo mais padrão
     */
    $app = MainGama::getInstanceOf();

    // Com o autoexec instanciado e sobrescrevendo tudo que preciso do MainGama, inicio a aplicação
    echo $app->exec($_GET, $_POST);
} catch (Exception $e) {
    /**
     * Qualquer exceção fora do contexto da classe do webservice deverá manter o padrão da mesma,
     * com a formatação padrão da saída do tratamento de erros.
     */
    $dadosIni = array_merge($_POST, $_GET);

    $ret = array(
        'erros' => array(
            'codigo'   => $e->getCode(),
            'mensagem' => $e->getMessage()
        )
    );

    header('Content-Type: application/json; charset=UTF-8');
    $JSON = new JSONView($ret);
    echo $JSON->getDadosJSON();
}