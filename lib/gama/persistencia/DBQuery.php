<?php // $Rev: 495 $ - $Author: eduluz $ $Date: 2010-04-06 09:46:54 -0300 (ter, 06 abr 2010) $

define('QUERY_STYLE_ASSOC', ADODB_FETCH_ASSOC);
define('QUERY_STYLE_NUM'  , ADODB_FETCH_NUM);
define('QUERY_STYLE_BOTH' , ADODB_FETCH_BOTH);

/**
 * Classe que visa encapsular as operações de consulta a banco de dados.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.base.persistencia
 */
class DBQuery {

	/**
	 * Conexão com o servidor de banco de dados.
	 *
	 * @var ADOConnection
	 */
	protected $con;

	/**
	 * @var MainGama
	 */
	protected $app;


	/**
	 * Lista de campos
	 *
	 * @var VOGenerico
	 */
	protected $fields;



	/**
	  * @var mixed vo
	  */
	private $vo;

	/**
	  * @var mixed mapaVO
	  */
	private $mapaVO;



	protected $query;
	protected $table_list;
	protected $where;
	protected $order_by;
	protected $group_by;
	protected $limit;
	protected $offset;
	protected $join;
	protected $type;
	protected $update_list;
	protected $value_list;
	protected $create_table;
	protected $create_definition;
	protected $include_count;
	protected $_table_prefix;
	protected $_query_id = null;
	protected $_old_style = null;



	/**
	 * Retorna o valor de con
	 * @return ADOConnection
	 */
	public function getCon () {
		return $this->con;
	} // eof getCon



	/**
	 * Retorna o valor de app
	 * @return mixed
	 */
	public function getApp () {
		return $this->app;
	} // eof getApp

	/**
	 * Retorna o valor de fields
	 * @return mixed
	 */
	public function getFields () {
		return $this->fields;
	} // eof getFields


	/**
	 * Retorna o valor de vo
	 * @return VOGenerico
	 */
	public function getVO () {
		return $this->vo;
	} // eof getVo

	/**
	 * Retorna um array com os nomes dos campos da consulta, na mesma
	 * ordem dos atributo dinâmicos do VO.
	 *
	 * @return array
	 */
	public function getMapaVO () {
		return $this->mapaVO;
	} // eof getMapaVO






	//--------------------------------------------



	/**
	 * Define o valor de con
	 * @param ADOConnection $con
	 */
	public function setCon ($con) {
		$this->con = $con;
	} // eof setCon


	/**
	 * Define o valor de app
	 * @param mixed $app
	 */
	public function setApp ($app) {
		$this->app = $app;
	} // eof setApp



	/**
	 * Define o valor de fields
	 * @param mixed $fields
	 */
	public function setFields ($fields) {
		$this->fields = $fields;
	} // eof setFields


	/**
	 * Define o valor de vo, e opcionalmente a lista de mapeamento
	 * para fazer o bind.
	 *
	 * @param VOGenerico $vo
	 * @param array $mapa
	 */
	public function setVO ($vo,$mapa=null) {
		$this->vo = $vo;
		$this->setMapaVO($mapa);
	} // eof setVO

	/**
	 * Define o valor de mapaVO.
	 * O 'mapaVO' é um array associativo que mantém as relações entre
	 * os campos vindos de uma consulta SQL e o objeto VO vigente.
	 *
	 * @param array $mapaVO
	 */
	public function setMapaVO ($mapaVO) {
		$this->mapaVO = $mapaVO;
	} // eof setMapaVO



	//--------------------------------------------


	/**
	 * Construtor
	 *
	 * @param ADOConnection $con
	 * @param MainGama $app
	 * @param string $prefix
	 * @return DBQuery
	 */
	function DBQuery($app=false,$prefix = null)
	{
		if (isset($prefix)) {
			$this->_table_prefix = $prefix;
		} else {
			$this->_table_prefix = '';
		}

		if (!$app) {
			$app = MainGama::getApp();
		}

		$this->setApp($app);
		$this->setCon($app->getCon());


		$this->include_count = false;
		$this->clear();
	} // eof DBQuery




	/**
	 * Carrega os resultados de uma única coluna, para apenas uma linha.
	 * Deve ser usado para recuperar o valor de um cálculo ou contagem.
	 */
	function loadResult()
	{
		$result = false;
		if (! $this->exec(ADODB_FETCH_NUM)) {
			throw new SysException($db->ErrorMsg(),999);
		} else if ($data = $this->fetchRow()) {
			print_r($data);
			$result =  $data[0];
		}
		$this->clear();
		return $result;
	} // eof loadResult



	function getListaVOs() {
		$mapa  = array();
		if (is_null($this->getMapaVO())) {
			$map = false;
			$map = true;
			foreach ($this->getVO()->_getListaAtributos() as $i => $nomeAtributo) {
				$mapa[$nomeAtributo] = $nomeAtributo;
			}
		} else {
			$map = true;
			$aux = $this->getMapaVO();
			foreach ($this->getVO()->_getListaAtributos() as $i => $nomeAtributo) {
				$mapa[$aux[$i]] = $nomeAtributo;
			}
		}
		$fetchMode = $this->getCon()->fetchMode;
		$this->getCon()->fetchMode = ADODB_FETCH_ASSOC;

		$sql = $this->prepare();

		$res = $this->getCon()->Execute($sql);

		$lista = array();
		while ($arr = $res->FetchRow()) {
			$vo = clone $this->getVO();
			if ($map) {
				foreach ($arr as $k => $v) {
					$vo->_set($mapa[$k],$v);
				}
			}
			$lista[] = $vo;
//			print_r($arr);
		}
		$this->getCon()->fetchMode = $fetchMode;
		return $lista;
	}


	/**
	 * Monta a string da consulta SQL.
	 *
	 * @return string
	 */
	function prepareSelect()
	{
		$q = 'SELECT ';

		// (2)
		//		if ($this->include_count) {
		//			$q .= ' SQL_CALC_FOUND_ROWS ';
		//		}

		if (isset($this->query)) {
			if (is_array($this->query)) {
				$inselect = false;
				$q .= implode(',', $this->query);
			} else {
				$q .= $this->query;
			}
		} else if (!is_null($this->getVO())) {
			if (is_null($this->getMapaVO())) {
				$lista = $this->getVO()->_getListaAtributos();
			} else {
				$lista = $this->getMapaVO();
			}
			$q .= join(', ',$lista);
		} else {
			$q .= '*';
		}

		$q .= "\n FROM ";
		if (isset($this->table_list)) {
			if (is_array($this->table_list)) {
				//	(1)			$q .= '( ';	// Required for MySQL 5 compatability.
				$q .= ' ';
				$intable = false;
				foreach ($this->table_list as $table_id => $table) {
					if ($intable)
					$q .= ",";
					else
					$intable = true;
					$q .= ' ' . $this->_table_prefix . $table . ' ';
					if (! is_numeric($table_id))
					$q .= " as $table_id";
				}
				$q .= ' ';
				//	(1)			$q .= ' )'; // MySQL 5 compat.
			} else {
				$q .= $this->_table_prefix . $this->table_list;
			}
		} else {
			return false;
		}
		$q .= "\n". $this->make_join($this->join);
		$q .= "\n". $this->make_where_clause($this->where);
		$q .= "\n". $this->make_group_clause($this->group_by);
		$q .= "\n". $this->make_order_clause($this->order_by);
		return $q;
	}




	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	// @@@@@@@  NÃO VERIFICADOS  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@


	function clear()
	{

		$ADODB_FETCH_MODE = $this->getCon()->fetchMode;
		if (isset($this->_old_style)) {
			$ADODB_FETCH_MODE = $this->_old_style;
			$this->_old_style = null;
		}
		$this->type = 'select';
		$this->query = null;
		$this->table_list = null;
		$this->where = null;
		$this->order_by = null;
		$this->group_by = null;
		$this->limit = null;
		$this->offset = -1;
		$this->join = null;
		$this->value_list = null;
		$this->update_list = null;
		$this->create_table = null;
		$this->create_definition = null;
		if ($this->_query_id) {
			$this->_query_id->Close();
		}
		$this->_query_id = null;
	}

	function clearQuery()
	{
		if ($this->_query_id) {
			$this->_query_id->Close();
		}
		$this->_query_id = null;
	}

	/**
   * Add a hash item to an array.
   *
   * @access	private
   * @param	string	$varname	Name of variable to add/create
   * @param	mixed	$name	Data to add
   * @param	string 	$id	Index to use in array.
   */
	function addMap($varname, $name, $id)
	{
		if (!isset($this->$varname)) {
			$this->$varname = array();
		}

		if (isset($id)) {
			$this->{$varname}[$id] = $name;
		} else {
			$this->{$varname}[] = $name;
		}
	}

	/**
   * Adds a table to the query.  A table is normally addressed by an
   * alias.  If you don't supply the alias chances are your code will
   * break.  You can add as many tables as are needed for the query.
   * E.g. addTable('something', 'a') will result in an SQL statement
   * of {PREFIX}table as a.
   * Where {PREFIX} is the system defined table prefix.
   *
   * @param	string	$name	Name of table, without prefix.
   * @parem	string	$id	Alias for use in query/where/group clauses.
   */
	function addTable($name, $id = null)
	{
		$this->addMap('table_list', $name, $id);
	}

	/**
   * Add a clause to an array.  Checks to see variable exists first.
   * then pushes the new data onto the end of the array.
   */
	protected function addClause($clause, $value, $check_array = true)
	{

		MainGama::getApp()->getDebug()->log("Adding '$value' to $clause clause",'DBQuery');
		if (!isset($this->$clause))
		$this->$clause = array();
		if ($check_array && is_array($value)) {
			foreach ($value as $v) {
				array_push($this->$clause, $v);
			}
		} else {
			array_push($this->$clause, $value);
		}
	}

	/**
   * Add the actual select part of the query.  E.g. '*', or 'a.*'
   * or 'a.field, b.field', etc.  You can call this multiple times
   * and it will correctly format a combined query.
   *
   * @param	string	$query	Query string to use.
   */
	function addQuery($query)
	{
		$this->addClause('query', $query);
	}

	function addInsert($field, $value, $set = false, $func = false)
	{
		if ($set)
		{
			if (is_array($field))
			$fields = $field;
			else
			$fields = explode(',', $field);

			if (is_array($value))
			$values = $value;
			else
			$values = explode(',', $value);

			for($i = 0; $i < count($fields); $i++)
			$this->addMap('value_list', $this->quote($values[$i]), $fields[$i]);
		}
		else if (!$func)
		$this->addMap('value_list', $this->quote($value), $field);
		else
		$this->addMap('value_list', $value, $field);
		$this->type = 'insert';
	}

	// implemented addReplace() on top of addInsert()

	function addReplace($field, $value, $set = false, $func = false)
	{
		$this->addInsert($field, $value, $set, $func);
		$this->type = 'replace';
	}


	function addUpdate($field, $value, $set = false)
	{
		if ($set)
		{
			if (is_array($field))
			$fields = $field;
			else
			$fields = explode(',', $field);

			if (is_array($value))
			$values = $value;
			else
			$values = explode(',', $value);

			for($i = 0; $i < count($fields); $i++)
			$this->addMap('update_list', $values[$i], $fields[$i]);
		}
		else
		$this->addMap('update_list', $value, $field);
		$this->type = 'update';
	}

	function createTable($table)
	{
		$this->type = 'createPermanent';
		$this->create_table = $table;
	}

	function createTemp($table)
	{
		$this->type = 'create';
		$this->create_table = $table;
	}

	function dropTable($table)
	{
		$this->type = 'drop';
		$this->create_table = $table;
	}

	function dropTemp($table)
	{
		$this->type = 'drop';
		$this->create_table = $table;
	}

	function alterTable($table)
	{
		$this->create_table = $table;
		$this->type = 'alter';
	}

	function addField($name, $type)
	{
		if (! is_array($this->create_definition))
		$this->create_definition = array();
		$this->create_definition[] = array('action' => 'ADD',
		'type' => '',
		'spec' => $name . ' ' . $type);
	}

	function alterField($name, $type)
	{
		if (! is_array($this->create_definition))
		$this->create_definition = array();
		$this->create_definition[] = array('action' => 'CHANGE',
		'type' => '',
		'spec' => $name . ' ' . $name . ' ' . $type);
	}

	function dropField($name)
	{
		if (! is_array($this->create_definition))
		$this->create_definition = array();
		$this->create_definition[] = array('action' => 'DROP',
		'type' => '',
		'spec' => $name);
	}

	function addIndex($name, $type)
	{
		if (! is_array($this->create_definition))
		$this->create_definition = array();
		$this->create_definition[] = array('action' => 'ADD',
		'type' => 'INDEX',
		'spec' => $name . ' ' . $type);
	}

	function dropIndex($name)
	{
		if (! is_array($this->create_definition))
		$this->create_definition = array();
		$this->create_definition[] = array('action' => 'DROP',
		'type' => 'INDEX',
		'spec' => $name);
	}

	function dropPrimary()
	{
		if (! is_array($this->create_definition))
		$this->create_definition = array();
		$this->create_definition[] = array('action' => 'DROP',
		'type' => 'PRIMARY KEY',
		'spec' => '');
	}

	function createDefinition($def)
	{
		$this->create_definition = $def;
	}

	function setDelete($table)
	{
		$this->type = 'delete';
		$this->addMap('table_list', $table, null);
	}

	/**
   * Add where sub-clauses.  The where clause can be built up one
   * part at a time and the resultant query will put in the 'and'
   * between each component.
   *
   * Make sure you use table aliases.
   *
   * @param	string 	$query	Where subclause to use
   */
	function addWhere($query)
	{
		if (isset($query))
		$this->addClause('where', $query);
	}

	/**
   * Add a join condition to the query.  This only implements
   * left join, however most other joins are either synonymns or
   * can be emulated with where clauses.
   *
   * @param	string	$table	Name of table (without prefix)
   * @param	string	$alias	Alias to use instead of table name (required).
   * @param	mixed	$join	Join condition (e.g. 'a.id = b.other_id')
   *				or array of join fieldnames, e.g. array('id', 'name);
   *				Both are correctly converted into a join clause.
   */
	function addJoin($table, $alias, $join, $type = 'left')
	{
		$var = array ( 'table' => $table,
		'alias' => $alias,
		'condition' => $join,
		'type' => $type );

		$this->addClause('join', $var, false);
	}

	function leftJoin($table, $alias, $join)
	{
		$this->addJoin($table, $alias, $join, 'left');
	}

	function rightJoin($table, $alias, $join)
	{
		$this->addJoin($table, $alias, $join, 'right');
	}

	function innerJoin($table, $alias, $join)
	{
		$this->addJoin($table, $alias, $join, 'inner');
	}

	/**
   * Add an order by clause.  Again, only the fieldname is required, and
   * it should include an alias if a table has been added.
   * May be called multiple times.
   *
   * @param	string	$order	Order by field.
   */
	function addOrder($order)
	{
		if (isset($order)) {
			$this->addClause('order_by', $order);
		}
	}

	/**
   * Add a group by clause.  Only the fieldname is required.
   * May be called multiple times.  Use table aliases as required.
   *
   * @param	string	$group	Field name to group by.
   */
	function addGroup($group)
	{
		$this->addClause('group_by', $group);
	}

	/**
   * Set a limit on the query.  This is done in a database-independent
   * fashion.
   *
   * @param	integer	$limit	Number of rows to limit.
   * @param	integer	$start	First row to start extraction.
   */
	function setLimit($limit, $start = -1)
	{
		$this->limit = $limit;
		$this->offset = $start;
	}

	/**
   * Set include count feature, grabs the count of rows that
   * would have been returned had no limit been set.
   */
	function includeCount()
	{
		$this->include_count = true;
	}

	/**
   * Prepare a query for execution via db_exec.
   *
   */
	function prepare($clear = false)
	{
		switch ($this->type) {
			case 'select':
				$q = $this->prepareSelect();
				break;
			case 'update':
				$q = $this->prepareUpdate();
				break;
			case 'insert':
				$q = $this->prepareInsert();
				break;
			case 'replace':
				$q = $this->prepareReplace();
				break;
			case 'delete':
				$q = $this->prepareDelete();
				break;
			case 'create':	// Create a temporary table
			$s = $this->prepareSelect();
			$q = 'CREATE TEMPORARY TABLE ' . $this->_table_prefix . $this->create_table;
			if (!empty($this->create_definition))
			$q .= ' ' . $this->create_definition;
			$q .= ' ' . $s;
			break;
			case 'alter':
				$q = $this->prepareAlter();
				break;
			case 'createPermanent':	// Create a temporary table
			$s = $this->prepareSelect();
			$q = 'CREATE TABLE ' . $this->_table_prefix . $this->create_table;
			if (!empty($this->create_definition))
			$q .= ' ' . $this->create_definition;
			$q .= ' ' . $s;
			break;
			case 'drop':
				$q = 'DROP TABLE IF EXISTS ' . $this->_table_prefix . $this->create_table;
				break;
		}
		if ($clear)
		$this->clear();
		return $q;
		MainGama::getApp()->getDebug()->log($q,'DBQuery');
	}


	function prepareUpdate()
	{
		// You can only update one table, so we get the table detail
		$q = 'UPDATE ';
		if (isset($this->table_list)) {
			if (is_array($this->table_list)) {
				reset($this->table_list);
				// Grab the first record
				list($key, $table) = each ($this->table_list);
			} else {
				$table = $this->table_list;
			}
		} else {
			return false;
		}
		$q .= ' ' . $this->_table_prefix . $table . ' ';

		$q .= ' SET ';
		$sets = '';
		foreach( $this->update_list as $field => $value) {
			if ($sets)
			$sets .= ", ";
			$sets .= " $field  = " . $this->quote($value);
		}
		$q .= $sets;
		$q .= $this->make_where_clause($this->where);
		return $q;
	}

	function prepareInsert()
	{
		$q = 'INSERT INTO ';
		if (isset($this->table_list)) {
			if (is_array($this->table_list)) {
				reset($this->table_list);
				// Grab the first record
				list($key, $table) = each ($this->table_list);
			} else {
				$table = $this->table_list;
			}
		} else {
			return false;
		}
		$q .= ' ' . $this->_table_prefix . $table . ' ';

		$fieldlist = '';
		$valuelist = '';
		foreach( $this->value_list as $field => $value) {
			if ($fieldlist)
			$fieldlist .= ",";
			if ($valuelist)
			$valuelist .= ",";
			$fieldlist .= ' ' . trim($field) . ' ';
			$valuelist .= $value;
		}
		$q .= "($fieldlist) values ($valuelist)";
		return $q;
	}

	function prepareReplace()
	{
		$q = 'REPLACE INTO ';
		if (isset($this->table_list)) {
			if (is_array($this->table_list)) {
				reset($this->table_list);
				// Grab the first record
				list($key, $table) = each ($this->table_list);
			} else {
				$table = $this->table_list;
			}
		} else {
			return false;
		}
		$q .= ' ' . $this->_table_prefix . $table . ' ';

		$fieldlist = '';
		$valuelist = '';
		foreach( $this->value_list as $field => $value) {
			if ($fieldlist)
			$fieldlist .= ",";
			if ($valuelist)
			$valuelist .= ",";
			$fieldlist .= ' ' . trim($field) . ' ';
			$valuelist .= $value;
		}
		$q .= "($fieldlist) values ($valuelist)";
		return $q;
	}

	function prepareDelete()
	{
		$q = 'DELETE FROM ';
		if (isset($this->table_list)) {
			if (is_array($this->table_list)) {
				// Grab the first record
				list($key, $table) = each ($this->table_list);
			} else {
				$table = $this->table_list;
			}
		} else {
			return false;
		}
		$q .= ' ' . $this->_table_prefix . $table . ' ';
		$q .= $this->make_where_clause($this->where);
		return $q;
	}

	//TODO: add ALTER DROP/CHANGE/MODIFY/IMPORT/DISCARD/...
	//definitions: http://dev.mysql.com/doc/mysql/en/alter-table.html
	function prepareAlter()
	{
		$q = 'ALTER TABLE  ' . $this->_table_prefix . $this->create_table . '  ';
		if (isset($this->create_definition)) {
			if (is_array($this->create_definition)) {
				$first = true;
				foreach ($this->create_definition as $def) {
					if ($first)
					$first = false;
					else
					$q .= ', ';
					$q .= $def['action'] . ' ' . $def['type'] . ' ' . $def['spec'];
				}
			} else {
				$q .= 'ADD ' . $this->create_definition;
			}
		}

		return $q;
	}

	/**
   * Execute the query and return a handle.  Supplants the db_exec query
   */
	function &exec($style = ADODB_FETCH_BOTH, $debug = false)
	{
		$db = $this->getCon();
		$ADODB_FETCH_MODE = $this->getCon()->fetchMode;

		if (! isset($this->_old_style))
		$this->_old_style = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = $style;
		$this->clearQuery();
		if ($q = $this->prepare()) {
			MainGama::getApp()->getDebug()->log("executing query($q)",'DBQuery');
			if ($debug) {
				// Before running the query, explain the query and return the details.
				$qid = $db->Execute('EXPLAIN ' . $q);
				if ($qid) {
					$res = array();
					while ($row = $this->fetchRow()) {
						$res[] = $row;
					}
					MainGama::getApp()->getDebug()->log( "QUERY DEBUG: " . var_export($res, true),'DBQuery');
					$qid->Close();
				}
			}
			if (isset($this->limit)) {
				$this->_query_id = $db->SelectLimit($q, $this->limit, $this->offset);
			} else {
				$this->_query_id =  $db->Execute($q);
			}
			if (! $this->_query_id) {
				$error = $db->ErrorMsg();
				MainGama::getApp()->getDebug()->log("query failed($q) - error was: " . $error,'DBQuery');
				return $this->_query_id;
			}
			return $this->_query_id;
		} else {
			return $this->_query_id;
		}
	}

	function fetchRow()
	{
		if (! $this->_query_id) {
			return false;
		}
		return $this->_query_id->FetchRow();
	}

	/**
	 * loadList - replaces dbLoadList on
	 */
	function loadList($maxrows = null)
	{
		$db = $this->getCon();
		//		global $AppUI;

		if (! $this->exec(ADODB_FETCH_ASSOC)) {
			//			$AppUI->setMsg($db->ErrorMsg(), UI_MSG_ERROR);
			$this->clear();
			throw new SysException($db->ErrorMsg(),999);
			//			return false;
		}

		$list = array();
		$cnt = 0;
		while ($hash = $this->fetchRow()) {
			$list[] = $hash;
			if ($maxrows && $maxrows == $cnt++)
			break;
		}
		$this->clear();
		return $list;
	}

	function loadHashList($index = null) {
		$db = $this->getCon();

		if (! $this->exec(ADODB_FETCH_ASSOC)) {
			exit ($db->ErrorMsg());
		}
		$hashlist = array();
		$keys = null;
		while ($hash = $this->fetchRow()) {
			if ($index) {
				$hashlist[$hash[$index]] = $hash;
			} else {
				// If we are using fetch mode of ASSOC, then we don't
				// have an array index we can use, so we need to get one
				if (! $keys)
				$keys = array_keys($hash);
				$hashlist[$hash[$keys[0]]] = $hash[$keys[1]];
			}
		}
		$this->clear();
		return $hashlist;
	}

	function loadHash()
	{
		$db = $this->getCon();
		if (! $this->exec(ADODB_FETCH_ASSOC)) {
			exit ($this->db->ErrorMsg());
		}
		$hash = $this->fetchRow();
		$this->clear();
		return $hash;
	}

	function loadArrayList($index = 0) {
		$db = $this->getCon();

		if (! $this->exec(ADODB_FETCH_NUM)) {
			exit ($db->ErrorMsg());
		}
		$hashlist = array();
		$keys = null;
		while ($hash = $this->fetchRow()) {
			$hashlist[$hash[$index]] = $hash;
		}
		$this->clear();
		return $hashlist;
	}

	function loadColumn() {
		$db = $this->getCon();
		if (! $this->exec(ADODB_FETCH_NUM)) {
			die ($db->ErrorMsg());
		}
		$result = array();
		while ($row = $this->fetchRow()) {
			$result[] = $row[0];
		}
		$this->clear();
		return $result;
	}

	function loadObject( &$object, $bindAll=false , $strip = true) {
		if (! $this->exec(ADODB_FETCH_NUM)) {
			die ($this->_db->ErrorMsg());
		}
		if ($object != null) {
			$hash = $this->fetchRow();
			$this->clear();
			if( !$hash ) {
				return false;
			}
			$this->bindHashToObject( $hash, $object, null, $strip, $bindAll );
			return true;
		} else {
			if ($object = $this->_query_id->FetchNextObject(false)) {
				$this->clear();
				return true;
			} else {
				$object = null;
				return false;
			}
		}
	}

	/**
	 * Using an XML string, build or update a table.
	 */
	function execXML($xml, $mode = 'REPLACE') {
		$db = $this->getCon();
		//		global $AppUI;

		include_once './lib/adodb/adodb-xmlschema.inc.php';
		$schema = new adoSchema($db);
		$schema->setUpgradeMode($mode);
		if (isset($this->_table_prefix) && $this->_table_prefix) {
			$schema->setPrefix($this->_table_prefix, false);
		}
		$schema->ContinueOnError(true);
		if (($sql = $scheme->ParseSchemaString($xml)) == false) {
			//			$AppUI->setMsg(array('Error in XML Schema', 'Error', $db->ErrorMsg()), UI_MSG_ERR);
			throw new SysException($db->ErrorMsg(),999);
			//			return false;
		}
		if ($schema->ExecuteSchema($sql, true))
		return true;
		else
		return false;
	}



	/** {{{2 function make_where_clause
   * Create a where clause based upon supplied field.
   *
   * @param	mixed	$clause	Either string or array of subclauses.
   * @return	string
   */
	function make_where_clause($where_clause)
	{
		$result = '';
		if (! isset($where_clause))
		return $result;
		if (is_array($where_clause)) {
			if (count($where_clause)) {
				$started = false;
				$result = ' WHERE ' . implode(' AND ', $where_clause);
			}
		} else if (strlen($where_clause) > 0) {
			$result = " where $where_clause";
		}
		return $result;
	}
	//2}}}

	/** {{{2 function make_order_clause
   * Create an order by clause based upon supplied field.
   *
   * @param	mixed	$clause	Either string or array of subclauses.
   * @return	string
   */
	function make_order_clause($order_clause)
	{
		$result = "";
		if (! isset($order_clause))
		return $result;

		if (is_array($order_clause)) {
			$started = false;
			$result = ' ORDER BY ' . implode(',', $order_clause);
		} else if (strlen($order_clause) > 0) {
			$result = " ORDER BY $order_clause";
		}
		return $result;
	}
	//2}}}

	//{{{2 function make_group_clause
	function make_group_clause($group_clause)
	{
		$result = "";
		if (! isset($group_clause))
		return $result;

		if (is_array($group_clause)) {
			$started = false;
			$result = ' GROUP BY ' . implode(',', $group_clause);
		} else if (strlen($group_clause) > 0) {
			$result = " GROUP BY $group_clause";
		}
		return $result;
	}
	//2}}}

	//{{{2 function make_join
	function make_join($join_clause)
	{
		$result = "";
		if (! isset($join_clause))
		return $result;
		if (is_array($join_clause)) {
			foreach ($join_clause as $join) {
				$result .= ' ' . strtoupper($join['type']) . ' JOIN  ' . $this->_table_prefix . $join['table'] . ' ';
				if ($join['alias'])
				$result .= ' AS ' . $join['alias'];
				if (is_array($join['condition'])) {
					$result .= ' USING (' . implode(',', $join['condition']) . ')';
				} else {
					$result .= ' ON ' . $join['condition'];
				}
			}
		} else {
			$result .= ' LEFT JOIN  ' . $this->_table_prefix . $join_clause . ' ';
		}
		return $result;
	}
	//2}}}

	function foundRows()
	{
		$db = $this->getCon();
		$result = false;
		if ($this->include_count) {
			if ($qid = $db->Execute('SELECT FOUND_ROWS() as rc')) {
				$data = $qid->FetchRow();
				$result = isset($data['rc']) ? $data['rc'] : $data[0];
			}
		}
		return $result;
	}

	function quote($string)
	{
		$db = $this->getCon();
		return $db->qstr($string, get_magic_quotes_runtime());
	}
}


/**
 * (1) - Retirei os nomes das tabelas de entre os parênteses, por estar apresentando
 * um erro com o postgres.
 *
 * (2) - não sei ao certo o que é, mas não deve ser padrão ANSI...
 */

/*

//		$vo = new VOGenerico(array('id','nome','username','status'));
//		$vo = new VOGenerico(array('usu_id','usu_nome'));




		$q = new DBQuery($this->getApp());
		$q->addTable('tb_sys_usuario');
//		$q->addTable('tb_sys_permissao_usuario');
//		$q->addQuery('usu_nivel');

		$q->addWhere("usu_nivel > 0");
		$q->addOrder('usu_nome');



		// @todo Criar uma espécie de dicionário de definições de mapeamento onde eu
		// possa cadastrar a relação entre a coluna da tabela com o atributo...
//		$q->setMapaVO(array('usu_id','usu_nome','usu_username','usu_status_registro'));
		$q->setVO($vo);

		echo $q->prepare();

		echo '<pre><hr>';
//		print_r($q->loadResult());

//		$res = $q->exec();

		print_r($q->getListaVOs());
		exit;



//		print_r($res-);

//
//
//		$lista = $q->exec();
//
//		$item = $lista[0];
//		$item->getNome();
*/


?>