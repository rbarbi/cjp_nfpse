<?php // $Rev: 84 $ $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $


/**
 * Classe que encapsula as funcionalidades necessсrias
 * para que sejam realizadas chamadas a webservices
 * disponibilizados dentro da arquitetura Gama.
 *
 * Um detalhe importante, щ que os dados enviados ao
 * servidor de webservice serуo serializados e depois codificados
 * usando o padrуo BASE64. Lс deverуo ser decodificados e
 * deserializados, para entуo ser processados. Seu retorno deverс
 * tambщm ser convertido em um objeto serializado e encapsulado
 * em um formato BASE64, para depois voltar para o cliente.
 *
 * Esse padrуo foi necessсrio para permitir a passagem de objetos
 * e vetores como parтmetros, uma vez que seria mais complexa a
 * conversуo dos objetos para um padrуo XML e depois retornar aos
 * valores naturais.
 *
 * @author Eduardo Schmitt da Luz
 * @created: 2008-05-29
 * @copyright IASoft Desenvolvimento de Sistemas LTDA
 * @package gama3.main.webservice.teste
 */
class WSGamaClient {

	/**
	 * Lista de parтmetros passados para o servidor de
	 * webservice, padronizados pelo Gama.
	 *
	 * @var array
	 */
	var $parms = array();

	/**
	 * Nome do serviчo que serс invocado.
	 *
	 * @var string
	 */
	var $nome;

	/**
	 * Instтncia da classe nusoap_client, que possui todas as
	 * funcionalidades necessсrias para realizar o processo
	 * de envio da solicitaчуo de execuчуo de serviчo remoto.
	 *
	 * @var nusoap_client
	 */
	var $client = null;


	/**
	 * Construtor da classe
	 *
	 * @param string $url localizaчуo do script que atenderс o serviчo
	 * @param boolean $wsdl indica se estс sendo informada na URL o parтmetro WSDL
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
	 * @param string $url localizaчуo do script que atenderс o serviчo
	 * @param boolean $wsdl indica se estс sendo informada na URL o parтmetro WSDL
	 */
	function setClient($url,$wsdl) {
		$this->client = new nusoap_client($url,$wsdl);
	}

	/**
	 * Recupera a referъncia ao objeto nusoap_client
	 *
	 * @return nusoap_client
	 */
	function getClient() {
		return $this->client;
	}


	/**
	 * Define os valores dos atributos necessсrios para se efetivar
	 * uma comunicaчуo com um componente remoto do Gama.
	 *
	 * @param string $m Nome do mѓdulo
	 * @param string $u Nome do submѓdulo
	 * @param string $a Nome do script action
	 * @param string $acao Nome do mщtodo que serс invocado
	 */
	function setGamaParms($m=null,$u=null,$a=null,$acao=null) {
		$this->parms['m'] = $m;
		$this->parms['u'] = $u;
		$this->parms['a'] = $a;
		$this->parms['acao'] = $acao;
	}

	/**
	 * Define os valores dos atributos de autenticaчуo.
	 *
	 * @param string $user
	 * @param string $pass
	 */
	function setAuthParms($user,$pass) {
		$this->parms['cd_login_usuario'] = $user;
		$this->parms['cd_senha_usuario'] = $pass;
	}

	/**
	 * Recupera o conteњdo dos parтmetros de configuraчуo
	 * previamente definidas, alщm de permitir a inclusуo
	 * de um vetor de parтmetros adicionais.
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
	 * Define o nome do serviчo que serс invocado.
	 *
	 * @param string $nome
	 */
	function setNome($nome) {
		$this->nome = $nome;
	}

	/**
	 * Retorna o nome do serviчo invocado.
	 *
	 * @return string
	 */
	function getNome() {
		return $this->nome;
	}

	/**
	 * Realiza a chamada do serviчo remoto.
	 *
	 * @param array $parms Parтmetros que serуo enviados para o serviчo
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