<?php // $Rev: 259 $ $Author: eduluz $ $Date: 2008-11-24 12:12:15 -0200 (seg, 24 nov 2008) $

/**
 * Classe que encapsula um array associavito, usado especialmente em listas
 * que serão apresentados nos formulários como combo-boxes.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.utils.datatype
 */
class ArrayAssociativo {

	/**
	 * Lista de valores.
	 *
	 * @var array
	 */
	private $lista;
	
	/**
	 * Chave selecionada.
	 *
	 * @var mixed
	 */
	private $chave;

	/**
	 * Construtor da classe. 
	 * Inicializa as variáveis.
	 *
	 */
	function __construct() {
		$this->lista = array();
		$this->chave = null;
	} // eof __construct
	

	/**
	 * Recupera a lista inteira.
	 *
	 * @return array
	 */
	public function getLista() {
		return $this->lista;
	} // eof getLista

	/**
	 * Define o conteúdo da lista.
	 *
	 * @param array $lista
	 */
	public function setLista($lista) {
		$this->lista = $lista;
	} // eof setLista

	/**
	 * Define o valor da chave.
	 *
	 * @param mixed $chave
	 */
	public function setChave ($chave) {
		$this->chave = $chave;
	} // eof setChave

	/**
	 * Recupera o valor da chave selecionada.
	 *
	 * @return mixed
	 */
	public function getChave() {
		return $this->chave;
	} // eof getChave

	/**
	 * Recupera o elemento selecionado com 'chave'.
	 * Se nenhuma chave estiver selecionada, recupera o
	 * primeiro item.
	 *
	 * @return mixed
	 */
	public function getElementoSelecionado() {
		if (is_null($this->getChave())) {
			reset($this->lista);
			$this->setChave(key($this->lista));
		}
		return $this->lista[$this->getChave()];
	} // eof getElementoSelecionado

	/**
	 * Recupera a lista de índices da lista.
	 *
	 * @return array
	 */
	public function getKeys() {
		return array_keys($this->getLista());
	} // eof getKeys

	
	/**
	 * Recupera a lista dos valores da lista.
	 *
	 * @return array
	 */
	public function getValues() {
		return array_values($this->getLista());
	} // eof getValues

	
	/**
	 * Inclui um item na lista, passando o índice e o valor a ser
	 * inserido.
	 *
	 * @param mixed $chave
	 * @param mixed $valor
	 */
	public function addItem($chave,$valor) {
		$this->lista[$chave] = $valor;
	} // eof addItem

	/**
	 * Elimina um item da lista.
	 *
	 * @param mixed $chave
	 */
	public function delItem($chave) {
		unset($this->lista[$chave]);
	} // eof delItem


	/**
	 * Dada uma lista de VOs, preenche os dados internos apenas com uma chave e um
	 * valor descritivo.
	 *
	 * @param array $listaVO
	 * @param string $nomeFuncGetChave
	 * @param string $nomeFuncGetValor
	 */
	public function import($listaVO,$nomeFuncGetChave,$nomeFuncGetValor) {
		foreach ($listaVO as $vo) {
			$this->addItem($vo->$nomeFuncGetChave(),$vo->$nomeFuncGetValor());
		}
	} // eof import
	
	
	
	public function getPrimeiroItem() {
		$listaK = array_keys($this->lista);
		$k = reset($listaK);
		return $this->lista[$k];
	}
	
	public function getUltimoItem() {
		$listaK = array_keys($this->lista);		
		$k = array_pop($listaK);
		return $this->lista[$k];
	}
	
} // eoc ArrayAssociativo


?>