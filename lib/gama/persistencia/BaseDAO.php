<?php // $Rev: 692 $ - $Author: eduluz $ $Date: 2014-08-29 14:47:03 -0300 (sex, 29 ago 2014) $

/**
 * Classe base para as que agrupam as consultas �s base de dados.
 *
 * @author Eduardo S. Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.base.persistencia
 */
class BaseDAO  {


	/**
	  * @var string DB
	  */
	private $DB;



	/**
	  * @var ADOConnection con
	  */
	protected $con;



	/**
	  * @var VOGenerico VO
	  */
	protected  $VO;



	/**
	 * Array associativo que faz o mapeamento dos campos das consultas
	 * para os atributos virtuais do objeto.
	 *
	 * @var array
	 */
	protected $lsMapeamento;



	/**
	  * @var int numRegistrosPorPagina
	  */
	private $numRegistrosPorPagina=-1;

	/**
	  * @var int numPagina
	  */
	private $numPagina=-1;

	//--------------------------------------------

	/**
	  * N�mero total de registros selecion�veis, desconsiderando a pagina��o.
	  * @var int numTotalRegistros
	  */
	private $numTotalRegistros;

	/**
	  * @var int numRegistrosCarregados
	  */
	private $numRegistrosCarregados;



	/**
	  * @var mixed numPaginas
	  */
	private $numPaginas;



	protected $mapaCampos = array();


	//--------------------------------------------

	/**
	 * Retorna o valor de numPaginas
	 * @return mixed
	 */
	public function getNumPaginas () {
		return $this->numPaginas;
	} // eof getNumPaginas



	//--------------------------------------------

	/**
	 * Retorna o valor de DB
	 * @return string
	 */
	public function getDB () {
		return $this->DB;
	} // eof getDB



	//--------------------------------------------

	/**
	 * Define o valor de DB
	 * @param string $DB
	 */
	public function setDB ($DB) {
		if (is_null($DB) || ($DB === false)) {
			$DB = '-';
		}
		$this->DB = $DB;
	} // eof setDB



	/**
	 * Define o valor de numPaginas
	 * @param mixed $numPaginas
	 */
	public function setNumPaginas ($numPaginas) {
		$this->numPaginas = $numPaginas;
	} // eof setNumPaginas



	//--------------------------------------------

	/**
	 * Retorna o n�mero total de registros selecion�veis, desconsiderando a pagina��o.
	 * @return int
	 */
	public function getNumTotalRegistros () {
		return $this->numTotalRegistros;
	} // eof getNumTotalRegistros

	/**
	 * Retorna o valor de numRegistrosCarregados
	 * @return int
	 */
	public function getNumRegistrosCarregados () {
		return $this->numRegistrosCarregados;
	} // eof getNumRegistrosCarregados

	/**
	 * Retorna o valor de numRegistrosPorPagina
	 * @return mixed
	 */
	public function getNumRegistrosPorPagina () {
		return $this->numRegistrosPorPagina;
	} // eof getNumRegistrosPorPagina

	/**
	 * Retorna o valor de numPagina
	 * @return mixed
	 */
	public function getNumPagina () {
		return $this->numPagina;
	} // eof getNumPagina



	//--------------------------------------------

	/**
	 * Define o valor de n�mero total de registros
	 * @param num $numRegistros
	 */
	public function setNumTotalRegistros ($numRegistros) {
		$this->numTotalRegistros = $numRegistros;
	} // eof setNumTotalRegistros

	/**
	 * Define o valor de numRegistrosCarregados
	 * @param int $numRegistrosCarregados
	 */
	public function setNumRegistrosCarregados ($numRegistrosCarregados) {
		$this->numRegistrosCarregados = $numRegistrosCarregados;
	} // eof setNumRegistrosCarregados


	/**
	 * Define o valor de numRegistrosPorPagina
	 * @param mixed $numRegistrosPorPagina
	 */
	public function setNumRegistrosPorPagina ($numRegistrosPorPagina) {
		$this->numRegistrosPorPagina = $numRegistrosPorPagina;
	} // eof setNumRegistrosPorPagina

	/**
	 * Define o valor de numPagina
	 * @param mixed $numPagina
	 */
	public function setNumPagina ($numPagina) {
		$this->numPagina = $numPagina;
	} // eof setNumPagina


	//--------------------------------------------

	/**
	 * Retorna o valor de con
	 * @return ADOConnection
	 */
	public function getCon ($idConn=false) {
		if ($idConn === false) {
			$idConn = $this->getDB();
			if (is_null($idConn)) {
				$idConn = '-';
			}
		} else {
			$idConn = '-';
		}
		return MainGama::getApp()->getCon($idConn);
		//		return $this->con;
	} // eof getCon


	/**
	 * Retorna o valor de VO
	 * @return VOGenerico
	 */
	public function getVO () {
		return $this->VO;
	} // eof getVO



	/**
	 * Retorna um array associativo com os nomes dos campos da consulta
	 * como chave, e os nomes dos atributos din�micos do VO como valor.
	 *
	 * @return array
	 */
	public function getLsMapeamento () {
		return $this->lsMapeamento;
	} // eof getLsMapeamento



	//--------------------------------------------


	/**
	 * Define o valor de VO
	 * @param VOGenerico $VO
	 */
	public function setVO ($VO) {
		$this->VO = $VO;
	} // eof setVO



	/**
	 * Define o valor de lsMapeamento.
	 * O 'lsMapeamento' � um array associativo que mant�m as rela��es entre
	 * os campos vindos de uma consulta SQL e o objeto VO vigente.
	 *
	 * @param mixed $lsMapeamento
	 */
	public function setLsMapeamento ($lsMapeamento) {
		$this->lsMapeamento = $lsMapeamento;
	} // eof setLsMapeamento


	//--------------------------------------------


	/**
	 * Construtor
	 * @return BaseDAO
	 */
	function __construct($idConn=false) {
		$this->setDB($idConn);
		//		$this->setCon(MainGama::getApp()->getCon($idConn));
	} // eof BaseDAO

	//--------------------------------------------



	/**
	 * Executa a consulta SQL passada como par�metro, retornando
	 * uma lista arrays ou VOs, dependendo do que est� configurado.
	 *
	 * @param string $sql
	 * @param array $parms
	 * @param boolean $paginar
	 * @return array
	 */
	function listar($sql,$parms=false,$paginar=false) {

		//		error_log($sql . "\n " . var_export($parms,true),3,'sql.log');
		try {
			if ($paginar) {
				$lista = $this->getCon()->SelectLimit($sql,$this->getNumRegistrosPorPagina(),$this->getNumRegistrosPorPagina()*($this->getNumPagina()-1));
			} else {
				$lista = $this->getCon()->GetArray($sql,$parms);
			}

			if (is_null($this->getVO())) {
				return $lista;
			} else {
				$resultado = array();
				foreach ($lista as $reg) {
					$vo = clone $this->getVO();
					$vo->bind($reg,$this->getLsMapeamento());
					$resultado[] = $vo;
				}
				return $resultado;
			}
		} catch (Exception $e) {
			$se = new SysException('Erro na execucao da pesquisa',30);
			//			$se = new SysException($e->getMessage(),30);
			$se->setDescricao($e->getMessage());
			throw $se;
		}

	} // eof listar



	/**
	 * Inicializa o objeto VO com os dados passando pelo par�metro.
	 *
	 * @param array $arr
	 */
	public function initVO($arr) {
		$this->setLsMapeamento($arr);
		$this->setVO(new VOGenerico(array_values($arr)));
	} // eof initVO


	/**
	 * Recupera a lista de registros da tabela, de acordo com os par�metros.
	 */
	public function getListaSQL($sql) {
		$lista = $this->getCon()->SelectLimit($sql,$this->getNumRegistrosPorPagina(),$this->getNumRegistrosPorPagina()*($this->getNumPagina()-1));
		return $lista;
	} // eof getLista



	/**
	 * Recupera a lista de registros da tabela, de acordo com os par�metros.
	 */
	public function getLista($tabela, $listaCampos='*', $filtro=null,$orderby=null) {
		if(!is_null($orderby)){
			if (!$this->isVulneravel($orderby)) {
				$orderby = " ORDER BY " . $orderby;
			}
		}
		if(is_null($filtro) || (strlen($filtro)==0)){
			$filtro = '';
		} else {
			$filtro = " WHERE $filtro ";
		}

		if ($this->getNumPaginas() < $this->getNumPagina()) {
			$this->setNumPagina($this->getNumPaginas());
		}
		$sql = "Select $listaCampos from $tabela $filtro $orderby";
		$lista = $this->getCon()->SelectLimit($sql,$this->getNumRegistrosPorPagina(),$this->getNumRegistrosPorPagina()*($this->getNumPagina()-1));
		return $lista;
	} // eof getLista


	/**
	 * Conta o n�mero de registros existentes na tabela $tabela, aplicando o filtro $filtro
	 *
	 * @param string $tabela
	 * @param string $filtro
	 * @return int
	 */
	function contaRegistros($tabela,$filtro=false) {
		if(!$filtro){
			$filtro = '';
		} else {
			$filtro = " WHERE $filtro ";
		}
		$sql = "Select count(*) from $tabela $filtro";
		$num = $this->getCon()->getOne($sql);
		return $num;
	} // eof contaRegistros


	/**
	 * M�todo que verifica se a string passada por par�metro � ou n�o
	 * pass�vel de ser uma instru��o SQL, caracterizando um risco de
	 * seguran�a.
	 *
	 * @param string $parm
	 * @return boolean true se � mesmo um risco de seguran�a
	 */
	public function isVulneravel($parm) {
		$expSQL = "/select[ ]|delete[ ]|update[ ]|insert[ ]|drop[ ]|create[ ]/i";

		$possuiInstrucoesSQL = preg_match($expSQL,$parm);

		if ($possuiInstrucoesSQL) {
			return true;
		} else {
			return false;
		}
	} // isVulneravel



	/**
	 * Recupera o nome do campo da tabela, com base no nome
	 * do campo da tela.
	 *
	 * @param string $nomeCampoTela
	 * @return string
	 */
	protected function getNomeColunaTabela($nomeCampoTela) {		
		$key = array_search($nomeCampoTela, $this->mapaCampos);
		if ($key === false) {
			if ($nomeCampoTela == 'id') {
				$nomeCampoTela = 'ID';
				$key = array_search($nomeCampoTela, $this->mapaCampos);
				if ($key) {
					return $key;
				}
			}
			$se = new SysException('Erro (1) - atributo nao mapeado: '.$nomeCampoTela,999);
			throw $se;
		} else {
			return $key;
		}
	} // getNomeColunaTabela


	/**
	 * Recupera o nome do campo da tela, com base no nome do campo da tela.
	 *
	 * @param string $nomeCampoTabela
	 * @return string
	 */
	protected function getNomeCampoTela($nomeCampoTabela) {
		if (!isset($this->mapaCampos[$nomeCampoTabela])) {
			$se = new SysException('Erro (2) - atributo nao mapeado: '.$nomeCampoTabela,999);
			throw $se;
		} else {
			return $this->mapaCampos[$nomeCampoTabela];
		}
	} // getNomeCampoTela



	/**
	 * Define o valor do mapeamento do campo para o nome da tabela;
	 *
	 * @param string $nomeCampoTabela
	 * @param string $nomeCampoTela
	 */
	protected function setNomeCampo($nomeCampoTabela,$nomeCampoTela=null) {
		if (is_null($nomeCampoTela)) {
			$nomeCampoTela = $nomeCampoTabela;
		}
		$this->mapaCampos[$nomeCampoTabela] = $nomeCampoTela;
	} // setNomeCampo



} // eoc BaseDAO


?>