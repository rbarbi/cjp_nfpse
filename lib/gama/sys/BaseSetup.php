<?php // $Rev: 84 $ - $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $

/*
 * @author Eduardo Schmitt da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.utils.setup
 */
class BaseSetup {


	var $lsItensMenu;

	/**
	 * Referencia para a aplicacao rodando.
	 *
	 * @var MainGama
	 */
	var $app;


	/**
	 * Enter description here...
	 *
	 * @param MainGama $app
	 * @return BaseSetup
	 */
	function BaseSetup($app) {
		$this->lsItensMenu = array();
		$this->app = $app;
	}


	/**
	 * Mйtodo responsбvel pela inserзгo na base de dados dos itens de menu do mуdulo atual
	 *
	 */
	function _geraItensMenu() {
		$idItemPai = 0;
		foreach ($this->lsItensMenu as $k => $item) {

/*			if ($item->id_item_menu_pai === null) {
				$item->id_item_menu_pai = $id
			}
*/

			$item->gravar();
			$idItemAnterior = $item->id_item_menu;

		}
	}


	function _geraTabelas() {
		  $flds = "    COLNAME DECIMAL(8.4) DEFAULT 0 NOTNULL,    id I AUTO,    `MY DATE` D DEFDATE,    NAME C(32) CONSTRAINTS 'FOREIGN KEY REFERENCES reftable'  ";
		  $dict = NewDataDictionary($this->app->conn);
		  $sqlarray = $dict->CreateTableSQL("tb_teste1", $flds);
  		  $dict->ExecuteSQLArray($sqlarray);
  		  $idxflds = 'co11, col2';
  		  $sqlarray = $dict->CreateIndexSQL($idxname, $tabname, $idxflds);
  		  $dict->ExecuteSQLArray($sqlarray);

	}


	function executa() {
		$this->_geraItensMenu();
		$this->_geraTabelas();
	}



}




?>