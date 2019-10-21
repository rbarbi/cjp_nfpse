<?php // $Rev: 257 $ - $Author: eduluz $ $Date: 2008-11-24 10:27:01 -0200 (Mon, 24 Nov 2008) $//

/**
 * Classe que centraliza a lѓgica de autorizaчуo dos
 * usuсrios ao acesso рs transaчѕes do sistema.
 *
 */
class SysAutorizacaoBO extends BasePersistenteBO {


	/**
	 * Verifica se o usuсrio em questуo tem a permissуo para acessar a transaчуo em questуo.
	 *
	 * Primeiro щ feita uma verificaчуo das variсveis passadas por parтmetro, assumindo os
	 * valores default em caso de nуo informaчуo de alguma chave.
	 * 
	 * Depois avalia-se se a requisiчуo jс possui uma autorizaчуo registrada na sessуo, desde
	 * o login. Se hс uma autorizaчуo explэcita, entуo deixa passar.
	 * 
	 * Se houve alguma alteraчуo na base de permissѕes, verifica.
	 * 
	 * @param int    $usuID
	 * @param string $m
	 * @param string $u
	 * @param string $a
	 * @param string $acao
	 *
	 * @return boolean true se estiver autorizado
	 *
	 * @throws SysException  5 = transacao inexixtente
	 * @throws SysException 10 = o usuсrio estс explicitamente desautorizado
	 * @throws SysException 15 = o usuсrio estс desautorizado pelo default da transacao
	 */
	function estaAutorizado($usuID,$m=false,$u=null,$a=false,$acao=false) {

		// 1a Fase - verificar variсveis vindas como Request.

		if (!$m) { $m = $this->getApp()->getM(); }

		//		if (!$u) { $u = $this->getApp()->getU(); }

		if (!$a) { $a = $this->getApp()->getA(); }
		if (!$acao) { $acao = $this->getApp()->getAcao(); }

		// 2a Fase - avaliar se a requisiчуo jс se encontra autorizada desde o login
		$trans = new SysTransacaoBO();
		$alias = $trans->formataAlias(array($m,$u,$a,$acao));
//		$alias = SysTransacaoBO::formataAlias(array($m,$u,$a,$acao));
		$lista = $this->getApp()->getSess()->getProfile()->getTransacoesPermitidas();

		//		error_log(var_export($lista,true),3,'sql.log');

		foreach ($lista as $tr) {
			if ($alias == $tr->getAlias()) {
				if ($tr->getPermissaoDefault() == 'S') {
					return true;
				} else {
					//					error_log("\n(1a) = [" . $tr->getPermissao() . "] \n" ,3,'sql.log');
					break;
				}
			}
		}


		// 3a Fase - verificar se houve alguma alteraчуo nas permissѕes.
		$dao = new SysDAO();

		try {
			$tr = $dao->getTransacaoByParms($m,$u,$a,$acao);
			if (!$tr) {
				$se = new SysException("Transacao nao existente $alias ",5);
				throw $se;
			}

		} catch (SysException $e) {
			throw $e;
		}


		$trID = $tr->getID();
		$permissao = $dao->getPermissaoUsuario($usuID,$trID);

		if ($permissao == 'N') {
			$se = new SysException('Usuario explicitamente desautorizado',10);
			$se->setDescricao("Existe uma permissуo que desautoriza explicitamente este usuсrio");
			throw $se;
		} else if ($permissao == 'D') {
			$transacao = new SysTransacaoBO();
			$transacao->setID($tr->getTransacaoPaiID());
			$transacao->load();

			if ($transacao->getPermissaoDefault() != 'S') {
				//			if ($tr->getPermissaoDefault() != 'S') {
				$se = new SysException('Usuario nao autorizado pela transacao',15);
				throw $se;
			}
		}
		return true;
	} // eof estaAutorizado





	/**
	 * Recupera a lista de transaчѕes que sуo permitidas para o usuсrio em questуo.
	 *
	 * @param SysUsuarioBO $usuario
	 * @return array
	 */
	function getListaTransacoesPermitidasUsuario($usuario) {
		$usuID = $usuario->getID();
		//		$usuID = 41;
		$boT = new SysTransacaoBO();
		$lsTransacoes = $boT->getListaTodasTransacoesAtivas();

		$boP = new SysPermissaoBO();
		$lsPermissoes = $boP->getListaTodasPermissoesUsuario($usuID);

		$lista = SysAutorizacaoBO::determinaTransacoesAcessiveisUsuario($lsTransacoes,$lsPermissoes);

		return $lista;
	}



	/**
	 * Algoritmo:
	 * 		1) passo 1 = Extrair da lista de transaчѕes apenas as que sуo abertas por natureza;
	 * 					 Obter a lista de transaчѕes agregadas a lista obtida.
	 * 		2) passo 2 = Obter a lista de transaчѕes que possuem permissуo de abertura.
	 * 					 Obter a lista de transaчѕes agregadas a lista obtida.
	 * 		3) passo 3 = Recuperar a lista de transaчѕes que possuem permissѕes explicitamente negadas.
	 * 					 Obter a lista de transaчѕes agregadas a lista obtida.
	 * 		4) passo 4 = Realizar a intersecчуo dos conjuntos resultantes dos passos 1 e 2, e 
	 * 					 extrair deles os itens existentes no conjunto do passo 3.
	 * 
	 * @param array $lsTransacoes
	 * @param array $lsPermissoes
	 * @return array
	 */
	protected function determinaTransacoesAcessiveisUsuario($lsTransacoes,$lsPermissoes) {
		$res1 = SysAutorizacaoBO::passo1($lsTransacoes,$lsPermissoes);
		$res2 = SysAutorizacaoBO::passo2($lsTransacoes,$lsPermissoes);
		$res3 = SysAutorizacaoBO::passo3($lsTransacoes,$lsPermissoes);

		$res4 = SysAutorizacaoBO::passo4($res1,$res2,$res3);
		return $res4;
	}



	function simplifica($ls) {
		$r = array();
		foreach ($ls as $tr) {
			$r[] = $tr->getID();
		}
		return $r;
	}


	function passo1($lsTransacoes,$lsPermissoes) {
		$res = array();
		foreach ($lsTransacoes as $tr) {
			//			if ($tr->getPermissao() == 'S') {
			if ($tr->getPermissaoDefault() == 'S') {
				$res[] = $tr;
			}
		}

		return SysAutorizacaoBO::agrega($lsTransacoes,$lsPermissoes,$res);
	}



	function passo2($lsTransacoes,$lsPermissoes) {
		$res = array();
//		error_log("\n\n\n===============================================================",3,'perm.log');
//		error_log("\nIniciando a verificacao:",3,'perm.log');
		foreach ($lsPermissoes as $pe) {
//			error_log("\n\tAvaliando:" . $pe->getID() . " tr=".$pe->getTransacao()->getID() . " perm=".$pe->getPermissao(),3,'perm.log');
			if ($pe->getPermissao() == 'S') {
//				error_log("\n\t Permissao concedida",3,'perm.log');
				//			$pe->getTransacao()->setPermissao('S');				
				$res[$pe->getTransacao()->getID()] = $lsTransacoes[$pe->getTransacao()->getID()];
				$res[$pe->getTransacao()->getID()]->setPermissaoDefault($pe->getPermissao());
			} else {
//				error_log("\n\t Permissao negada",3,'perm.log');
			}
		}
//		error_log("\n\t Agregando...",3,'perm.log');
		$resposta = SysAutorizacaoBO::agrega($lsTransacoes,$lsPermissoes,$res);
//		error_log("\n---------------------------------------------------------\n\n",3,'perm.log');
		return $resposta;
	}




	function passo3($lsTransacoes,$lsPermissoes) {
		$res = array();
		foreach ($lsPermissoes as $pe) {
			if ($pe->getPermissao() == 'N') {
				$res[] = $pe->getTransacao();
			}
		}
		return SysAutorizacaoBO::agrega($lsTransacoes,$lsPermissoes,$res);
	}


	function passo4($ls1,$ls2,$ls3) {
		$res = array();

		//	print_r($ls1); exit;

		foreach ($ls1 as $tr) {
			$res[$tr->getID()] = $tr;
		}
		foreach ($ls2 as $tr) {
			if (!isset($res[$tr->getID()])) {
				$res[$tr->getID()] = $tr;
			}
		}
		foreach ($ls3 as $tr) {
			if (isset($res[$tr->getID()])) {
				unset($res[$tr->getID()]);
			}
		}

		return $res;
	}


	function agrega($lsTransacoes,$lsPermissoes,$res) {
		$res2 = array();
//		error_log("\n\n",3,'perm.log');

		foreach ($res as $tr) {
			$res2[] = $tr;
//			error_log("\n\t\t Verificando a transacao ".$tr->getID() . "  ( " . $tr->getAcao() . " ) ",3,'perm.log');
			foreach ($lsTransacoes as $trAux) {
				if ($tr->getID() == $trAux->getTransacaoAgregadora()->getID()) {
//					error_log("\n\t\t\t Agregando a transacao ".$trAux->getID() . "   " . $trAux->getAcao(),3,'perm.log');
					$trAux->setPermissaoDefault($tr->getPermissaoDefault());
					$res2[] = $trAux;
				}
			}
		}
		return $res2;
	}




} // eoc SysAutorizacaoBO



?>