<?php // $Rev: 84 $ $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $

/**
 * Classe derivada da MainGama, especializada em tratar de requisi��es
 * de servi�os disponibilizados na modalidade de webservices.
 *
 * @author Eduardo Schmitt da Luz
 * @created 2008-05-27
 * @copyright IASoft Desenvolvimento de Sistemas LTDA
 * @package gama3.main.webservice
 */
class MainGamaWS extends MainGama  {

	/**
	 * Lista de par�metros passados para o webservice.
	 * Foi necess�rio criar um esquema intermedi�rio, visto que o que vem
	 * da chamada � um XML que precisa ser decodificado. O objetivo desta
	 * vari�vel � manter o conte�do decodificado desse pacote XML.
	 *
	 * @var array
	 */
	var $wsParms;

	/**
	 * Construtor da classe.
	 *
	 * @return MainGamaWS
	 */
	function MainGamaWS () {
		parent::MainGama();
	}

	/**
	 * Sobrescrevi este m�todo para evitar que seja carregado
	 * qualquer arquivo adicional de configura��o. Se isso for necess�rio,
	 * dever� ser feito por aqui.
	 *
	 */
	function includesAdicionais() {
		// nada
	}

	/**
	 * Sobreescrevi este m�todo para evitar que seja executado algum
	 * procedimento al�m do j� previso no fluxo normal do MainGama.
	 *
	 */
	function overrides() {
		// nada
	}


	/**
	 * Sobreescrevi este m�todo para permitir que a autentica��o dos
	 * webservices possa ser feita de um jeito mais interessante, pois
	 * isso n�o exige um formul�rio.
	 *
	 * @todo Colocar a situa��o de falha na autentica��o;
	 *
	 * @param array $get_vars
	 * @param array $post_vars
	 */
	function doLogin($get_vars,$post_vars) {
		if (!$this->getSess()->get('conectado')) {
			if (strlen($GLOBALS['HTTP_RAW_POST_DATA']) > 0)	{
				$parser = new nusoap_parser($GLOBALS['HTTP_RAW_POST_DATA'],'ISO-8859-1');
				$this->wsParms = $parser->get_soapbody();
				$this->getSess()->set('cd_login_usuario',$this->wsParms['cd_login_usuario']);
				$this->getSess()->set('cd_senha_usuario',$this->wsParms['cd_senha_usuario']);
				// Efetivar a autenticacao

				$this->getSess()->set('tx_nome_usuario','ws');
				$this->getSess()->set('cd_status_usuario','A');
				$this->getSess()->set('conectado',true);
			}
		}
	}

	/**
	 * Sobreescrevi este m�todo para fazer com que o valor retornado para quem
	 * o invoca.
	 *
	 * @return int
	 */
	function getTipoResultado() {
		return GM_WEB_SERVICE;
	}



	/**
	 * Realiza o processamento da requisi��o. Primeiro ele decodifica os
	 * par�metros vindos no envelope XML, e depois executa.
	 *
	 * Retorna a resposta do servi�o executado.
	 *
	 * @param string $path
	 * @param array $GET
	 * @param array $POST
	 * @return mixed
	 */
	function processa($path,$GET,$POST) {
		$_result = null;
		$POST = Gama3Utils::deserializa($GET['parametros']);
		require_once($path);
		return Gama3Utils::serializa($_result);
	}


	/**
	 * Realiza a gera��o do nome do arquivo que ser�
	 * inclu�do/executado pelo sistema, com base nos
	 * par�metros passados.
	 *
	 * @param array $get
	 * @param array $post
	 * @return string
	 */
	function geraPath(&$get,$post=array()) {
		if (strlen($GLOBALS['HTTP_RAW_POST_DATA']) > 0)	{
			$get = $this->wsParms;
		}
		return parent::geraPath($get,$post);
	}


} // EOC


?>