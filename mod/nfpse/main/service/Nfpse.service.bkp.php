<?php

//require_once 'NfpseBase.service.php';

/*
 * Namespaces das classes para assinatura digital do XML para requisição da NFPS-e
 */
use NFePHP\Common\Signer;
use NFePHP\Common\Certificate;

/**
 * Classe que conterá métodos específicos utilizados pelos métodos do módulo
 * @author Carlos Domingues <carlos.domingues@grupoapi.net.br>
 */
class NfpseService extends NfpseBaseService {

	/**
	 * Construtor da classe
	 * @param array $parms
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Chama a geração de uma nota fiscal na DAO, consumindo o webservice da PMF
	 * Retorna um base64 com o XML completo de retorno da nota gerada
	 * 
	 * @return array
	 * @throws Exception
	 */
	public function gerarNota() {
		try {
			$dadosPost = $this->getDadosPost();

			// Define o AEDF conforme ambiente que está acessando. Mais informações ver item "numeroAEDF" na geração do XML da requisição
			$aedf = $this->dao->getAEDF();

			// Define o idCNAE - conforme definido por empresa no INI de configs
			$idCNAE = $this->dao->getIdCNAE();

			/*
			 * ANTES de enviar os dados para a geração do XML, faz a consulta das retenções caso for necessário
			 */
			//$strDadosAdicionais = $dadosPost['dadosNota']['dadosAdicionais'];
			$strDadosAdicionais = "";

			// 1 - Para INFODIGI: todas as retenções do regime tributário MAIS retenção do ISS
			if (strpos($strDadosAdicionais, '[CONSULTAR_TODAS_RETENCOES]') !== false) {
				$dadosRetencoes = $this->getDaoObject()->retornaDadosImpostosRetidos(array(
					'valor_unitario' => $dadosPost['dadosNota']['valor_unitario'],
					'ccn_regime_tributario' => $dadosPost['dadosNota']['ccn_regime_tributario'],
					'ccn_possui_convenio_uniao' => $dadosPost['dadosNota']['ccn_possui_convenio_uniao'],
					'ccn_retem_iss' => $dadosPost['dadosNota']['ccn_retem_iss'],
					'ccn_percentual_iss' => $dadosPost['dadosNota']['ccn_percentual_iss']
				));

				$dadosPost['dadosNota']['dadosAdicionais'] = str_replace('[CONSULTAR_TODAS_RETENCOES]', $dadosRetencoes['observacoes'], $strDadosAdicionais);
			}

			// 1 - Para POL: Somente a retenção do ISS
			if (strpos($strDadosAdicionais, '[CONSULTAR_SOMENTE_ISS]') !== false) {
				$dadosRetencoes = $this->getDaoObject()->retornaDadosImpostosRetidos(array(
					'valor_unitario' => $dadosPost['dadosNota']['valor_unitario'],
					'ccn_regime_tributario' => 'N',
					'ccn_possui_convenio_uniao' => 'N',
					'ccn_retem_iss' => $dadosPost['dadosNota']['ccn_retem_iss'],
					'ccn_percentual_iss' => $dadosPost['dadosNota']['ccn_percentual_iss']
				));

				$dadosPost['dadosNota']['dadosAdicionais'] = str_replace('[CONSULTAR_SOMENTE_ISS]', $dadosRetencoes['observacoes'], $strDadosAdicionais);
			}

			// Gera o XML com os dados que foram postados na requisição
			$dadosStrXML = UtilsNFPSe::gerarXMLrequisicao($dadosPost['dadosNota'], $aedf, $idCNAE);
			if ($dadosStrXML['sucesso'] == false) {
				throw new Exception($dadosStrXML['mensagemErro'], $dadosStrXML['httpCode']);
			}

			// Abre o certificado da empresa específica
			$nomeCertificado = MainGama::getApp()->getConfig("nome_certificado-{$dadosPost['emp']}");
			$senhaCertificado = MainGama::getApp()->getConfig("senha_certificado-{$dadosPost['emp']}");
			$pathCertificado = dirname(dirname(__DIR__)).'/certificado-digital/'.$nomeCertificado;
			$strCertificado = file_get_contents($pathCertificado);
			$objCertificado = Certificate::readPfx($strCertificado, $senhaCertificado);

			// Chama a assinatura digital do XML com o certificado da empresa que está gerando a nota fiscal
			$strXMLAssinada = Signer::sign($objCertificado, $dadosStrXML['conteudoXML'], 'xmlProcessamentoNfpse', '', OPENSSL_ALGO_SHA1, [true, false, null, null], '');

			// Por fim chama o método que gera a nota, validando se gerou corretamente caso contrário dispara exceção
			$dadosNotaGerada = $this->dao->gerarNota($strXMLAssinada);
			if ($dadosNotaGerada['sucesso'] == false) {
				throw new Exception($dadosNotaGerada['mensagemErro'], $dadosNotaGerada['httpCode']);
			}

			return $dadosNotaGerada;
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Reliza a consulta de uma nota fiscal na DAO, consumindo o webservice da PMF e retorna um array com o ID único da nota
	 * 
	 * @param integer $numeroSerie Contem o número de série da nota gerada
	 * @return integer
	 * @throws Exception
	 */
	public function consultarIdNota($numeroSerie) {
		try {
			$dadosNota = $this->dao->consultarIdNota($numeroSerie);
			if ($dadosNota['sucesso'] == false) {
				throw new Exception($dadosNota['mensagemErro'], $dadosNota['httpCode']);
			}

			return $dadosNota;
		} catch (Exception $e) {
			/*
			 * Se alguma coisa der errada na geração da nota, retorna o código 400 (Bad request)
			 * Os objetos Certificate e Signer disparam exceções, que por sua vez extendem da Exception nativa do PHP
			 */
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Chama a geração do PDF de uma nota fiscal na DAO, consumindo o webservice da PMF
	 * Retorna um array com o base64 com o binario do PDF para o sistema da empresa que acessar salvar em arquivo
	 *
	 * @param integer $numeroSerie Contem o número de série da nota gerada
	 * @param integer $idNota Contem o ID da nota gerada
	 * @param integer $cmcNota Contem o CMC (insc. estadual) da nota gerada
	 * @return array
	 * @throws Exception
	 */
	public function gerarPDFNota($numeroSerie, $idNota, $cmcNota) {
		try {
			$dadosPdfGerado = $this->dao->gerarPDFNota($numeroSerie, $idNota, $cmcNota);
			if ($dadosPdfGerado['sucesso'] == false) {
				throw new Exception($dadosPdfGerado['mensagemErro'], $dadosPdfGerado['httpCode']);
			}

			return $dadosPdfGerado;
		} catch (Exception $e) {
			/*
			 * Se alguma coisa der errada na geração da nota, retorna o código 400 (Bad request)
			 * Os objetos Certificate e Signer disparam exceções, que por sua vez extendem da Exception nativa do PHP
			 */
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Chama o método de cancelamento de uma NFPS-e, consumindo o webservice da PMF
	 * @return array
	 * @throws Exception
	 */
	public function cancelarNota() {
		try {
			$dadosPost = $this->getDadosPost()['dadosNota'];

			// Define o AEDF conforme ambiente que está acessando. Mais informações ver item "numeroAEDF" na geração do XML da requisição
			$aedf = $this->dao->getAEDF();

			// Gera o XML com os dados que foram postados na requisição
			$dadosStrXML = UtilsNFPSe::gerarXMLcancelamento($aedf, $dadosPost['numeroSerie'], $dadosPost['codigoVerificacao'], $dadosPost['motivoCancelamento']);
			if ($dadosStrXML['sucesso'] == false) {
				throw new Exception($dadosStrXML['mensagemErro'], $dadosStrXML['httpCode']);
			}

			// Abre o certificado da empresa específica
			$nomeCertificado = MainGama::getApp()->getConfig("nome_certificado-{$dadosPost['emp']}");
			$senhaCertificado = MainGama::getApp()->getConfig("senha_certificado-{$dadosPost['emp']}");
			$pathCertificado = dirname(dirname(__DIR__)).'/certificado-digital/'.$nomeCertificado;
			$strCertificado = file_get_contents($pathCertificado);
			$objCertificado = Certificate::readPfx($strCertificado, $senhaCertificado);

			// Chama a assinatura digital do XML com o certificado da empresa que está gerando a nota fiscal
			$strXMLAssinada = Signer::sign($objCertificado, $dadosStrXML['conteudoXML'], 'xmlCancelamentoNfpse', '', OPENSSL_ALGO_SHA1, [true, false, null, null], '');

			// Por fim chama o método que gera a nota, validando se gerou corretamente caso contrário dispara exceção
			$dadosNotaCancelada = $this->dao->cancelarNota($strXMLAssinada);
			if ($dadosNotaCancelada['sucesso'] == false) {
				throw new Exception($dadosNotaCancelada['mensagemErro'], $dadosNotaCancelada['httpCode']);
			}

			return $dadosNotaCancelada;
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Chama o método de de consulta de notas por datas consumindo o webservice da PMF e retorna um array de números de série dessas notas
	 * @return array
	 * @throws Exception
	 */
	public function consultarNotasPorDatas() {
		try {
			$dadosPost = $this->getDadosPost()['dadosNota'];
			$pagina = (isset($dadosPost['pagina'])) ? $dadosPost['pagina'] : 1;

			// Na primeira consulta passa o parâmetro da página default 1
			$dados = $this->dao->consultarNotasPorDatas($dadosPost['dataInicial'], $dadosPost['dataFinal'], $pagina);
			if ($dados['sucesso'] === false) {
				throw new Exception($dados['mensagemErro'], $dados['httpCode']);
			}

			$arrNotas = json_decode($dados['resultado'], true);
			$arrRetorno = [];

			// Se a consulta retornar mais de 1 página, deve-se fazer novas requisições de consulta para cada página e ir populando o array de números de série
			if ($arrNotas['totalPaginas'] > 1) {
				for ($pagina = 1; $pagina <= $arrNotas['totalPaginas']; $pagina++) {
					$dados = $this->dao->consultarNotasPorDatas($dadosPost['dataInicial'], $dadosPost['dataFinal'], $pagina);
					if ($dados['sucesso'] === false) {
						throw new Exception($dados['mensagemErro'], $dados['httpCode']);
					}

					$arrNotas = json_decode($dados['resultado'], true);
					foreach ($arrNotas['notas'] as $nota) {
						$arrRetorno[] = $nota['numero'];
					}
				}
			} else {
				// Caso contrário somente popula o array de números de série com as notas retornadas da única página
				foreach ($arrNotas['notas'] as $nota) {
					$arrRetorno[] = $nota['numero'];
				}
			}
			sort($arrRetorno);

			return $arrRetorno;
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
	}


	/**
	 * Chama a geração do XML de uma nota fiscal na DAO, consumindo o webservice da PMF
	 * Retorna um array com o base64 com o binario do XML para o sistema da empresa que acessar salvar em arquivo
	 *
	 * @param integer $idNota
	 * @param integer $cmcNota
	 * @return array
	 * @throws Exception
	 */
	public function retornarXMLNota($idNota, $cmcNota) {
		try {
			$dadosPdfGerado = $this->dao->retornarXMLNota($idNota, $cmcNota);
			if ($dadosPdfGerado['sucesso'] == false) {
				throw new Exception($dadosPdfGerado['mensagemErro']);
			}

			return $dadosPdfGerado;
		} catch (Exception $e) {
			/*
			 * Se alguma coisa der errada na geração da nota, retorna o código 400 (Bad request)
			 * Os objetos Certificate e Signer disparam exceções, que por sua vez extendem da Exception nativa do PHP
			 */
			throw new Exception($e->getMessage(), 400);
		}
	}

	/**
	 * Retorna os dados de retenções para uma determinada fatura
	 *
	 * @param array $dadosFatura
	 * @return array
	 * @throws Exception
	 */
	public function consultarRetencoesFatura($dadosFatura) {
		try {

			return $this->dao->retornaDadosImpostosRetidos($dadosFatura);
		} catch (Exception $e) {
			/*
			 * Se alguma coisa der errada na geração da nota, retorna o código 400 (Bad request)
			 */
			throw new Exception($e->getMessage(), 400);
		}
	}

	/**
	 * Retorna os boletos que devem ter seu valor atualizado com base nas possíveis retenções,
	 * e aplica o desconto valor das retenções sobre o valor da fatura
	 *
	 * @param string $dataInicial
	 * @return array
	 */
	public function atualizaValorRetencaoFaturas($dataInicial = null) {
		echo $txt = date('d/m/Y H:i')." - Buscando faturas de clientes que recebem nota fiscal para atualizar o valor com base nas retencoes de impostos:\n";
		$strLog = $txt;

		//$arrBoletos = $this->dao->getBoletosAtualizar($dataInicial);
		$arrBoletos = [];
		//pred($arrBoletos);

		if (count($arrBoletos) > 0) {
			foreach ($arrBoletos as $bol) {
				pred($bol);
				$retIRRF = $bol['valor_retencao_irrf'];
				$retISS = $bol['valor_retencao_iss'];
				$retOutros = $bol['valor_retencao_outros'];
				$retTotal = $bol['valor_retencao_total'];

				// Aplica o desconto na fatura somene se o valor de uma das retenções form maior que ZERO
				if ($retTotal > 0) {
					$novoValorBoleto = $bol['valor_bol_atualizado'];

					$dadosAtualizar = array(
						'bol_valor_documento' => $novoValorBoleto,
						'bol_valor_liquido' => $bol['bol_valor_documento']
					);
					$atualizou = $this->dao->getCon()->AutoExecute('public.boleto', $dadosAtualizar, 'UPDATE', "bol_id={$bol['bol_id']}");
					$txtAtualizou = ($atualizou !== false) ? 'OK' : 'ERRO';

					// Nova implementação: inserir log para o boleto informando da alteração de valor
					$valorOriginal = number_format($bol['bol_valor_documento'], 2, ',', '.');
					$valorAtualizado = number_format($novoValorBoleto, 2, ',', '.');
					$this->dao->getCon()->AutoExecute('public.log_gerar_boleto', [
						'lgb_usu_id' => 2,
						'lgb_id_fatura' => $bol['bol_id'],
						'lgb_sucesso' => 't',
						'lgb_acao' => 'A',
						'lgb_Resposta' => "Alterou o valor do documento de R$ 15,00 para R$ 12,55 (retenção de impostos)",

					]);

					echo $txt = "".date('d/m/Y H:i')." - Atualizando valor da fatura {$bol['bol_id']} R$ {$valorOriginal}, desontando IRRF R$".number_format($retIRRF, 2, ',', '.').", ISS R$".number_format($retISS, 2, ',', '.')." e Outros R$".number_format($retOutros, 2, ',', '.')." - TOTAL R$ {$valorAtualizado} - {$txtAtualizou}\n";

					$strLog .= $txt;
				}
			}
		} else {
			echo $txt = date('d/m/Y H:i')." - Nenhuma fatura para ser atualizada no momento da execucao do script.\n";
			$strLog .= $txt;
		}

		echo $txt = date('d/m/Y H:i')." - Execucao do script finalizada.\n\n";
		$strLog .= $txt;

		// Grava em LOG todo o andamento do processo de geração das NF-e
		file_put_contents('log/Atualiza_valor_retencao_faturas.txt', $strLog, FILE_APPEND);
	}
}