<?php // $Rev: 374 $ $Author: eduluz $ $Date: 2009-04-15 10:34:44 -0300 (qua, 15 abr 2009) $//

/**
 * Classe que centraliza as constantes de erros de Banco de Dados.
 *
 */
class SYSErroBancoDados {


	const ERRO_INSERT_DESCONHECIDO 		= 51;
	const ERRO_INSERT_UNIQUE			= 52;
	const ERRO_INSERT_DEPENDENTE		= 53;



	const ERRO_UPDATE_DESCONHECIDO 		= 61;
	const ERRO_UPDATE_UNIQUE 			= 62;


	const ERRO_DELETE_DESCONHECIDO 			= 71;
	const ERRO_DELETE_REGISTRO_ASSOCIADO 	= 72;

	const ERRO_SYS_TABELA_AUDITORIA_INEXISTENTE = 81;


} // eoc SYSErroBancoDados


/**
 * Classe que centraliza o tratamento dos erros emitidos pelo banco de dados.
 *
 * @author Eduardo Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.base.persistencia
 */
class TrataErroBancoDadosFactory {


	/**
	 * Retorna os parâmetros de erro para uma dada exceção
	 *
	 * @param SysException $e
	 * @param string $acao
	 * @param ADORecordSet $res
	 * @param ADOConnection $con
	 */
	function getParametrosExcecao(&$e,$acao='insert',$res=null,$con=false) {
		if (!$con) {
			$con = MainGama::getApp()->getCon();
		} else if (is_string($con)) {
			$con = MainGama::getApp()->getCon($con);
		} else if (!is_object($con)) {
			echo '<pre>';
			print_r($con);
			echo '<hr>';
			debug_print_backtrace();
			die('Objeto de conexão não informado corretamente');
		}


		$nomeBD = $con->databaseType;

		$nomeClasse = "TrataErroBancoDados_" . $nomeBD;

		$tratador = new $nomeClasse();

		return $tratador->getParametros($e,$acao);

	}


} // eoc TrataErroBancoDadosFactory


?>