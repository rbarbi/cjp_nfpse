<?php

/**
 * Classe que realizará as requisições no webservice da PMF.
 *
 * @author Carlos Domingues <carlos.domingues@grupoapi.com.br>
 */
class NfpseDAO {
	/**
	 * Conterá a URL do webservice da PMF
	 * @var string
	 */
	private $_urlWebservice;

	/**
	 * Define o ambiente que esta acessando: homologacao ou producao
	 * @var string
	 */
	private $_ambiente;

	/**
	 * Define o usuário (CMC) para autenticação
	 * @var string
	 */
	private $_userName;

	/**
	 * Define a senha para autenticação
	 * @var string
	 */
	private $_password;

	/**
	 * Define o client_id para autenticação
	 * @var string
	 */
	private $_clientId;

	/**
	 * Define o client_secret para autenticação
	 * @var string
	 */
	private $_clientSecret;

	/**
	 * Conterá o AEDF conforme ambiente que está acessando o webservice: homologação ou produção
	 * @var string
	 */
	private $_aedf;

	/**
	 * Conterá o idCNAE conforme definido por empresa no INI de configurações
	 * @var string
	 */
	private $_idCNAE;

	/**
	 * Define o token retornado na autenticação, que será usado em todas as requisições
	 * @var string
	 */
	private $_tokenRequest;

	/**
	 * #67663 - Define o token retornado na autenticação, que será usado SOMENTE para requisições de gerar o PDF da nota fiscal
	 * @var string
	 */
	private $_tokenRequestGerarPdf;

	/**
	 * Retorna a URL do webservice da PMF
	 * @return string
	 */
	public function getUrlWebservice() {
		return $this->_urlWebservice;
	}

	/**
	 * Retorna o ambiente que está acessando
	 * @return string
	 */
	public function getAmbiente() {
		return $this->_ambiente;
	}

	/**
	 * Retorna o usuário (CMC) para autenticação
	 * @return string
	 */
	public function getUserName() {
		return $this->_userName;
	}

	/**
	 * Retorna a senha para autenticação
	 * @return string
	 */
	public function getPassword() {
		return $this->_password;
	}

	/**
	 * Retorna o client_id para autenticação
	 * @return string
	 */
	public function getClientId() {
		return $this->_clientId;
	}

	/**
	 * Retorna o client_id para autenticação
	 * @return string
	 */
	public function getClientSecret() {
		return $this->_clientSecret;
	}

	/**
	 * Retorna o AEDF conforme ambiente que está acessando o webservice: homologação ou produção
	 * @return string
	 */
	public function getAEDF() {
		return $this->_aedf;
	}

	/**
	 * Retorna o idCNAE conforme definido por empresa no INI de configurações
	 * @return string
	 */
	public function getIdCNAE() {
		return $this->_idCNAE;
	}

	/**
	 * Retorna o token retornado na autenticação, que será usado em todas as requisições
	 * @return string
	 */
	public function getTokenRequest() {
		return $this->_tokenRequest;
	}

	/**
	 * Retorna o objeto de coneção para executar querys no banco quando necessário
	 * @return ADOConnection
	 */
	public function getCon() {
		return MainGama::getApp()->getCon('-');
	}

	/**
	 * Construtor da classe, que consumirá os métodos do webservice da PMF
	 * @param string $empresaAcesso
	 */
	public function __construct($empresaAcesso) {
		// Define a URL para acesso conforme ambiente que esta usando o microservico
		$dominio = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
		$ambienteAcessar = ((strpos($dominio, '192.168') !== false || strpos($dominio, 'localhost') !== false || strpos($dominio, 'dev.') !== false || strpos($dominio, 'sistemas.') !== false)) ? 'homologacao'
				: 'producao';

		// Tarefa #52555 - Alteração paliativa para caso o endereço de acesso ao microserviço for os IPs abaixo, definir como produção
		if (
			strpos($dominio, '192.168.20.158') !== false
		//|| strpos($dominio, '192.168.20.73') !== false
		) {
			$ambienteAcessar = 'producao';
		}

		$this->_urlWebservice = MainGama::getApp()->getConfig('url'.ucfirst($ambienteAcessar));

		// Define o ambiente
		$this->_ambiente = $ambienteAcessar;

		// Define os dados de autenticação OAuth conforme a empresa que está acessando
		$this->_userName = MainGama::getApp()->getConfig("username-{$empresaAcesso}");
		$this->_password = MainGama::getApp()->getConfig("password-{$empresaAcesso}");
		$this->_clientId = MainGama::getApp()->getConfig("client_id-{$empresaAcesso}");
		$this->_clientSecret = MainGama::getApp()->getConfig("client_secret-{$empresaAcesso}");

		// Define o AEDF - Se homolgação, SEIS primeiros dígitos do CMC, caso contrário o que está definido no INI de configs
		$this->_aedf = ($ambienteAcessar == 'homologacao') ? substr($this->_userName, 0, 6) : MainGama::getApp()->getConfig("aedf-{$empresaAcesso}");

		// Define o idCNAE - Que está definido no INI de configs
		$this->_idCNAE = MainGama::getApp()->getConfig("idCNAE-{$empresaAcesso}");

		// Por fim, realiza a autenticação no webservice para obter o token, que dura umas 12.5 horas
		$this->_tokenRequest = $this->_getTokenOAuth();

		// Realiza a autenticação no webservice para obter o token SOMENTE para geração do PDF
		$this->_tokenRequestGerarPdf = $this->_getTokenOAuth('P');
	}

	/**
	 * Método que realiza a autenticação no webservice da PMF e retorna o token para as demais requisições
	 * @param string $authPadrao	G - Autenticar para gerar nota, cancelar, etc | P = Autenticar para gerar PDF com credenciais fixas que a PMF usa em seu site
	 * @return array
	 */
	private function _getTokenOAuth($authPadrao = 'G') {
		try {
			// Dados que serão enviados por parâmetros para realizar a autenticação
			if ($authPadrao === 'G') {
				$dadosAutenticar = array(
					'grant_type' => 'password',
					'username' => $this->_userName,
					'password' => strtoupper(md5($this->_password)),
					'client_id' => $this->_clientId,
					'secret_id' => $this->_clientSecret
				);
			} else {
				// #67663 - Se for para gerar PDF, altera-se a autenticação para somente obter credenciais com chaves da própria PMF
				$dadosAutenticar = array(
					'grant_type' => 'client_credentials'
				);

				$this->_clientId = 'consulta-nfpse-client';
				$this->_clientSecret = '2ca53c015bef55767f7064d1c5159d45';
			}

			// Base64 do client_id:client_secret para o HEADER
			$headerAuth = base64_encode("{$this->_clientId}:{$this->_clientSecret}");

			$cURL = curl_init();
			curl_setopt($cURL, CURLOPT_URL, "{$this->_urlWebservice}autenticacao/oauth/token");
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cURL, CURLOPT_ENCODING, "");
			curl_setopt($cURL, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($dadosAutenticar));
			curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
				"Authorization: Basic {$headerAuth}",
				"Content-Type: application/x-www-form-urlencoded"
			));

			// Em produção: comentar estas duas linhas para habilitar a verificação de certificados no servidor
			//$dominio = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
			if ($this->_ambiente === 'homologacao'/* || strpos($dominio, '192.168.20.73') !== false */) {
				curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
			}

			$resultado = json_decode(curl_exec($cURL));
			$infoRequisicao = curl_getinfo($cURL);
			curl_close($cURL);

			if ($infoRequisicao['http_code'] != 200) {
				return array('sucesso' => false);
			}

			return array('sucesso' => true, 'conteudo' => $resultado->access_token);
		} catch (Exception $e) {
			return array('sucesso' => false);
		}
	}

	/**
	 * Gera a NFPS-e com base no XML devidamente assinado
	 * Se OK, retorna um base64 com o XML de retorno da nota gerada
	 * @param string $strXMLAssinada
	 * @return array
	 */
	public function gerarNota($strXMLAssinada) {
		try {
			$req = $this->_execRequisicaoCURL('GE', $strXMLAssinada);
			$infoRequisicao = $req['infoRequisicao'];
			$resultado = $req['resultado'];
			$msgErroCurl = $req['msgErroCurl'];

			/*
			 * Tarefa #49159 - Verificar se retorno é um XML válido de nota fiscal gerada, pois checar somente pelo HTTP_CODE 200 não 
			 * está sendo o suficiente. Ocorre que as vezes a nota fiscal é gerada e o retorno 200 esperado não vem, e sim outro http_code.
			 */
			$gerouNota = false;
			if (strpos($resultado, '<?xml') !== false) {
				$aux = simplexml_load_string(str_replace("\n", "", $resultado));

				if (isset($aux['numeroSerie'])) {
					$gerouNota = true;
				}
			}

			// Interpreta como erro caso a requisição não retorne 200 E também não exista a tag "numeroSerie" no XML
			if ($infoRequisicao['http_code'] != 200 && $gerouNota == false) {
				// Valida se o retorno é um XML. Se for, é sinal que houve algum erro nos dados do XML para gerar a nota
				if (strpos($resultado, '<?xml') !== false) {

					$strXml = str_replace("(<", "(", str_replace(">.<", " - ", str_replace("\n", "", $resultado)));
					$aux = (array) simplexml_load_string($strXml);

					$codigoErro = $infoRequisicao['http_code'];
					$mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : $aux['message'];
				}
				// Caso contrário, é um erro de execução do webservice, nesse caso o retorno é um JSON
				else {
					$aux = json_decode($resultado);
					$codigoErro = 500;
					$mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : (isset($aux->status) ? $aux->error : $aux->error.' - '.$aux->error_description);
				}

				return array('sucesso' => false, 'mensagemErro' => $mensagemErro, 'httpCode' => $codigoErro);
			}

			return array('sucesso' => true, 'conteudoXML' => base64_encode($resultado));
		} catch (Exception $e) {
			return array('sucesso' => false, 'mensagemErro' => $e->getMessage(), 'httpCode' => 400);
		}
	}

	/**
	 * Consulta uma nota fiscal e retornar o ID único da mesma
	 * @param integer $numeroSerie Contem o número de série da nota gerada
	 * @return array
	 */
	public function consultarIdNota($numeroSerie) {
		try {
			$req = $this->_execRequisicaoCURL('ID', null, $numeroSerie);
			$infoRequisicao = $req['infoRequisicao'];
			$resultado = $req['resultado'];
			$msgErroCurl = $req['msgErroCurl'];

			if ($infoRequisicao['http_code'] != 200) {
				$mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : 'Erro ao obter o ID da nota na PMF';

				throw new Exception($mensagemErro, $infoRequisicao['http_code']);
			}

			// Se requisição OK, decodifica o JSON para obter os dados necessários
			$resultadoAux = json_decode($resultado);

			return array('sucesso' => true, 'idNota' => $resultadoAux->notas[0]->id, 'cmcNota' => $resultadoAux->notas[0]->cmcPrestador);
		} catch (Exception $e) {
			return array('sucesso' => false, 'mensagemErro' => $e->getMessage(), 'httpCode' => $e->getCode());
		}
	}

	/**
	 * Gera e retorna um array com o base64 do PDF da nota fiscal
	 *
	 * @todo    IMPOTANTE: Estamos autorizados pela PMF para usar este recurso de gerar o PDF no webservice,
	 *          pois é um recurso que segundo eles não era para ser usado pelos emitentes de notas.
	 *          Assim que tiver tempo após liberar este microserviço, precisarei desenvolver do zero a
	 *          geração do PDF, de acordo com o layout atual, inclusive com o QR code com a URL de verificação da nota.
	 *
	 * @param integer $numeroSerie Contem o número de série da nota gerada
	 * @param integer $idNota Contem o ID da nota gerada
	 * @param integer $cmcNota Contem o CMC (insc. estadual) da nota gerada
	 *
	 * @return array
	 */
	public function gerarPDFNota($numeroSerie, $idNota, $cmcNota) {
		try {
			$req = $this->_execRequisicaoCURL('PD', null, $numeroSerie, ['idNota' => $idNota, 'cmcNota' => $cmcNota]);
			$infoRequisicao = $req['infoRequisicao'];
			$resultado = $req['resultado'];
			$msgErroCurl = $req['msgErroCurl'];

			if ($infoRequisicao['http_code'] != 200) {
				$mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : 'Erro ao gerar o PDF da nota na PMF';
				throw new Exception($mensagemErro, $infoRequisicao['http_code']);
			}

			return array('sucesso' => true, 'conteudoPDF' => $resultado);
		} catch (Exception $e) {
			return array('sucesso' => false, 'mensagemErro' => $e->getMessage(), 'httpCode' => $e->getCode());
		}
	}

	/**
	 * Calncela a NFPS-e com base no XML devidamente assinado
	 * Se OK, retorna um base64 com o XML de retorno da nota cancelada
	 * @param string $strXMLAssinada
	 * @return array
	 */
	public function cancelarNota($strXMLAssinada) {
		try {
			$req = $this->_execRequisicaoCURL('CA', $strXMLAssinada);
			$infoRequisicao = $req['infoRequisicao'];
			$resultado = $req['resultado'];
			$msgErroCurl = $req['msgErroCurl'];

			if ($infoRequisicao['http_code'] != 200) {
				// Valida se o retorno é um XML. Se for, é sinal que houve algum erro nos dados do XML para gerar a nota
				if (strpos($resultado, '<?xml') !== false) {
					$aux = (array) simplexml_load_string(str_replace("\n", "", $resultado));
					$codigoErro = $infoRequisicao['http_code'];
					$mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : $aux['message'];
				}
				// Caso contrário, é um erro de execução do webservice, nesse caso o retorno é um JSON
				else {
					$aux = json_decode($resultado);
					$codigoErro = 500;
					$mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : (isset($aux->status) ? $aux->error : $aux->error.' - '.$aux->error_description);
				}

				return array('sucesso' => false, 'mensagemErro' => $mensagemErro, 'httpCode' => $codigoErro);
			}

			return array('sucesso' => true, 'conteudoXML' => base64_encode($resultado));
		} catch (Exception $e) {
			return array('sucesso' => false, 'mensagemErro' => $e->getMessage(), 'httpCode' => 400);
		}
	}

	/**
	 * Retorna as notas geradas em um intervalo de datas passado
	 * @param string $dataInicial
	 * @param string $dataFinal
	 * @param integer $pagina
	 * @return array
	 */
	public function consultarNotasPorDatas($dataInicial, $dataFinal, $pagina = 1) {
		try {
			$req = $this->_execRequisicaoCURL('CD', null, null, ['dataInicial' => $dataInicial, 'dataFinal' => $dataFinal, 'pagina' => $pagina]);
			$infoRequisicao = $req['infoRequisicao'];
			$resultado = $req['resultado'];
			$msgErroCurl = $req['msgErroCurl'];

			if ($infoRequisicao['http_code'] != 200) {
				// Valida se o retorno é um XML. Se for, é sinal que houve algum erro nos dados do XML para gerar a nota
				if (strpos($resultado, '<?xml') !== false) {
					$aux = (array) simplexml_load_string(str_replace("\n", "", $resultado));
					$codigoErro = $infoRequisicao['http_code'];
					$mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : $aux['message'];
				}
				// Caso contrário, é um erro de execução do webservice, nesse caso o retorno é um JSON
				else {
					$aux = json_decode($resultado);
					pred($aux);
					$codigoErro = 500;
					$mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : (isset($aux->status) ? $aux->error : $aux->error.' - '.$aux->error_description);
				}

				return array('sucesso' => false, 'mensagemErro' => $mensagemErro, 'httpCode' => $codigoErro);
			}

			return array('sucesso' => true, 'resultado' => $resultado);
		} catch (Exception $e) {
			return array('sucesso' => false, 'mensagemErro' => $e->getMessage(), 'httpCode' => 400);
		}
	}

	/**
	 * Retorna o XML de uma nota fiscal
	 * @param integer $idNota
	 * @param integer $cmcNota
	 * @return array
	 */
	public function retornarXMLNota($idNota, $cmcNota) {
		try {
			$req = $this->_execRequisicaoCURL('XML', null, null, ['id' => $idNota, 'cmc' => $cmcNota]);
			$infoRequisicao = $req['infoRequisicao'];
			$resultado = $req['resultado'];
			$msgErroCurl = $req['msgErroCurl'];

			if ($infoRequisicao['http_code'] != 200) {
				$aux = json_decode($resultado);
				$mensagemErro = (!empty($msgErroCurl)) ? $msgErroCurl : (isset($aux->status) ? $aux->error : $aux->error.' - '.$aux->error_description);

				return array('sucesso' => false, 'mensagemErro' => $mensagemErro, 'httpCode' => 500);
			}

			return array('sucesso' => true, 'conteudoXML' => base64_encode($resultado));
		} catch (Exception $e) {
			return array('sucesso' => false, 'mensagemErro' => $e->getMessage(), 'httpCode' => 400);
		}
	}

	/**
	 * Faz a requisição no webservice da PMF conforme o tipo solicitado:
	 *    GE : Gerar uma NFPS-e
	 *    ID : Retornar o ID de uma NFPS-e, necessário para gerar o PDF
	 *    PD : Gerar o onteúdo binário do PDF, para salvar
	 *    CA : Cancelar uma NFPS-e
	 *
	 * @param string    $tipoRequisicao
	 * @param string    $strXMLAssinada
	 * @param integer   $numerSerieOuIdNota
	 * @param array		$arrParametrosAdicionais
	 * @return array
	 */
	private function _execRequisicaoCURL($tipoRequisicao, $strXMLAssinada = null, $numerSerieOuIdNota = null, $arrParametrosAdicionais = null) {
		$cURL = curl_init();
		$authToken = $this->_tokenRequest['conteudo'];

		switch ($tipoRequisicao) {
			case 'GE':
				$parteUrl = 'processamento/notas/processa';
				$verbo = 'POST';
				$arrHeaders = array("Authorization: Bearer {$authToken}", "Content-Type: application/xml");
				curl_setopt($cURL, CURLOPT_POSTFIELDS, "{$strXMLAssinada}");
				curl_setopt($cURL, CURLOPT_HTTPHEADER, $arrHeaders);
				break;
			case 'ID':
				$parteUrl = "consultas/notas/numero/{$numerSerieOuIdNota}";
				$verbo = 'GET';
				$arrHeaders = array("Authorization: Bearer {$authToken}");
				curl_setopt($cURL, CURLOPT_HTTPHEADER, $arrHeaders);
				break;
			case 'PD':
				$authTokenGerarPdf = $this->_tokenRequestGerarPdf['conteudo'];
				$parteUrl = "pdf/notas/{$arrParametrosAdicionais['idNota']}/{$arrParametrosAdicionais['cmcNota']}/nota_{$this->_aedf}-{$numerSerieOuIdNota}.pdf";
				$verbo = 'GET';
				$arrHeaders = array("Authorization: Bearer {$authTokenGerarPdf}");
				curl_setopt($cURL, CURLOPT_HTTPHEADER, $arrHeaders);
				break;
			case 'CA':
				$parteUrl = 'cancelamento/notas/cancela';
				$verbo = 'POST';
				$arrHeaders = array("Authorization: Bearer {$authToken}", "Content-type: application/xml");
				curl_setopt($cURL, CURLOPT_POSTFIELDS, "{$strXMLAssinada}");
				curl_setopt($cURL, CURLOPT_HTTPHEADER, $arrHeaders);
				break;
			case 'CD':
				$parteUrl = "consultas/notas/data/{$arrParametrosAdicionais['dataInicial']}/{$arrParametrosAdicionais['dataFinal']}/?pagina={$arrParametrosAdicionais['pagina']}";
				$verbo = 'GET';
				$arrHeaders = array("Authorization: Bearer {$authToken}", "Content-type: application/xml");
				curl_setopt($cURL, CURLOPT_HTTPHEADER, $arrHeaders);
				break;
			case 'XML':
				$parteUrl = "consultas/notas/xml/{$arrParametrosAdicionais['id']}/{$arrParametrosAdicionais['cmc']}";
				$verbo = 'GET';
				$arrHeaders = array("Authorization: Bearer {$authToken}");
				curl_setopt($cURL, CURLOPT_HTTPHEADER, $arrHeaders);
				break;
		}

		$urlCompleta = "{$this->_urlWebservice}{$parteUrl}";

		curl_setopt_array($cURL, array(
			CURLOPT_URL => $urlCompleta,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $verbo
		));

		// Em DEV: não habilitar a verificação de certificados no servidor
		//$dominio = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
		if ($this->_ambiente === 'homologacao'/* || strpos($dominio, '192.168.20.73') !== false */) {
			curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
		}

		$resultado = curl_exec($cURL);
		$infoRequisicao = curl_getinfo($cURL);
		$msgErroCurl = curl_error($cURL);
		curl_close($cURL);

		// #67663 - Se for para gerar PDF da nota, converter o resultado em base64 antes de retornar, pois este retorno é o binário do PDF da nota
		if ($tipoRequisicao === 'PD') {
			$resultado = base64_encode($resultado);
		}

		$arrInserir = [
			'lrn_recurso' => $urlCompleta,
			'lrn_verbo' => $verbo,
			'lrn_headers' => json_encode($arrHeaders),
			'lrn_request' => $strXMLAssinada,
			'lrn_response' => $resultado,
			'lrn_info_response' => json_encode($infoRequisicao),
		];

		$this->getCon()->AutoExecute('log.log_requisicao_nfpse', $arrInserir);

		//pre($resultado, 'resultado na dao do microservico', false);
		//pre($infoRequisicao, 'info da requisicao na dao do microservico', false);
		//pre($msgErroCurl, 'msg de erro na dao do microservico', true);

		return array(
			'resultado' => $resultado,
			'infoRequisicao' => $infoRequisicao,
			'msgErroCurl' => $msgErroCurl
		);
	}

	/**
	 * Retorna os boletos que devem ter seu valor atualizado com base nas possíveis retenções
	 * @param string $dataInicial
	 * @return array
	 */
	public function getBoletosAtualizar($dataInicial = null) {
		$filtroData = !empty($dataInicial) ? "'{$dataInicial}'" : "CURRENT_DATE";

		$sql = "
            SELECT 	b.bol_id
                    ,b.bol_cc_id
                    ,b.bol_valor_documento
                    ,ccn.ccn_regime_tributario
                    ,ccn.ccn_possui_convenio_uniao
                    ,ccn.ccn_retem_iss
                    ,ccn.ccn_percentual_iss
            FROM 	public.boleto b
            JOIN	public.contrato_cobranca cc ON cc.cc_id = b.bol_cc_id
            JOIN	public.contrato_cobranca_nfe ccn ON ccn.ccn_cc_id = cc.cc_id
            JOIN	public.conta_corrente ccor on ccor.ccor_id = b.bol_ccor_id
            WHERE 	b.bol_id_externo IS NULL                    -- Faturas não registradas ainda
            AND     b.bol_valor_liquido IS NULL                 -- Que não tenham este valor já atualizado
            AND     b.bol_valor_documento >= 215.05             -- Faturas que tenham valor mínimo para gerar retenções
            AND 	b.bol_data_emissao >= {$filtroData}         -- Faturas emitidas na data atual, ou uma data passada por parâmetro
            AND 	ccor.ccor_ced_id IN(1,4)                    -- Cedentes Infodigi(1) e POL/bradesco(4)
            AND     (                                           -- Faturas de clientes que tem o Regime Tributário OU retenção de ISS definidA
                        ccn.ccn_regime_tributario IS NOT NULL
                        OR ccn.ccn_retem_iss IS NOT NULL
                    )
        ";
		$dados = $this->getCon()->GetArray($sql);

		// Percorre os dados para buscar as retenções de cada fatura
		foreach ($dados as $ind => $bol) {
			$retencoes = $this->retornaDadosImpostosRetidos(array(
				'valor_unitario' => $bol['bol_valor_documento'],
				'ccn_regime_tributario' => $bol['ccn_regime_tributario'],
				'ccn_possui_convenio_uniao' => $bol['ccn_possui_convenio_uniao'],
				'ccn_retem_iss' => $bol['ccn_retem_iss'],
				'ccn_percentual_iss' => $bol['ccn_percentual_iss']
			));

			$retIRRF = round($retencoes['valorRetIRRF'], 2);
			$retISS = round($retencoes['valorRetISS'], 2);
			$retOutros = round($retencoes['valorRetOutros'], 2);
			$retTotal = $retIRRF + $retISS + $retOutros;

			if ($retTotal > 0) {
				// Se encontrou alguma retenção, adiciona as novas colunas para o Service tratar e atualizar a fatura
				$dados[$ind]['valor_retencao_irff'] = $retencoes['valorRetIRRF'];
				$dados[$ind]['valor_retencao_iss'] = $retencoes['valorRetISS'];
				$dados[$ind]['valor_retencao_outros'] = $retencoes['valorRetOutros'];
				$dados[$ind]['valor_retencao_total'] = $retTotal;
				$dados[$ind]['valor_bol_atualizado'] = $bol['bol_valor_documento'] - $retTotal;
			} else {
				// Caso contrário remove este item do array
				unset($dados[$ind]);
			}
		}

		return $dados;
	}

	/**
	 * Valida todas as regras referente às retenções de impostos, com base no que foi definido nos dados de nota fiscal do cliente
	 *
	 * @param array $dadosFatura
	 * @return array
	 */
	public function retornaDadosImpostosRetidos($dadosFatura) {
		//pred2($dadosFatura);
		$valorFatura = $dadosFatura['valor_unitario'];

		/*
		 * Novas regras com base no valor do boleto
		 *  - Boletos até R$ 215,04 - Não tem retenções
		 *  - Boletos de R$ 215,05 até R$ 666,66 - Reter apenas PIS/COFINS/CSLL
		 *  - Boletos acima de R$ 666,66 - Tem todas as retenções
		 */
		if ($valorFatura <= 215.04) {
			$temRetencao = false;
			$retencaoCompleta = false;
		} elseif ($valorFatura >= 215.05 && $valorFatura <= 666.66) {
			$temRetencao = true;
			$retencaoCompleta = false;
		} elseif ($valorFatura >= 666.67) {
			$temRetencao = true;
			$retencaoCompleta = true;
		}

		// Se tiver alguma retenção, aplicar as regras existentes
		if ($temRetencao === true) {
			$percOutros = 4.65;
			$percIRRF = 1.5;
			$observacoes = '';
			$valorRetIRRF = $valorFatura * ($percIRRF / 100);
			$valorRetOutros = $valorFatura * ($percOutros / 100);
			$valorRetIRRFf = number_format($valorRetIRRF, 2, ",", ".");
			$valorRetOutrosf = number_format($valorRetOutros, 2, ",", ".");
			$valorRetISS = 0;

			if ($dadosFatura['ccn_regime_tributario'] === 'SIN') {
				// Se for PJ simples nacional
				if ($retencaoCompleta === true) {
					$valorRetOutros = 0;
					$observacoes = " Retenção do IRRF 1,5% R$ {$valorRetIRRFf}.";
				} else {
					$valorRetIRRF = 0;
				}
				$valorRetOutros = 0;
			} elseif ($dadosFatura['ccn_regime_tributario'] === 'LPR') {
				// Se for PJ lucro presumido
				if ($retencaoCompleta === true) {
					$observacoes = " Retenção do PIS/COFINS/CSLL 4,65% R$ {$valorRetOutrosf}; IRRF 1,5% R$ {$valorRetIRRFf}.";
				} else {
					$valorRetIRRF = 0;
					$observacoes = " Retenção do PIS/COFINS/CSLL 4,65% R$ {$valorRetOutrosf}.";
				}
			} elseif ($dadosFatura['ccn_regime_tributario'] === 'OPME') {
				// Se for órgão público estadual ou municipal - adicionar frase das retenções somente se tiver convênio com a União
				if ($dadosFatura['ccn_possui_convenio_uniao'] === 'S') {
					if ($retencaoCompleta === true) {
						$observacoes = " Retenção do PIS/COFINS/CSLL 4,65% R$ {$valorRetOutrosf}; IRRF 1,5% R$ {$valorRetIRRFf}.";
					} else {
						$valorRetIRRF = 0;
						$observacoes = " Retenção do PIS/COFINS/CSLL 4,65% R$ {$valorRetOutrosf}.";
					}
				} else {
					$valorRetOutros = $valorRetIRRF = 0;
				}
			} elseif ($dadosFatura['ccn_regime_tributario'] === 'OPF') {
				// Se for órgão público federal
				$percOutros = 9.45;
				$valorRetIRRF = 0;
				$valorRetOutros = $valorFatura * ($percOutros / 100);
				$valorRetOutrosf = number_format($valorRetOutros, 2, ",", ".");
				$observacoes = " Retenção do PIS/COFINS/CSLL/IRPJ 9,45% R$ {$valorRetOutrosf}.";
			} elseif ($dadosFatura['ccn_regime_tributario'] == 'CON') {
				// Se for condomínio
				$valorRetIRRF = 0;
				$observacoes = " Retenção do PIS/COFINS/CSLL 4,65% R$ {$valorRetOutrosf}.";
			} else {
				// Nenhuma retenção - se o campo ccn_regime_tributario estiver = "N" ou vazio
				$valorRetOutros = $valorRetIRRF = 0;
			}

			// Retenção de ISS - ainda falta definir
			if ($dadosFatura['ccn_retem_iss'] === 'S') {
				// Se retém ISS - Calcular com base no que foi digitado. Se nada for definido nesse campo, o default será DOIS
				$percISS = (!empty($dadosFatura['ccn_percentual_iss'])) ? $dadosFatura['ccn_percentual_iss'] : 2;
				$percISSf = number_format($percISS, 1, ',', '.');
				$valorRetISS = $valorFatura * ($percISS / 100);
				$valorRetISSf = number_format($valorRetISS, 2, ",", ".");

				// A observação do ISS deve ser concatenada com a observação dos demais impostos retidos
				$observacoes .= " Retenção do ISS {$percISSf}% R$ {$valorRetISSf}.";
			}
		} else {
			// Nenhuma retenção - se o valor da fatura for inferior a 215,04
			$valorRetOutros = $valorRetIRRF = 0;
		}
		//exit("$observacoes - $valorRetIRRF - $valorRetOutros - $valorRetISS");

		return array(
			'observacoes' => utf8_encode($observacoes), // Onde for implementado, deve-se usar o utf8_decode
			'valorRetIRRF' => $valorRetIRRF,
			'valorRetOutros' => $valorRetOutros,
			'valorRetISS' => $valorRetISS
		);
	}
}