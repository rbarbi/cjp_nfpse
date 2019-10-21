<?php // $Rev: 348 $ - $Author: eduluz $ $Date: 2009-03-31 11:57:26 -0300 (ter, 31 mar 2009) $

/**
 * Classe usada para transitar mensagens da camada de interface para as
 * origens das requisições Ajax, no formato JSON.
 *
 * Valores válidos de Tipo:
 * 		url - uma URL é enviada para que o browser seja redirecionado
 * 		comando - um comando javascript, como o nome de um método
 * 		alerta - uma mensagem que deverá ser exibida para o usuário.
 *
 *
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.interface
 */
class MsgJSON {

	 /**
	  * @var mixed tipo
	  */
	protected $tipo;

	 /**
	  * @var mixed url
	  */
	protected $url;


	protected $parmsAdicionais;

//--------------------------------------------

	/**
	 * Retorna o valor de tipo
	 * @return mixed
	 */
	public function getTipo () {
		return $this->tipo;
	} // eof getTipo

	/**
	 * Retorna o valor de url
	 * @return mixed
	 */
	public function getURL () {
		return $this->url;
	} // eof getUrl


	public function getParmAdicional($nome=null) {
		if (is_null($nome)) {
			return $this->parmsAdicionais;
		} else if (isset($this->parmsAdicionais[$nome])) {
			return $this->parmsAdicionais[$nome];
		} else {
			return null;
		}
	}

//--------------------------------------------

	/**
	 * Define o valor de tipo
	 * Pode ser: url, comando ou alerta
	 *
	 * @param mixed $tipo
	 */
	public function setTipo ($tipo) {
		$this->tipo = $tipo;
	} // eof setTipo

	/**
	 * Define o valor de url
	 * @param mixed $url
	 */
	public function setURL ($url) {
		$this->url = $url;
	} // eof setUrl


	public function setParmAdicional($nome,$valor) {
		$this->parmsAdicionais[$nome] = $valor;
	}


	public function asJSON() {
		$s = "";

		$lista = $this->getParmAdicional();
		$lista2 = array();
		if (count($lista) > 0) {
			foreach ($lista as $nomeVar => $item) {
				$lista2[] =   json_encode($nomeVar) . ':' . json_encode($item);
			}
			$s = join(",",$lista2);
			return sprintf('{"tipo":"%s","url":"%s",%s}',$this->getTipo(),$this->getURL(),$s);
		} else {
			return sprintf('{"tipo":"%s","url":"%s"}',$this->getTipo(),$this->getURL(),$s);
		}
	}


}
?>