<?php // $Rev: 700 $ - $Author: eduluz $ $Date: 2015-03-12 10:43:16 -0300 (qui, 12 mar 2015) $

/**
 * Classe que faz a interface entre o aplicativo e a tabela no banco de dados,
 * usando o padrao Active Record.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.base.persistencia
 */
class BaseAR extends ADOdb_Active_Record {

	/**
	 * Constante para definir que o conteúdo de um
	 * campo é explicitamente NULL.
	 */
	public static $NULL = 'NULL';



	/**
	 * Mantém o nome do atributo que contém a referência da sequence e
	 * o nome desta.
	 *
	 * @var array
	 */
	protected $__oid = null;

	/**
	 * Inicializa o objeto com o nome da tabela e a lista de campos que
	 * participam da chave primaria.
	 *
	 * @param string $tabela nome da tabela onde o objeto sera persistido.
	 * @param array $pks lista de atributos que participam da chave primaria
	 */
	function init($tabela=false,$pks=false,$db=false) {
		if (!is_array($pks)) {
			throw new SysException("Erro na definicao das PKs: $pks - Array era esperado. ",1);
		}

  		if (strpos($tabela,'.')===false) {
   			$tabela = 'public.'.$tabela;
  		}

		if ($db) {
			if (is_string($db)) {
				ADODB_Active_Record::__construct($tabela,$pks,MainGama::getApp()->getCon($db));
			} else if (is_object($db)) {
				ADODB_Active_Record::__construct($tabela,$pks,$db);
			} else {
				throw new SysException('Parâmetro incorreto no construtor do AR - DSN ou ADOConnection esperado',999);
			}
		} else {
			ADODB_Active_Record::__construct($tabela,$pks,MainGama::getApp()->getCon());
		}
	} // eof init


	/**
	 * Retorna o nome da tabela.
	 *
	 * @return string
	 */
	function getNomeTabela() {
		return $this->_table;
	}

	/**
	 * Define o nome da sequence e do atributo que está a ela
	 * associado.
	 *
	 * @param string $oid Nome do atributo autoincremental
	 * @param string|boolean $nomeSequence Opcional - nome da sequence
	 */
	function setOID($oid,$nomeSequence=false) {
		if (!property_exists($this,$oid)) {
			throw new SysException("Atributo indefinido: $oid na classe: " . get_class($this),1);
		}

		if ($nomeSequence) {
  			if (strpos($nomeSequence,'.') === false) {
   				$nomeSequence = 'public.'.$nomeSequence;
  			}
			$this->__oid = array($nomeSequence => $oid );
		} else {
			$this->__oid = array($oid => $oid);
		}
	} // eof setOID


	/**
	 * Retorna a referência a sequence.
	 *
	 * @return array
	 */
	function getOID(){
		return $this->__oid;
	} // eof getOID


	/**
	 * Recupera a lista de nomes de campos que fazem parte da chave primaria
	 * da tabela.
	 *
	 * @return array
	 */
	function getPKs() {
		$arr = $this->GetPrimaryKeys($this->DB(),$this->getNomeTabela());
		return $arr;
	} // eof getPKs

	/**
	 * Verifica se o objeto atual ja existe (update) ou nao existe (insert).
	 *
	 * @return string
	 */
	function checkPKs() {
		$pks = $this->getPKs();
		$acao = 'insert';
		foreach ($pks as $campo) {
			if ((!is_null($this->$campo)) && (!empty($this->$campo))) {
				$acao = 'update';
			} else {
				$this->$campo = null;
			}
		}
		return $acao;
	} // eof checkPKs

	/**
	 * Realiza a persistencia do objeto atual.
	 *
	 */
	function gravar() {
		switch ($this->checkPKs()) {
			case 'insert':
				$res = $this->Insert();
				if (!$res) {
					$e = new SysException($this->ErrorMsg(),$this->ErrorNo());
					throw ($e);
				}
				break;
			case 'update' :
				$res = $this->Update();
				if (!$res) {
					$e = new SysException($this->ErrorMsg(),$this->ErrorNo());
					throw ($e);
				}
				break;
		}
	} // eof gravar



	/**
	 * Insere o registro.
	 *
	 * @return unknown
	 */
	function insert() {
		$res = parent::Insert();
		return $res;
	} // eof insert

	/**
	 * Atualiza o registro
	 *
	 * @return unknown
	 */
	function update() {
		$res = parent::Update();
		return $res;
	} // eof update


	function delete() {
		$res = parent::Delete();
		return $res;
	} // eof delete


	/**
	 * Carrega o objeto atual, com base nos parâmetros informados,
	 * ou se nada for informado, monta a condição com base na chave
	 * primária.
	 *
	 * @param string|boolean $where Condição SQL da seleção
	 */
	function load($where=false) {
		if (!$where) {
			$where = $this->getPK();
		}
		parent::Load($where);
	} // load


	/**
	 * Metodo que recupera o objeto atual.
	 * @deprecated
	 */
	function recuperar() {
		$this->load();
	} // eof recuperar

	/**
	 * Metodo que recupera e retorna a string com a condicao que identifica
	 * o objeto.
	 *
	 * @return string
	 */
	function getPK() {

		$s = '';
		$pks = $this->getPKs();

		if (count($pks) == 1) {
			$campo = $pks[0];

			if (is_numeric($this->$campo)) {

				$s = " $campo = " . $this->$campo;
			} else {

				$s = " $campo = '" . $this->$campo ."' ";
			}
		} else {
			for ($i=0;$i<count($pks);$i++) {
				$campo = $pks[$i];
				if ($i > 0) {
					$s .= ' AND ';
				}

				if (!property_exists(get_class($this),$campo)) {
					$se = new SysException('Erro na chave primaria',15);
					$se->setDescricao("A chave primária da tabela " . $this->getNomeTabela() . " nao e numerica ou e inexistente ");
					throw $se;
				}
				if (is_numeric($this->$campo)) {
					$s .= " $campo = " . $this->$campo . " ";
				} else {
					$s .= " $campo = '" . $this->$campo . "' ";
				}
			}
		}
		return $s;
	} //  eof getPK


	/**
	 * Metodo que mapeia o vetor passado como parametro para os atributos
	 * do objeto.
	 *
	 * @param array $arr
	 */
	function bind($arr) {
		$atributos = get_object_vars($this);
		foreach ($atributos as $k => $v) {
			if (array_key_exists($k, $arr)) {
				$this->$k = $arr[$k];
			}
		}
	} // eof bind


	/**
	 * Metodo que exibe a lista de atributos "normais" do AR.
	 * E' usado para facilitar os trabalhos de desenvolvimento
	 *
	 */
	protected function _showAtributos() {
		$atrs = get_object_vars($this);
		foreach ($atrs as $k => $v) {
			if (substr($k,0,1) != '_') {
				//echo "\n $k";
			}
		}
	} // eof _showAtributos


	protected function _getAtributos() {
		$atrs = get_object_vars($this);
		foreach ($atrs as $k => $v) {
			if (substr($k,0,1) == '_') {
				unset($atrs[$k]);
			}
		}
		return $atrs;
	} // eof _showAtributos



	/**
	 * Recupera o último valor da sequence
	 *
	 * @return boolean|integer
	 */
	function LastInsertID() {
		if (!is_null($this->getOID())) {
			$oid = $this->getOID();

			$sequence = key($oid);
			$atributo = $oid[$sequence];

			if (is_null($this->$atributo) || ($this->$atributo == 0)) {
				$sql = "select currval('".$sequence."')";
				$arr = $this->DB()->GetArray($sql);
				$currval = $arr[0]['currval'];
				$this->$atributo = $currval;
			}
			return $this->$atributo;
		} else {
			return false;
		}
	} // eof LastInsertID







	/**
	 * Monta o filtro de uma consulta no banco.
	 *
	 * @return string
	 */
	function montaWhere(){
		$table =& $this->TableInfo();
		$where = "";
		foreach($table->flds as $name=>$fld) {
			if (!is_null($this->$name)){
				if ($where == "") {
					$where .= " WHERE ($name = '". $this->$name."')";
				} else {
					$where .= " AND ($name = '". $this->$name."')";
				}
			}
		}
		return $where;
	} // eof montaWhere



	/**
	 * Retorna uma lista com todos os registros da tabela sem filtro algum.
	 *
	 * @return array
	 */
	function listAll($filtro=null,$orderby=null){
		if(!is_null($orderby)){
			$orderby = " ORDER BY " . $orderby;
		}
		$where = "";
		if(!is_null($filtro)){
			$where = "WHERE ".join(" AND ",$filtro);
		}

		$sql = "SELECT * FROM ? $where $orderby";
		$resultado = $this->DB()->Execute($sql,array($this->getNomeTabela()));

		$retorno = array();

		foreach ($resultado as $registro){
			$nome = get_class($this);
			$obj = new $nome();
			$obj->bind($registro);

			$retorno[] = $obj;
		}

		return $retorno;

	} // eof listAll



	/**
	 * Retorna a conexão do AR
	 *
	 * @return ADOConnection
	 */
	function getDB() {
		return $this->DB();
	} // eof getDB



	protected function _getCampos() {
		return get_object_vars($this);
	} // _getCampos


	/**
	 * Monta o SQL usado na consulta de atualização de um
	 * dado registro no banco.
	 *
	 * @return string
	 */
	function montaSQLUpdate() {		
		$s = 'UPDATE ' . $this->getNomeTabela() . ' SET ';
		$arr = $this->_getCampos();		
		$arr2 = array();
		foreach ($arr as $k => $v) {

			//Este if foi incluído na função
			if(($v === self::$NULL) && (substr($k,0,1)!='_')) {
				$arr2[] = "$k = null";
				continue;
			}
			//Fim modificação

			if (!is_null($v) && (substr($k,0,1)!='_')) {
				switch (gettype($v)) {
					case "string": $v = $this->getDB()->escape2($v);
									
								   $v = addcslashes($v, "\'\"");
                                                        $arr2[] = "$k = '{$v}'"; break;
					default      : $arr2[] = "$k = $v";
				}
			}
		}
		$s .= join(',',$arr2);

		$s .= " WHERE " . $this->getPK();

		return $s;
	} // montaSQLUpdate







	/**
	 * Verifica se o registro é válido ou não.
	 *
	 * @return boolean
	 */
	public function isValid() {
		if (is_null($this->getID()) || ($this->getID() <= 0)) {
			return false;
		} else {
			return true;
		}
	} // eof isValid



	/**
	 * Converte o conteúdo deste objeto para
	 * a representação JSON.
	 *
	 * @return string
	 */
	public function _tostring() {
		return json_encode($this->_getAtributos());
	} // _tostring


} // eoc  BaseAR

?>