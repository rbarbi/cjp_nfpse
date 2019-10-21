<?php

/**
 * Componente criado para prover m�todos relacionado ao XML da NFPS-e: gera��o do XML, XML de cancelamento, etc
 * @author Carlos Domingues <carlos.domingues@grupoapi.com.br>
 */
class UtilsNFPSe
{

    /**
     * M�todo que gera o XML da requisi��o de gera��o da NFPS-e para cada boleto selecionado
     *
     * @param array $dados      Os dados necess�rios para preencher a NFPS-e
     * @param string $aedf      AEDF que � definida conforme ambiente que est� acessando: homologa��o ou produ��o
     * @param string $idCNAE    CNAE que � definida no AutoExec.ini - por empresa
     * @return array
     */
    public static function gerarXMLrequisicao($dados, $aedf, $idCNAE)
    {
        //pre($dados, 'dados de um boleto na funcao de gerar XML', true);

        try {
            $xmlModelo = simplexml_load_file(dirname(__DIR__)."/modelo-xml/layout.xml");
            /**
             * Dados do tomador --------------------------------------------------------------------------------------------------------------
             */
            $xmlModelo->identificacaoTomador = str_replace(array(' ', '-', '/', '.'), '', $dados["cpf_cnpj"]);

            if (empty($dados["nome_nota"])) {
                $xmlModelo->razaoSocialTomador = '-';
            } else {
                $xmlModelo->razaoSocialTomador = $dados["nome_nota"];
            }

            if (empty($dados["telefone"])) {
                $xmlModelo->telefoneTomador = '-';
            } else {
                $xmlModelo->telefoneTomador = str_replace(array(' ', '-', '(', ')'), '', $dados["telefone"]);
            }

            if (empty($dados["email"])) {
                $xmlModelo->emailTomador = '-';
            } else {
                $xmlModelo->emailTomador = $dados["email"];
            }

            if (empty($dados["email"])) {
                $xmlModelo->emailTomador = '-';
            } else {
                $xmlModelo->emailTomador = $dados["email"];
            }

            if (empty($dados["cmc_inscr_municipal"])) {
                $xmlModelo->inscricaoMunicipalTomador = 0;
            } else {
                $xmlModelo->inscricaoMunicipalTomador = $dados["cmc_inscr_municipal"];
            }

            $xmlModelo->codigoMunicipioTomador = $dados["cod_ibge"];

            $xmlModelo->codigoPostalTomador = str_replace('-', '', $dados["cep"]);

            $xmlModelo->logradouroTomador = $dados["logradouro"];

            $xmlModelo->numeroEnderecoTomador = $dados["numero"];

            $xmlModelo->complementoEnderecoTomador = $dados["complemento"];

            $xmlModelo->bairroTomador = $dados["bairro"];

            $xmlModelo->ufTomador = $dados["uf"];

            $xmlModelo->paisTomador = 1058;

            /**
             * Dados da NFPS-e --------------------------------------------------------------------------------------------------------------
             */
            // Regra para c�lculo do ISSQN e valor base de c�lculo, calcular somente se al�quota for DIF ZERO
            $aliquota = number_format(($dados["ali_desconto"] / 100), 2);
            if ($aliquota != 0) {
                // Base de c�lculo do ISSQN (imposto sobre servi�os de qualquer natureza). - Fixo: 0.0
                $baseCalculoISSQN = $dados['valor_unitario'];
                $valorISSQN       = number_format(($baseCalculoISSQN * $aliquota), 2);
            } else {
                $baseCalculoISSQN = 0;
                $valorISSQN       = 0;
            }

            $xmlModelo->baseCalculo = $baseCalculoISSQN;

            // Conforme manual de integra��o deve-se definir como ZERO
            $xmlModelo->baseCalculoSubstituicao = 0;

            /**
             * C�digo fiscal de presta��o de servi�o. (OK API)
             *  CFPS|DESCRICAO
             *  -----------------------------------------------------------------------------------------------------------
             * 	9201|No Munic�pio, para Tomador ou Destinat�rio estabelecido ou domiciliado no Munic�pio
             * 	9202|No Munic�pio, para Tomador ou Destinat�rio estabelecido ou domiciliado fora do Munic�pio
             * 	9203|No Munic�pio, para Tomador ou Destinat�rio estabelecido ou domiciliado em outro estado da federa��o
             *  -----------------------------------------------------------------------------------------------------------
             * 	9204|No Munic�pio, para Tomador ou Destinat�rio estabelecido ou domiciliado no exterior
             * 	9205|Fora do Munic�pio para Tomador ou Destinat�rio estabelecido ou domiciliado no Estado de Santa Catarina
             * 	9206|Fora do Munic�pio para Tomador ou Destinat�rio estabelecido ou domiciliado em outro estado da federa��o
             * 	9207|Fora do Munic�pio para Tomador ou Destinat�rio estabelecido ou domiciliado no exterior
             * 	9208|No Munic�pio, em bens de terceiros por conta de Tomador ou Destinat�rio estabelecido ou domiciliado no Munic�pio
             * 	9209|No Munic�pio, em bens de terceiros por conta de Tomador ou Destinat�rio estabelecido ou domiciliado fora do Munic�pio
             * 	9210|No Munic�pio, em bens de terceiros por conta de Tomador ou Destinat�rio estabelecido ou domiciliado em outro estado da federa��o
             * 	9211|No Munic�pio, em bens de terceiros por conta de Tomador ou Destinat�rio estabelecido ou domiciliado no exterior
             */
            if (!empty($dados["cfps"])) {
                $xmlModelo->cfps = $dados["cfps"];
            } else {
                // Se a cidade do tomador for FLORIANOPOLIS (4205407 codigo ibge)
                if ($dados["cod_ibge"] == "4205407") {
                    $xmlModelo->cfps = "9201";
                }
                // Se a cidade do tomador n�o for FLORIANOPOLIS, mas for SC
                else if ($dados["uf"] == "SC") {
                    $xmlModelo->cfps = "9202";
                }
                // Se n�o � de SC
                else {
                    $xmlModelo->cfps = "9203";
                }
            }

            // Dados adicionais da nota fiscal, informa��o j� definida e utulizada na gera��o de XML anterior
            if ($dados['tipo_nota'] == 'POL') {
                // Para Infodigi, Iasoft e POL
                $porcentagemTributo = 17.59;
                $valorTributo       = number_format($dados['valor_unitario'] * ($porcentagemTributo / 100), 2, ",", ".");
                $dadosAdicionais    = "Referente ao contrato {$dados['codigo_contrato']} e a fatura {$dados['codigo_boleto']}.";
                $dadosAdicionais .= " Valor aproximado dos tributos com base na Lei 12.741/2012 R$ {$valorTributo} ({$porcentagemTributo}%) Fonte IBPT. Empresa optante pelo Simples Nacional.";
            } else {
                // Para API
                $venctoOriginal  = new \DateTime($dados['vencimento_original_boleto']);
                $dadosAdicionais = "Referente ao boleto {$dados['codigo_boleto']} com vencimento original em {$venctoOriginal->format('d/m/Y')} .";
                $dadosAdicionais .= " Valor aproximado dos tributos com base na Lei 12.741/2012 (PIS 0,65%, COFINS 3,00%, CSLL 2,88%, IRPJ 4,80%). Fonte: IBPT.";
            }
            $xmlModelo->dadosAdicionais = $dadosAdicionais;

            /**
             * Data da emiss�o da NFPS-e
             * PARA NF-e "NORMAIS":
             * - Se for consultado boletos com pagamento ANTERIOR ao primeiro dia do m�s atual,
             *   a emiss�o ser� o �ltimo dia do m�s da data de PAGAMENTO DO BOLETO
             * - Tarefa #29795: Ang�lica afirmou que a data se n�o for na regra acima, dever� ser a data
             *   do dia em que estiver gerando o arquivo com as notas para processar na prefeitura.
             *
             * PARA NF-e "ANTECIPADAS"
             * - Usar a data do dia em que est� gerando o arquivo
             */
            if ($dados['tipo_nota'] == 'N') {
                $dtRetorno          = new \DateTime($dados['pagamento_data_retorno']);
                $dtAtualPrimeiroDia = new \DateTime(date('Y').'-'.date('m').'-01');
                if ($dtRetorno < $dtAtualPrimeiroDia) {
                    $dtEmissao = $dtRetorno->format('Y').'-'.$dtRetorno->format('m').'-'.$dtRetorno->format('t');
                } else {
                    $dtEmissao = date('Y-m-d');
                }
            } else {
                // Aqui serve para Notas "Antecipadas da API" e notas geral da Infodigi, Iasoft e POL
                $dtEmissao = date('Y-m-d');
            }
            //$dtEmissao = date('Y-m-d'); // Decomentar esta linha para testes com boletos retroativos
            $xmlModelo->dataEmissao = $dtEmissao;

            /*
             * Informa��es de Autoriza��o para Emiss�o de Documentos Fiscais Eletr�nicos
             * Para homologa��o, o AEDF deve ser os seis primeiros d�gitos do CMC do emitente
             */
            $xmlModelo->numeroAEDF = $aedf;

            $xmlModelo->valorISSQN = $valorISSQN;

            // Conforme manual de integra��o deve-se definir como ZERO
            $xmlModelo->valorISSQNSubstituicao = 0;

            $xmlModelo->valorTotalServicos = $dados['valor_unitario'];

            $xmlModelo->itensServico->itemServico->aliquota = $aliquota;

            // C�digo da situa��o tribut�ria - Default API: 0
            if (!empty($dados["cst"])) {
                $xmlModelo->itensServico->itemServico->cst = $dados["cst"];
            } else {
                $xmlModelo->itensServico->itemServico->cst = 0;
            }

            // Descri��o do servi�o prestado - Fixo: Acompanhamento de publica��es judiciais
            if (!empty($dados['descricao'])) {
                $xmlModelo->itensServico->itemServico->descricaoServico = $dados['descricao'];
            } else {
                $xmlModelo->itensServico->itemServico->descricaoServico = 'Acompanhamento de publica��es judiciais';
            }

            // Identificador do C�digo de atividade
            $xmlModelo->itensServico->itemServico->idCNAE = $idCNAE;

            // Quantidade do servi�o prestado - Fixo: 1
            $xmlModelo->itensServico->itemServico->quantidade = 1;

            // Base de c�lculo do item (idem ao baseCalculo geral, pois nossas NFPS-e s�o de somente um item sempre)
            $xmlModelo->itensServico->itemServico->baseCalculo = $baseCalculoISSQN;

            // Valor total do servi�o prestado
            $xmlModelo->itensServico->itemServico->valorTotal = $dados['valor_unitario'];

            // Valor unit�rio do item de servi�o
            $xmlModelo->itensServico->itemServico->valorUnitario = $dados['valor_unitario'];

            //exit("XML gerado: ".$xmlModelo->asXML());

            /*
             * Ap�s popular os atributos do XML, gera a sa�da em string
             */
            return array('sucesso' => true, 'conteudoXML' => $xmlModelo->asXML());
        } catch (\Exception $ex) {
            return array('sucesso' => false, 'mensagemErro' => $ex->getMessage(), 'httpCode' => 400);
        }
    }

    /**
     * M�todo que gera o XML de cancelamento de uma NFPS-e
     *
     * @param integer   $nuAedf               AEDF que � definida conforme ambiente que est� acessando: homologa��o ou produ��o
     * @param integer   $nuNotaFiscal         N�mero de s�rie da nota
     * @param string    $codigoVerificacao    C�digo de verifica��o da nota
     * @param string    $motivoCancelamento   Motivo de cancelamento por extenso
     * @return array
     */
    public static function gerarXMLcancelamento($nuAedf, $nuNotaFiscal, $codigoVerificacao, $motivoCancelamento)
    {
        try {
            $xmlModelo = simplexml_load_file(dirname(__DIR__)."/modelo-xml/layout-cancelamento.xml");

            $xmlModelo->nuAedf             = $nuAedf;
            $xmlModelo->nuNotaFiscal       = $nuNotaFiscal;
            $xmlModelo->codigoVerificacao  = $codigoVerificacao;
            $xmlModelo->motivoCancelamento = $motivoCancelamento;

            /*
             * Ap�s popular os atributos do XML, gera a sa�da em string
             */
            return array('sucesso' => true, 'conteudoXML' => $xmlModelo->asXML());
        } catch (\Exception $ex) {
            return array('sucesso' => false, 'mensagemErro' => $ex->getMessage(), 'httpCode' => 400);
        }
    }
}