<?php // $Rev: 415 $ $Author: eduluz $ $Date: 2009-09-11 16:53:34 -0300 (sex, 11 set 2009) $


/**
 * Classe usada como base para a criação de objetos do
 * padrão VO (Value Object).
 * O VO é usado para transportar dados entre camadas, servindo como
 * uma espécie de array associativo com métodos getters e setters.
 *
 * Exemplo de uso:
 *
 * $vo = new VOGenerico(array('id','nome','endereco'));
 * $vo->setNome('Maria');
 * echo $vo->getNome();
 *
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas LTDA
 * @created 29-08-2008
 * @package gama3.base.vo
 */
class VOGenerico {

	/**
	 * Array associativo que manterá a lista de atributos e
	 * seus respectivos valores.
	 *
	 * @var array
	 */
	protected $lsAtributos;



	/**
	 * Construtor da classe, inicializa a lista de atributos.
	 *
	 * @example $x = new VOGenerico(array('ID','nome'));
	 *
	 * @param array $listaNomesAtributos Array com os nomes dos atributos dinâmicos
	 * @param array $listaMapeamento Array associativo com o mapeamento entre as
	 * colunas da consulta e os atributos diâmicos do objeto. ('coluna_tabela' => 'atributo_objeto')
	 * @return VOGenerico
	 */
	public function __construct($listaNomesAtributos=false) {
		if (!$listaNomesAtributos) {
			die('Atributos não informados neste VO');
		}
		foreach ($listaNomesAtributos as $nome) {
			$this->lsAtributos[$nome] = null;
		}
	} // eof VOGenerico

	/**
	 * Método "mágico" que captura as chamadas dos métodos
	 * não existentes na classe. Assim ele tenta descobrir
	 * o que deve ser executado, e procede com a execução.
	 *
	 * @param string $nomeMetodo
	 * @param array $parametros
	 * @return mixed
	 */
	public function __call ($nomeMetodo,$parametros) {
		$nomeAtributo = strtolower(substr($nomeMetodo,3,1)).substr($nomeMetodo,4);
		if (strtolower($nomeAtributo) == 'id') {
			$nomeAtributo = 'ID';
		}
		if (strtolower(substr($nomeMetodo,0,3)) == 'get') {
			if (count($parametros) > 0) {
				return $this->_get($nomeAtributo,$parametros[0]);
			} else {
				return $this->_get($nomeAtributo,null);
			}
		} if (strtolower(substr($nomeMetodo,0,3)) == 'set') {
			$this->_set($nomeAtributo,$parametros[0]);
		} else {
			die('Método não existe: ' . $nomeMetodo);
		}
	} // eof __call

	/**
	 * Retorna (ou tenta retornar) o valor de um atributo do
	 * objeto. Caso o mesmo não exista, ou esteja nulo, retorna
	 * o valor default ($valorDefault).
	 *
	 * @param string $nomeAtributo
	 * @param mixed $valorDefault
	 * @return mixed
	 */
	public function _get($nomeAtributo,$valorDefault = null ) {

		if ( (array_key_exists($nomeAtributo,$this->lsAtributos)) &&
		(!is_null($this->lsAtributos[$nomeAtributo]))) {
			return $this->lsAtributos[$nomeAtributo];
		} else {
			/*$nomeAtributoTratado = VOGenerico::getNomeCampo($nomeAtributo);
			if ( (array_key_exists($nomeAtributoTratado,$this->lsAtributos)) &&
			(!is_null($this->lsAtributos[$nomeAtributoTratado]))) {
				return $this->lsAtributos[$nomeAtributo];
			}*/

			return $valorDefault;
		}
	} // eof _get

	/**
	 * Tenta atribuir ao atributo interno o valor passado
	 * por parâmetro.
	 *
	 * @param string $nomeAtributo
	 * @param mixed $valor
	 */
	public function _set($nomeAtributo,$valor) {
		if (array_key_exists($nomeAtributo,$this->lsAtributos)) {
			$this->lsAtributos[$nomeAtributo] = $valor;
		} else {
			echo('<pre>Atributo ' . $nomeAtributo . " não definido neste VO <br> \n");
			debug_print_backtrace();
			exit;
		}
	} // eof _set


	/**
	 * Retorna a lista de nomes dos atributos deste objeto.
	 *
	 * @return array
	 */
	public function _getListaAtributos() {
		return array_keys($this->lsAtributos);
	}

	/**
	 * Realiza o mapeamento do array associativo passado como parâmetro
	 * para o objeto atual.
	 *
	 * @param array $reg
	 */
	public function bind($reg,$lsMapeamento=false) {
		if (!$lsMapeamento) {
			foreach ($this->_getListaAtributos() as $k) {
				if (isset($reg[$k])) {
					$this->_set($k,$reg[$k]);
				}
			}
		} else {
			foreach ($lsMapeamento as $k1 => $k2) {
				if (isset($reg[$k1])) {
					$this->_set($k2,$reg[$k1]);
				}
			}
		}
	} // eof bind




	/**
	 * Recupera uma lista de VOs dada a lista de registro e o mapeamento dos
	 * campos para os atributos.
	 *
	 * @param array $listaRegistros
	 * @param array $mapa
	 * @return VOGenerico
	 */
	public function getListaVOs($listaRegistros,$mapa) {

		$listaNomeMetodosSet = array();
		foreach ($mapa as $coluna => $atributo) {
			$listaNomeMetodosSet[$coluna] = 'set' . strtoupper(substr($atributo,0,1)) . substr($atributo,1);
		}



		$listaResultado = array();
		foreach ($listaRegistros as $reg) {
			//			$vo = new $nomeClasse($mapa);
			$vo = $this->getInstance($mapa);

			foreach ($reg as $k => $v) {
				$metodo = $listaNomeMetodosSet[$k];
				//				echo $k;
				$vo->$metodo($v);
			}
			$listaResultado[] = $vo;
		}
		return $listaResultado;
	} // eof getListaVOs



	protected function getInstance($mapa) {
		return new VOGenerico($mapa);
	}


	/**
	 * Inclui um atributo dinamicamente ao objeto.
	 *
	 * @param string $nome
	 */
	public function addAtributo($nome) {
		$this->lsAtributos[$nome] = null;
	}


	/**
	 * Recupera o nome do campo, baseando-se nas regras de nomeação
	 *
	 * @param string $s
	 * @return string
	 */
	function getNomeCampo($s) {
		$i = strpos($s,".");
		if ($i === false) {
			$j = strrpos($s," ");
			if ($j === false) {
				$r = $s;
			} else {
				$r = substr($s,$j+1);
			}
		} else {
			$j = strpos($s," ",$i);
			if ($j === false) {
				$r = substr($s,$i+1);
			} else {
				$r = substr($s,$i+1,($j-$i));
			}
			//	   	echo $i .' '.$j .' '.($j-$i).' ';
		}
		return $r;
	}// getNomeCampo

} // eoc VOGenerico


/*
$s = "tb_xpto.nome_campo AS cp_nome"; // vale o cp_nome
//$s = "nome_campo AS xpto";
//$s = "nome_campo";
//$s = "tabela.nome_campo";


echo getNomeCampo($s);
*/

?>