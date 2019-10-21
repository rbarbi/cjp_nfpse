<?php // $Rev: 84 $ $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $


/**
 * Classe que encapsula as funcionalidades necess�rias
 * para que sejam realizadas chamadas a webservices
 * disponibilizados dentro da arquitetura Gama.
 *
 * Um detalhe importante, � que os dados enviados ao
 * servidor de webservice ser�o serializados e depois codificados
 * usando o padr�o BASE64. L� dever�o ser decodificados e
 * deserializados, para ent�o ser processados. Seu retorno dever�
 * tamb�m ser convertido em um objeto serializado e encapsulado
 * em um formato BASE64, para depois voltar para o cliente.
 *
 * Esse padr�o foi necess�rio para permitir a passagem de objetos
 * e vetores como par�metros, uma vez que seria mais complexa a
 * convers�o dos objetos para um padr�o XML e depois retornar aos
 * valores naturais.
 *
 * @author Eduardo Schmitt da Luz
 * @created: 2008-05-29
 * @copyright IASoft Desenvolvimento de Sistemas LTDA
 * @package gama3.main.webservice.teste
 */
class WSGamaClient {

	/**
	 * Lista de par�metros passados para o servidor de
	 * webservice, padronizados pelo Gama.
	 *
	 * @var array
	 */
	var $parms = array();

	/**
	 * Nome do servi�o que ser� invocado.
	 *
	 * @var string
	 */
	var $nome;

	/**
	 * Inst�ncia da classe nusoap_client, que possui todas as
	 * funcionalidades necess�rias para realizar o processo
	 * de envio da solicita��o de execu��o de servi�o remoto.
	 *
	 * @var nusoap_client
	 */
	var $client = null;


	/**
	 * Construtor da classe
	 *
	 * @param string $url localiza��o do script que atender� o servi�o
	 * @param boolean $wsdl indica se est� sendo informada na URL o par�metro WSDL
	 * @return WSGamaClient
	 */
	function WSGamaClient($url="http://localhost/dev/infodigi/ws.php?wsdl",$wsdl=true) {
		$this->setClient($url,$wsdl);
		$err = $this->client->getError();
		if ($err){
			throw new SysException($err,81);
		}

		$this->setGamaParms();
	}

	/**
	 * Define o valor do objeto 'client'
	 *
	 * @param string $url localiza��o do script que atender� o servi�o
	 * @param boolean $wsdl indica se est� sendo informada na URL o par�metro WSDL
	 */
	function setClient($url,$wsdl) {
		$this->client = new nusoap_client($url,$wsdl);
	}

	/**
	 * Recupera a refer�ncia ao objeto nusoap_client
	 *
	 * @return nusoap_client
	 */
	function getClient() {
		return $this->client;
	}


	/**
	 * Define os valores dos atributos necess�rios para se efetivar
	 * uma comunica��o com um componente remoto do Gama.
	 *
	 * @param string $m Nome do m�dulo
	 * @param string $u Nome do subm�dulo
	 * @param string $a Nome do script action
	 * @param string $acao Nome do m�todo que ser� invocado
	 */
	function setGamaParms($m=null,$u=null,$a=null,$acao=null) {
		$this->parms['m'] = $m;
		$this->parms['u'] = $u;
		$this->parms['a'] = $a;
		$this->parms['acao'] = $acao;
	}

	/**
	 * Define os valores dos atributos de autentica��o.
	 *
	 * @param string $user
	 * @param string $pass
	 */
	function setAuthParms($user,$pass) {
		$this->parms['cd_login_usuario'] = $user;
		$this->parms['cd_senha_usuario'] = $pass;
	}

	/**
	 * Recupera o conte�do dos par�metros de configura��o
	 * previamente definidas, al�m de permitir a inclus�o
	 * de um vetor de par�metros adicionais.
	 *
	 * @param array $append
	 * @return array
	 */
	function getGamaParms($append=array()) {
/*		echo '(((';
		print_r($this->parms);
		print_r($append);
		echo ')))';*/
		return array_merge($this->parms,$append);
	}

	/**
	 * Define o nome do servi�o que ser� invocado.
	 *
	 * @param string $nome
	 */
	function setNome($nome) {
		$this->nome = $nome;
	}

	/**
	 * Retorna o nome do servi�o invocado.
	 *
	 * @return string
	 */
	function getNome() {
		return $this->nome;
	}

	/**
	 * Realiza a chamada do servi�o remoto.
	 *
	 * @param array $parms Par�metros que ser�o enviados para o servi�o
	 * @return mixed
	 */
	function executa($parms) {
		$parametros = Gama3Utils::serializa($parms);
		$resultado = $this->client->call ($this->getNome(),$this->getGamaParms(array('parametros'=>$parametros)));


		if ($this->client->fault){
			throw new SysException($result,82);
		}else{
			$err = $this->client->getError();
			if ($err){
				print_r($this->client);
				throw new SysException($err,83);
			}//end_if
		}//end_else
		return Gama3Utils::deserializa($resultado);
	}

}


?>