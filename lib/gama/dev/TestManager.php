<?php // $Rev: 84 $ - $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $
/**
 * Classe responsavel por gerenciar os testes unitarios dos
 * modulos desenvolvidos.
 *
 * Deve ser usada da seguinte maneira:
 * 	1. Criar uma classe que extenda o TestManager, implementando o construtor
 *     que receba como parametros:
 * 			$app - referencia do objeto que representa a aplicacao
 * 			$GET - vetor de atributos recebidos por GET
 * 			$POST - vetor de atributos recebidos por POST
 * 			$basePath - diretorio onde reside o modulo
 *
 * 2. Criar os metodos que realizar os testes, obedecendo a regra de que cada
 *    metodo destes devera retornar:
 * 		null - se ok
 * 		string - mensagem de erro ou alerta.
 *
 * 3. Criar um arquivo de teste no diretorio-raiz do modulo, criando o objeto de
 *    teste (passando os parametros apropriados) e depois exibindo (ou gravando)
 *    o resultado dos testes. Veja que ao criar o objeto de teste automaticamente
 *    todos os testes nele contidos serao executados.
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @created-date 25-nov-2007
 * @package gama3.dev.testes
 */
class TestManager {

	var $listaMensagens = array();
	var $app;
	var $parms;
	var $basePath;


	/**
	 * Construtor da classe TestManager.
	 *
	 * @param MainGama $app
	 * @param array $GET
	 * @param array $POST
	 * @param string $basePath
	 * @return TestManager
	 */
	function TestManager($app,$GET=array(),$POST=array(), $basePath = './mod') {
		$this->basePath = $basePath;
		$this->app = $app;
		$this->parms = array_merge($GET,$POST);

		ob_start();

		$this->_exec();

		$saida = ob_get_contents();
		ob_end_clean();

		echo "\n<div style='border:1px solid black'><pre>Saida do Teste:</pre><br>\n";

		echo $saida;

		echo "\n</div><br>";

		echo "\n<div style='border:1px solid black'><pre>Resultados do Teste:</pre>";
		$this->_showResults();
		echo "\n</div><br>";

	} // BaseAction


	/**
	 * Metodo destinado a executar os testes contidos no objeto.
	 *
	 */
	function _exec() {
		$listaMetodos = get_class_methods(get_class($this));
		$this->listaMensagens = array();


		foreach ($listaMetodos as $metodo) {
			if ($this->_checkMethod($metodo))  {
				$msg = $this->$metodo();
				if (!is_null($msg)) {
					$this->listaMensagens[$metodo] = $msg;
				} else {
					$this->listaMensagens[$metodo] = 'Ok';
				}
			}
		}

	}


	/**
	 * Metodo destinado a verificar se o metodo cujo nome esta sendo
	 * passado como parametro devera ou nao ser executado.
	 *
	 * @param string $metodo - nom do metodo
	 * @return boolean - indicador se o metodo deve ou nao ser executado
	 */
	function _checkMethod($metodo) {
		$resp = true;
		if (substr($metodo,0,1) == "_")  {
			$resp = false;
		}

		if ($metodo == 'TestManager') {
			$resp = false;
		}

		if (strtolower($metodo) == strtolower( get_class($this))) {
			$resp = false;
		}


		return $resp;
	}




	function _showResults() {

		$s = '';
		foreach ($this->listaMensagens as $metodo => $msg) {
			if ($msg == 'Ok') {
				$s .= '<span style="color:navy">'.$metodo.' - '.$msg.'</span>';
			} else {
				$s .= '<span style="color:red">'.$metodo.' - '.$msg.'</span>';
			}
			$s .= "\n<br>";

		}

		echo $s;
	}


}
?>