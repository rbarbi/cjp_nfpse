<?php

class TrataErroBancoDados_mssql {



	/**
	 * Processa e obt�m os par�metros de erro dada a exce��o
	 *
	 * @param SysException $e
	 */
	public function getParametros(&$e,$acao='insert') {
//		die($acao);
		if (is_a($e,'SysException')) {
			switch ($acao) {
				case 'delete':
					$e->setParm('msg','Erro na exclus�o do registro (vide detalhes)');
					$e->setParm('codigo',SYSErroBancoDados::ERRO_DELETE_DESCONHECIDO);
					$codigo = 1;
					switch ($e->getCode()) {
						case '-1':
							if (strpos($e->getMessage(),'violates foreign key constraint')>0){
	//							if (strpos($excecao->getMessage(),'tb_equipamento_instalado_veiculo')>0) {
									$e->setParm('msg','O registro n�o pode ser exclu�do pois tem outro a ele associado');
									$e->setParm('codigo',SYSErroBancoDados::ERRO_DELETE_REGISTRO_ASSOCIADO );
	//							}
							}
							break;
					}
	//				$se = new SysException($msg,$codigo);
	//				$se->setDescricao($excecao->getMessage());
	//				$excecao = $se;
					break;
					case 'insert':
							if (strpos($e->getMessage(), 'violates unique constraint')>0) {
								$e->setParm('msg','Alguma restricao de chave unica foi violada');
								$e->setParm('codigo',SYSErroBancoDados::ERRO_INSERT_UNIQUE );
							} else if (strpos($e->getMessage(),"violates foreign key constraint") > 0) {
								$e->setParm('msg','Alguma restricao de chave dependente voi violada');
								$e->setParm('codigo',SYSErroBancoDados::ERRO_INSERT_DEPENDENTE );
							} else {
								$e->setParm('msg','Erro na inclus�o do registro (vide detalhes)');
								$e->setParm('codigo',SYSErroBancoDados::ERRO_INSERT_DESCONHECIDO );
							}
							break;
					case 'update':
							if (strpos($e->getMessage(), 'violates unique constraint')>0) {
								$e->setParm('msg','Alguma restricao de chave unica foi violada');
								$e->setParm('codigo',SYSErroBancoDados::ERRO_UPDATE_UNIQUE );
							} else {
								$e->setParm('msg','Erro na altera��o do registro (vide detalhes)');
								$e->setParm('codigo',SYSErroBancoDados::ERRO_UPDATE_DESCONHECIDO);
							}
							break;
			}
		}


	} // eof getParametros


}


?>