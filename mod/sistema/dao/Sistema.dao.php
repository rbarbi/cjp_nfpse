<?php // $Rev: 505 $ $Author: kaleu $ $Date: 2010-04-12 11:21:17 -0300 (Mon, 12 Apr 2010) $//

/**
 * Classe que realiza as operações de acesso aos dados como
 * obtenção de listagens de usuários, por exemplo.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.acesso.dao
 */
class SysDAO extends BaseDAO {


	/**
	 * Recupera e retorna uma lista de registros de usuários ativos.
	 *
	 * @return array
	 */
	function listarRegistrosUsuariosAtivos() {
		return $this->listarRegistrosUsuarios(array("usu_status = 'A' "));
	} // eof listarRegistrosAtivos


	/**
	 * Recupera e retorna uma lista de registros de usuários inativos.
	 *
	 * @return array
	 */
	function listarRegistrosUsuariosInativos() {

		return $this->listarRegistrosUsuarios(array("usu_status = 'I' "));
	} // eof listarRegistrosInativos


	/**
	 * Recupera e retorna uma lista de registros de usuários.
	 *
	 * @return array
	 */
	function listarRegistrosUsuarios($where = null) {
		$sql = "SELECT usu_id, usu_nome, usu_username FROM tb_sys_usuario ";
		if (!is_null($where)) {
			$sql .= " WHERE " . join(' AND ',$where);
		}
		$this->setVO(new VOGenerico(array('ID','nome','username')));
		$this->setLsMapeamento(array('usu_id' => 'ID','usu_nome'=>'nome','usu_username'=>'username'));
		$lista = $this->listar($sql);
		return $lista;

	} // eof listarRegistros



	// ****************************************************************
	// ****************************************************************
	// ****************************************************************


	/**
	 * Recupera uma lista de SysUsuarios de acordo com os
	 * parâmetros passados.
	 *
	 * @param string $filtroStatus
	 */
	function getListaUsuarios($numRegsPorPagina=false,$offset=false,$filtro=false) {
		if ($numRegsPorPagina) {
			if ($filtro){
				$rs = $this->getCon()->SelectLimit("SELECT * FROM tb_sys_usuario WHERE usu_status like ? ORDER BY usu_id", $numRegsPorPagina,$offset,array($filtro));
			} else{
				$rs = $this->getCon()->SelectLimit("SELECT * FROM tb_sys_usuario ORDER BY usu_id", $numRegsPorPagina,$offset);
			}
		} else {
			$rs = $this->getCon()->Execute("SELECT * FROM tb_sys_usuario ORDER BY usu_id");
		}

		$arr = array();
		while ($linha = $rs->fetchRow()){
			//			$vo = new VOGenerico(array('ID','nome','username',''))
			$vo = new SysUsuarioVO();
			$vo->setID($linha['usu_id']);
			//			$bo = new SysUsuarioBO($this->getApp());
			//			$bo->setID();
			$vo->setNome($linha['usu_nome']);
			$vo->setUsername($linha['usu_username']);
			$vo->setNivel($linha['usu_nivel']);
			$vo->setSenha($linha['usu_senha']);
			$vo->setStatus($linha['usu_status']);
			$arr[] = $vo;
		}
		return $arr;
	}// eof getListaUsuarios

	
	function getNumRegistros($filtro){
		$ar = new SysUsuarioAR();
		if (strlen($filtro) > 0){
			$rs = $ar->DB()->Execute("select count(usu_id) from tb_usuario where usu_status like ?",array($filtro));
		} else{
			$rs = $ar->DB()->Execute("select count(usu_id) from tb_usuario");
		}
		$linha = $rs->fetchRow();
		return $linha[0];
	}



	function getArrayUsuarios() {
		$lista = new ArrayAssociativo();
		$ls = $this->getListaUsuarios();
		foreach ($ls as $usuario) {
			$lista->addItem($usuario->getID(),$usuario->getNome());
		}
		return $lista;
	}




	function getUsuarioPorUsernameSenha($username,$senha) {
		$vo = new VOGenerico(array('ID','nome','username','status','nivel','senha'));

		$q = new DBQuery();
		$q->addTable('tb_sys_usuario');
		$q->addWhere("usu_username = ?");
		$q->addWhere("usu_senha = ?");
		$q->addWhere("usu_status = 'A'");
	
		$res = $this->getCon()->GetArray($q->prepare(),array($username,$senha));

		
//		echo '<pre>';
//		print_r($res);
//		exit;
		
		if (count($res) > 0) {
			$vo->setID($res[0]['usu_id']);
			$vo->setNome($res[0]['usu_nome']);
			$vo->setNivel($res[0]['usu_nivel']);
			$vo->setUsername($res[0]['usu_username']);
			$vo->setSenha($res[0]['usu_senha']);
			$vo->setStatus($res[0]['usu_status']);
			return $vo;
		} else {
			$se = new SysException('Erro - username ou senha invalidos',1);
			throw $se;

		}


	} // eof getUsuarioPorUsernameSenha
	

	
	

	/**
	 * Dado um usuário, recupera a lista de permissões de um dado usuário,
	 * unindo as permissões diretas com as de grupo.
	 *
	 * @param int $usuID
	 * @return array
	 */
	function getListaPermissoes($usuID) {
		
//		$ls = $this->getListaTransacoesPermitidasUsuario($usuID);
//		echo '<pre>';
//		print_r($ls);
//		echo '</pre>';
//		
//		if (isset($ls[39])) {
//			echo "Está permitido";
//		} else {
//			echo "Acesso negado";
//		}
//		
		$lista = $this->getListaPermissoesTransacoes($usuID);		
		return $lista;
	} // eof getListaPermissoes

/*

		$lista1 = $this->getListaTransacoesPermitidasUsuario($usuID);
		print_r($lista1);

*/

	function getListaPermissoesTransacoes($usuID) {

		$this->setLsMapeamento(
			array(	'pe_id' => 'ID',
					'tr_id' => 'transacaoID',
					'tr_nome'=>'nomeTransacao',
					'pe_permissao'=>'permissao',
					'tr_m' =>'m',
					'tr_u' => 'u', 
					'tr_a' => 'a',
					'tr_acao'=>'acao',
					'tr_permissao_default'=>'permissaoDefault',
					'tr_tr_agregadora' => 'transacaoAgregadoraID'
		));


		$vo = new VOGenerico(array_values($this->getLsMapeamento()));

		$this->setVO($vo);

		$q = new DBQuery();
		$q->addTable('tb_sys_permissao_usuario','pu');
		$q->addTable('tb_sys_permissao','p');
		$q->addTable('tb_sys_transacao','t');
		$q->addWhere('pu.pu_usu_id = ?');
		$q->addWhere('pu.pu_id = p.pe_id');
		$q->addWhere('p.pe_tr_id = t.tr_id');
		$q->addWhere('length(t.tr_nome) > 0');
//		$q->addWhere('t.tr_id in ('.join(",",$lsIDs).')');
		$q->setVO($vo);
		$q->setMapaVO(array_keys($this->getLsMapeamento()));
		$sql = $q->prepareSelect();
		
//		echo join(",",$lsIDs);
//		echo $sql; exit;

		$lista = $this->listar($sql,array($usuID));

		return $lista;
	} // eof getListaPermissoesTransacoes

	/**
	 * Recupera e retorna a lista de permissões que um dado usuário possui.
	 *
	 * @param int $usuID
	 * @return array lista de VOs com os dados das permissões
	 */
	function getListaPermissoesUsuario($usuID) {

		$this->setLsMapeamento(
			array(	'pe_id' => 'ID',
					'tr_id' => 'transacaoID',
					'tr_nome'=>'nomeTransacao',
					'pe_permissao'=>'permissao',
					'pu_usu_id' => 'usuarioID',
					'tr_m' =>'m',
					'tr_u' => 'u', 
					'tr_a' => 'a',
					'tr_acao'=>'acao',
					'tr_permissao_default'=>'permissaoDefault',
					'tr_tr_agregadora' => 'transacaoAgregadoraID'		
		));


		$vo = new VOGenerico(array_values($this->getLsMapeamento()));

		$this->setVO($vo);

		$q = new DBQuery();
		$q->addTable('tb_sys_permissao_usuario','pu');
		$q->addTable('tb_sys_permissao','p');
		$q->addTable('tb_sys_transacao','t');
		$q->addWhere('pu.pu_usu_id = ?');
		$q->addWhere('pu.pu_id = p.pe_id');
		$q->addWhere('p.pe_tr_id = t.tr_id');
		$q->addWhere('length(t.tr_nome) > 0');
		$q->setVO($vo);
		$q->setMapaVO(array_keys($this->getLsMapeamento()));
		$sql = $q->prepareSelect();

		$lista = $this->listar($sql,array($usuID));

		return $lista;
	} // eof getListaPermissoesUsuario

	
	

	/**
	 * Dado o identificador do usuário, recupera a lista de transações que o mesmo
	 * pode acessar.
	 *
	 * 1. Recuperar a lista de permissões explícitas de um usuário.
	 * 2. Obter a lista de transações ativas.
	 * 3. 
	 * 
	 * @param int $usuID
	 * @return array
	 */
//	function getListaTransacoesPermitidas_Usuario_old($usuID) {	
//	    $listaTransacoesAutorizadas = $this->getListaPermissoesUsuario($usuID);
//		$listaTransacoes = $this->getListaTodasTransacoesAtivas();
//		$lsTr = array();
//		foreach ($listaTransacoes as  $tr) {
//			$lsTr[$tr->getID()] = $tr;
//		}
//		$lsAss = array();
//		foreach ($lsTr as $tr) {
//			if (!is_null($tr->getTransacaoID())) {
//				$lsAss[$tr->getID()] = $tr->getTransacaoID();
//			}
//		}
//		$lista = array();
//		foreach ($listaTransacoesAutorizadas as $permissao) {			
//			$lista2 = $this->_processaListaTransacoes($permissao->getTransacaoID(),$lsTr,$lsAss,$permissao);
//			$lista = array_merge($lista,$lista2);
//		}
//		$listaSaida = array();
//		foreach ($lista as $tr) {
//			$listaSaida[$tr->getTransacaoID()] = $tr;
//		}
//		
//		return $listaSaida;
//	}



	/**
	 * @deprecated 
	 *
	 */
/*	function _processaListaTransacoes($trID, &$lsTr, &$lsAss,$pPermissao=false) {		
		$arr = array();
		$arrPesquisar = array();
		

//		$arr[$trID] = $lsTr[$trID]->getPermissaoDefault();
		$permissao = new SysTransacaoVO();
		$permissao->setTransacaoID($trID);
		

		
		$permissao->setPermissaoDefault($lsTr[$trID]->getPermissaoDefault());
		$permissao->setM($lsTr[$trID]->getM());
		$permissao->setU($lsTr[$trID]->getU());
		$permissao->setA($lsTr[$trID]->getA());
		$permissao->setAcao($lsTr[$trID]->getAcao());
		
		$permissao->setTransacaoAgregadoraID($lsTr[$trID]->getTransacaoID());
		
		
		if ($pPermissao) {
			$permissao->setPermissao($pPermissao->getPermissao());
		} else {			
			
//			$perm = $this->getPermissaoUsuario()
			
			$perm = new SysPermissaoBO();
			error_log("\n\t (2) ID=".$permissao->getTransacaoAgregadoraID(),3,'sql.log');
			$perm->setID($permissao->getTransacaoAgregadoraID());	
			error_log("\n\t (2a) ID=".$perm->getID(),3,'sql.log');
			$perm->load();
			error_log("\n\t (2b) ID=".$perm->getID(),3,'sql.log');
			error_log("\n\t (3) Permissao=".$perm->getPermissao(),3,'sql.log');
			$permissao->setPermissao($perm->getPermissao());

			if ($lsTr[$trID]->getAcao() == 'doCadUsuario') {
				error_log("\n\n + ". var_export($lsTr[$trID],true),3,'sql.log');
				error_log("\n\n - ". var_export($permissao,true),3,'sql.log');
			}
			
		}
		

		$arr[] = $permissao;
	
		
		$achou = false;

		foreach ($lsAss as $filho => $pai ) {
			if ($trID == $pai) {
				$achou = true;
				$arrPesquisar[$pai] = $filho;
			}
		}

		if ($achou) {
			foreach ($arrPesquisar as $pai => $id) {
				$arr = array_merge($arr,$this->_processaListaTransacoes($id,$lsTr,$lsAss));
				//print_r($arr);
			}
		}
		return $arr;
	}*/


	
	
	/**
	 * @deprecated 
	 *
	 */
	/*function getListaTodasTransacoesAtivas() {
		$q = new DBQuery();
		$q->addTable('tb_sys_transacao');
		$q->addWhere("tr_status = 'A'");
		$sql = $q->prepareSelect();
		$lista = array();
		
		$res = MainGama::getApp()->getCon()->GetArray($sql);
		foreach ($res as $registro) {
			$vo = new SysTransacaoVO();
			$vo->setID($registro['tr_id']);
		}
		
	}*/
	
	
	
//	function getListaTodasTransacoesAtivas_old() {
//		$this->setLsMapeamento(array('tr_id' => 'ID','tr_tr_agregadora' => 'transacaoID','tr_nome'=>'nomeTransacao','tr_permissao_default'=>'permissaoDefault','tr_m' =>'m','tr_u' => 'u', 'tr_a' => 'a','tr_acao'=>'acao'));
//		$vo = new VOGenerico(array_values($this->getLsMapeamento()));
//		$this->setVO($vo);
//		$q = new DBQuery();
//		$q->addTable('tb_sys_transacao');
//		$q->addWhere("tr_status = 'A'");
//		$q->setVO($vo);
//		$q->setMapaVO(array_keys($this->getLsMapeamento()));
//		$sql = $q->prepareSelect();
//		$lista = $this->listar($sql);
//		return $lista;
//	}



	/**
	 * Retorna o valor da permissão.
	 *
	 * RN.1 - Se não houver registro de permissão para o usuário em questão,
	 *        então retorna o valor de SysPermissaoBO::PERM_DEFAULT. Neste caso,
	 *        o valor da permissão da transação é que definirá se o usuário em
	 *        questão pode ou não ter acesso.
	 *
	 * @param int $usuID
	 * @param int $trID
	 * @return char
	 * 
	 * @deprecated Acho que isso não é mais necessário, mas avaliar o impacto na SysAutorizacaoBO
	 */
	function getPermissaoUsuario($usuID,$trID) {
		$q = new DBQuery();
		$q->addQuery('pe_permissao AS permissao');
		$q->addTable('tb_sys_permissao_usuario','pu');
		$q->addTable('tb_sys_permissao','p');
		$q->addTable('tb_sys_transacao','t');
		$q->addWhere('pu.pu_usu_id = ?');
		$q->addWhere('pu.pu_id = p.pe_id');
		$q->addWhere('p.pe_tr_id = t.tr_id');
		$q->addWhere('t.tr_id = ?');

		$sql = $q->prepareSelect();

		try {
			//			MainGama::getApp()->_doLogDebug("Entrando... ");
			$perm = new SysPermissaoBO();
			$res = $this->getCon()->GetArray($sql,array($usuID,$trID));
			//			MainGama::getApp()->_doLogDebug(" DEFAULT =  " . SysPermissaoBO::PERM_DEFAULT);
			//		    MainGama::getApp()->_doLogDebug("Resultado = " . count($res));
			if (count($res) == 0) {
				$transacao = new SysTransacaoBO();
				$transacao->setID($trID);
				$transacao->load();
				
				
				
				MainGama::getApp()->_doLogDebug(" DEFAULT =  " . $transacao->getPermissaoDefault());
				return $transacao->getPermissaoDefault();
			} else {
				MainGama::getApp()->_doLogDebug(" perm =  " . $res[0]['permissao']);
				return ($res[0]['permissao']);
			}
		} catch (Exception $e) {
			MainGama::getApp()->_doLogDebug('ERRO - '.$e->getMessage());
		}

		MainGama::getApp()->_doLogDebug($sql);

	} // eof getPermissaoUsuario



	/**
	 * Retorna um ArrayAssociativo de transações, para preenchimento
	 * de campos SELECT de formulários HTML.
	 *
	 * @return ArrayAssociativo
	 */
	function getArrayTransacoes() {
		$lista = new ArrayAssociativo();
		$ls = $this->listarRegistrosTransacoes(null);
		foreach ($ls as $transacao) {
			$lista->addItem($transacao->getID(),$transacao->getNome());
		}
		return $lista;
	} // eof getArrayTransacoes

	/**
	 * Recupera e retorna uma lista de registros de transações ativas.
	 *
	 * @return array
	 */
	function listarRegistrosTransacoesAtivas() {
		return $this->listarRegistrosTransacoes(array("tr_status = 'A' "));
	} // eof listarRegistrosAtivos


	/**
	 * Recupera e retorna uma lista de registros de transações inativas.
	 *
	 * @return array
	 */
	function listarRegistrosTransacoesInativas() {

		return $this->listarRegistrosTransacoes(array("tr_status = 'I' "));
	} // eof listarRegistrosInativos


	/**
	 * Recupera e retorna uma lista de registros de transações.
	 * Filtra as transações que não possuem nome.
	 *
	 * @param array $where lista de condições de filtragem.
	 * @return array
	 */
	function listarRegistrosTransacoes($where=null) {

		$this->initVO(array('tr_id' => 'ID','tr_nome'=>'nome','tr_descricao' => 'descricao'));

		$q = new DBQuery();
		$q->addTable('tb_sys_transacao');
		$q->addWhere('length(tr_nome) > 0');
		if (!is_null($where)) {
			foreach ($where as $wh) {
				$q->addWhere($wh);
			}
		}

		$q->setVO($this->getVO(),array_keys($this->getLsMapeamento()));
		$sql = $q->prepareSelect();

		$lista = $this->listar($sql);
		return $lista;
	} // eof listarRegistros




	/**
	 * Recupera o ID do objeto da Transação com base nos parâmetros 
	 * m, u, a e acao.
	 *
	 * @param string $m
	 * @param string $u
	 * @param string $a
	 * @param string $acao
	 * @return VOGenerico
	 */
	public function getTransacaoByParms($m,$u,$a,$acao) {

		$this->setLsMapeamento(array('tr_id' => 'ID','tr_nome'=>'nome','tr_m' =>'m','tr_u' => 'u', 'tr_a' => 'a','tr_acao'=>'acao','tr_permissao_default' => 'permissaoDefault', 'tr_nivel_min_usuario' => 'nivelUsuario', 'tr_tr_agregadora' => 'transacaoPaiID' ));
//		$this->setLsMapeamento(array('tr_id' => 'ID','tr_nome'=>'nome','tr_m' =>'m','tr_u' => 'u', 'tr_a' => 'a','tr_acao'=>'acao','tr_permissao_default' => 'permissaoDefault', 'tr_nivel_min_usuario' => 'nivelUsuario', 'tr_tr_agregado' => 'transacaoPaiID' ));

		$vo = new VOGenerico(array_values($this->getLsMapeamento()));

		$this->setVO($vo);

		$q = new DBQuery();

		$q->setVO($vo);
		$q->setMapaVO(array_keys($this->getLsMapeamento()));

		$q->addTable('tb_sys_transacao');
		$q->addWhere('tr_m = ?');
		if (strlen(trim($u)) > 0) {
			$q->addWhere('tr_u = ?');
		}		
		$q->addWhere('tr_a = ?');
		$q->addWhere('tr_acao = ?');

		$sql = $q->prepareSelect();
		
		
		if (strlen(trim($u)) > 0) {			
			$lista = $this->listar($sql,array($m,$u,$a,$acao));
		} else {
			$lista = $this->listar($sql,array($m,$a,$acao));
		}
		return reset($lista);
	} // eof getTransacaoByParms

	
	// *********************************************************
	// *********************************************************
	// *********************************************************

	/**
	 * Retorna um ArrayAssociativo de grupos de usuários, para preenchimento
	 * de campos SELECT de formulários HTML.
	 *
	 * @return ArrayAssociativo
	 */
	function getArrayGrupos() {
		$lista = new ArrayAssociativo();
		$ls = $this->listarRegistrosGrupos(null);
		foreach ($ls as $grupo) {
			$lista->addItem($grupo->getID(),$grupo->getNome());
		}
		return $lista;
	} // eof getArrayGrupos

	/**
	 * Recupera e retorna uma lista de registros de grupos ativos.
	 *
	 * @return array
	 */
	function listarRegistrosGruposAtivos() {
		return $this->listarRegistrosGrupos(array("gu_status_registro = 'A' "));
	} // eof listarRegistrosGruposAtivos


	/**
	 * Recupera e retorna uma lista de registros de grupos inativos.
	 *
	 * @return array
	 */
	function listarRegistrosGruposInativos() {

		return $this->listarRegistrosGrupos(array("gu_status_registro = 'I' "));
	} // eof listarRegistrosGruposInativos


	/**
	 * Recupera e retorna uma lista de registros de transações.
	 * Filtra as transações que não possuem nome.
	 *
	 * @param array $where lista de condições de filtragem.
	 * @return array
	 */
	function listarRegistrosGrupos($where=null) {

		$this->initVO(array('gu_id' => 'ID','gu_nome'=>'nome','gu_descricao' => 'descricao'));

		$q = new DBQuery();
		$q->addTable('tb_sys_grupo_usuarios');
		$q->addWhere('length(gu_nome) > 0');
		if (!is_null($where)) {
			foreach ($where as $wh) {
				$q->addWhere($wh);
			}
		}

		$q->setVO($this->getVO(),array_keys($this->getLsMapeamento()));
		$sql = $q->prepareSelect();

		$lista = $this->listar($sql);
		return $lista;
	} // eof listarRegistrosGrupos

	
	
	
	
	function getListaUsuariosGrupo($grupoID) {

		$this->setLsMapeamento(
			array(	'ug_usu_id' => 'usuarioID',
					'ug_gu_id' => 'grupoID',
					'usu_nome'=>'nomeUsuario',
					'ug_nivel'=>'nivel'
		));


		$vo = new VOGenerico(array_values($this->getLsMapeamento()));

		$this->setVO($vo);

		$q = new DBQuery();
		$q->addTable('tb_sys_usuario_grupo','ug');
		$q->addTable('tb_sys_usuario','u');
		$q->addWhere('ug.ug_gu_id = ?');
		$q->addWhere('ug.ug_usu_id = u.usu_id');
		$q->setVO($vo);
		$q->setMapaVO(array_keys($this->getLsMapeamento()));
		$sql = $q->prepareSelect();

		$lista = $this->listar($sql,array($grupoID));

		return $lista;
	} // eof getListaUsuariosGrupo

	
} // eoc SysDAO



?>