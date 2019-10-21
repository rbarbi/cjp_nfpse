<?php // $Rev: 563 $ - $Author: eduluz $ $Date: 2011-06-27 13:49:49 -0300 (seg, 27 jun 2011) $

/**
 * Classe que representa uma exceção ocorrida em tempo de
 * execução, agrupando dados que facilitam a identificação
 * e correção das causas do mesmo.
 *
 * @author Eduardo S. da Luz
 * @created 2008-06-09
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.utils.exception
 */
class SysException extends Exception {

	protected $m;
	protected $u;
	protected $a;
	protected $acao;
	protected $dhEvento;
	protected $username;
//	protected $metodo;
//	protected $classe;
	protected $descricao;
	protected $parms;


	 /**
	  * @var mixed id_log
	  */
	 protected $idLog;

//--------------------------------------------

	/**
	 * Retorna o valor de id_log
	 * @return mixed
	 */
	public function getID () {
		return $this->idLog;
	} // eof getID



//--------------------------------------------

	/**
	 * Define o valor de id_log
	 * @param mixed $id_log
	 */
	public function setID ($idLog) {
		$this->idLog = $idLog;
	} // eof setID



	/**
	 * Construtor
	 *
	 * @param string $msg
	 * @param int $cod
	 * @return SysException
	 */
	function __construct($msg,$cod) {
		$this->setDhEvento(date('d/m/Y H:i:s'));
		$this->setM(MainGama::getApp()->getM());
		$this->setU(MainGama::getApp()->getU());
		$this->setA(MainGama::getApp()->getA());
		$this->setAcao(MainGama::getApp()->getAcao());
		if (MainGama::getApp()->getSess()->getProfile()) {
			$this->setUsername(MainGama::getApp()->getSess()->getProfile()->getUsuario()->getUsername());
		}
		parent::__construct($msg,$cod);

		if (MainGama::getApp()->getConfig('log_erro',false)) {
			$this->registraTabelaLog();
		}
	} // eof SysException


	const ERRO_ACAO_INVALIDA 			= 1;
	const ERRO_NENHUM_ARQ_ASSOCIADO 	= 20;
	const ERRO_VARIOS_ARQ_ASSOCIADOS 	= 21;

	/**
	 * Retorna o valor de m
	 * @return string
	 */
	public function getM () {
		return $this->m;
	} // eof getM

	/**
	 * Retorna o valor de u
	 * @return string
	 */
	public function getU () {
		return $this->u;
	} // eof getU

	/**
	 * Retorna o valor de a
	 * @return string
	 */
	public function getA () {
		return $this->a;
	} // eof getA

	/**
	 * Retorna o valor de acao
	 * @return string
	 */
	public function getAcao () {
		return $this->acao;
	} // eof getAcao

	/**
	 * Retorna o valor de dhEvento
	 * @return string
	 */
	public function getDhEvento () {
		return $this->dhEvento;
	} // eof getDhEvento

	/**
	 * Retorna o valor de username
	 * @return string
	 */
	public function getUsername () {
		return $this->username;
	} // eof getUsername

	/**
	 * Define o valor de m
	 * @param string $m
	 */
	public function setM ($m) {
		$this->m = $m;
	} // eof setM

	/**
	 * Define o valor de u
	 * @param string $u
	 */
	public function setU ($u) {
		$this->u = $u;
	} // eof setU

	/**
	 * Define o valor de a
	 * @param string $a
	 */
	public function setA ($a) {
		$this->a = $a;
	} // eof setA

	/**
	 * Define o valor de acao
	 * @param string $acao
	 */
	public function setAcao ($acao) {
		$this->acao = $acao;
	} // eof setAcao

	/**
	 * Define o valor de dhEvento
	 * @param string $dhEvento
	 */
	public function setDhEvento ($dhEvento) {
		$this->dhEvento = $dhEvento;
	} // eof setDhEvento

	/**
	 * Define o valor de username
	 * @param string $username
	 */
	public function setUsername ($username) {
		$this->username = $username;
	} // eof setUsername

	public function setDescricao($txt) {
		$this->descricao = $txt;

		if ($this->getID()>0) {

			if (strcmp($this->getMessage(),$txt)!=0) {
				$descrErro = addslashes($txt);

				$sql = "UPDATE sys_log_erro SET log_descr_erro = '{$descrErro}' WHERE log_id = " . $this->getID();
				try {
					MainGama::getApp()->getCon()->Execute($sql);
				} catch (Exception $e) {
					error_log("\n\n".date('Y-m-d h:i:s').": \n".var_export($e,true)."\n-----------------\n",3,'./log/sys_error.log');
					throw new Exception('Erro do sistema - reporte ao administrador',9999);
				}
			}
		}
	} // setDescricao

	public function getDescricao() {
		return $this->descricao;
	}


	public function getEncoded() {
		$s = var_export($this,true);
		$s = base64_encode($s);
		$s = chunk_split($s);
		return $s;
	}



	public function getParm($nome=null,$default=null) {
		if (is_null($nome)) {
			return $this->parms;
		} else if (isset($this->parms[$nome])) {
			return $this->parms[$nome];
		} else {
			return $default;
		}
	} // eof getParm



	public function setParm($nome,$valor) {
		$this->parms[$nome] = $valor;
	} // eof setParm



	protected function registraTabelaLog() {

		$usuID = 0;

		if (is_object(MainGama::getApp()->getSess()->getProfile())) {
			if (is_object(MainGama::getApp()->getSess()->getProfile()->getUsuario())) {
				$usuID = MainGama::getApp()->getSess()->getProfile()->getUsuario()->getID();
			}
		}


		try {
/*
			$sql = "INSERT INTO public.sys_log_erro (
  log_usu_id,
  log_dh_erro,
  log_m,
  log_u,
  log_a,
  log_acao,
  log_parms,
  log_msg_erro,
  log_descr_erro
)
VALUES (
  '$usuID',
  now(),
  '".MainGama::getApp()->getM()."',
  '".MainGama::getApp()->getU()."',
  '".MainGama::getApp()->getA()."',
  '".MainGama::getApp()->getAcao()."',
  '".addslashes(var_export(MainGama::getApp()->getParms(),true))."',
  '".addslashes($this->getMessage())."',
  '".addslashes($this->getDescricao())."'
);";

 	MainGama::getApp()->getCon()->Execute($sql);

 	$sql = "SELECT currval('sys_log_erro_log_id_seq')";

 		echo '<pre>';
 		print_r(MainGama::getApp()->getCon()->GetArray($sql));
 		exit;

*/

				/*echo '<pre>';
				var_export(MainGama::getApp()->getCon()->hasTransactions);

					echo '<pre>';
						var_export(MainGama::getApp()->getCon()->_transOK);
						exit;

*/
			if (MainGama::getApp()->getCon()->hasTransactions) {
				MainGama::getApp()->getCon()->RollbackTrans();
			}

			$rs = new SysRegistroLogErro();
			$rs->setUsuID($usuID);
			$rs->setDhErro(date('Y-m-d H:i:s'));
			$rs->setM(MainGama::getApp()->getM());
			$rs->setU(MainGama::getApp()->getU());
			$rs->setA(MainGama::getApp()->getA());
			$rs->setAcao(MainGama::getApp()->getAcao());
			$rs->setParms(addslashes(var_export(MainGama::getApp()->getParms(),true)));
			$rs->setMsgErro(addslashes($this->getMessage()));
			$rs->setDescrErro(addslashes($this->getDescricao()));
			$rs->Save();
			$this->setID($rs->getID());

		} catch (Exception $e) {
			error_log("\n\n".date('Y-m-d H:i:s').": \n".var_export($e,true)."\n-----------------\n",3,'./log/sys_error.log');
			throw new Exception('Erro do sistema - reporte ao administrador',9999);
		}
	} // registraTabelaLog


} // eoc SysException






class SysRegistroLogErro extends ADOdb_Active_Record
{

	 /**
	  * @var mixed log_id
	  */
	 public $log_id;

	 /**
	  * @var mixed log_usu_id
	  */
	 public $log_usu_id;

	 /**
	  * @var mixed log_dh_erro
	  */
	 public $log_dh_erro;

	 /**
	  * @var mixed log_m
	  */
	 public $log_m;

	 /**
	  * @var mixed log_u
	  */
	 public $log_u;

	 /**
	  * @var mixed log_a
	  */
	 public $log_a;

	 /**
	  * @var mixed log_acao
	  */
	 public $log_acao;

	 /**
	  * @var mixed log_parms
	  */
	 public $log_parms;

	 /**
	  * @var mixed log_msg_erro
	  */
	 public $log_msg_erro;

	 /**
	  * @var mixed log_descr_erro
	  */
	 public $log_descr_erro;

//--------------------------------------------

	/**
	 * Retorna o valor de log_id
	 * @return mixed
	 */
	public function getID () {
		return $this->log_id;
	} // eof getID

	/**
	 * Retorna o valor de log_usu_id
	 * @return mixed
	 */
	public function getUsuID () {
		return $this->log_usu_id;
	} // eof getUduID

	/**
	 * Retorna o valor de log_dh_erro
	 * @return mixed
	 */
	public function getDhErro () {
		return $this->log_dh_erro;
	} // eof getDhErro

	/**
	 * Retorna o valor de log_m
	 * @return mixed
	 */
	public function getM () {
		return $this->log_m;
	} // eof getM

	/**
	 * Retorna o valor de log_u
	 * @return mixed
	 */
	public function getU () {
		return $this->log_u;
	} // eof getU

	/**
	 * Retorna o valor de log_a
	 * @return mixed
	 */
	public function getA () {
		return $this->log_a;
	} // eof getA

	/**
	 * Retorna o valor de log_acao
	 * @return mixed
	 */
	public function getAcao () {
		return $this->log_acao;
	} // eof getAcao

	/**
	 * Retorna o valor de log_parms
	 * @return mixed
	 */
	public function getParms () {
		return $this->log_parms;
	} // eof getParms

	/**
	 * Retorna o valor de log_msg_erro
	 * @return mixed
	 */
	public function getMsgErro () {
		return $this->log_msg_erro;
	} // eof getMsgErro

	/**
	 * Retorna o valor de log_descr_erro
	 * @return mixed
	 */
	public function getDescrErro () {
		return $this->log_descr_erro;
	} // eof getDescrErro



//--------------------------------------------

	/**
	 * Define o valor de log_id
	 * @param mixed $log_id
	 */
	public function setID ($log_id) {
		$this->log_id = $log_id;
	} // eof setID

	/**
	 * Define o valor de log_usu_id
	 * @param mixed $log_usu_id
	 */
	public function setUsuID ($log_usu_id) {
		$this->log_usu_id = $log_usu_id;
	} // eof setUduID

	/**
	 * Define o valor de log_dh_erro
	 * @param mixed $log_dh_erro
	 */
	public function setDhErro ($log_dh_erro) {
		$this->log_dh_erro = $log_dh_erro;
	} // eof setDhErro

	/**
	 * Define o valor de log_m
	 * @param mixed $log_m
	 */
	public function setM ($log_m) {
		$this->log_m = $log_m;
	} // eof setM

	/**
	 * Define o valor de log_u
	 * @param mixed $log_u
	 */
	public function setU ($log_u) {
		$this->log_u = $log_u;
	} // eof setU

	/**
	 * Define o valor de log_a
	 * @param mixed $log_a
	 */
	public function setA ($log_a) {
		$this->log_a = $log_a;
	} // eof setA

	/**
	 * Define o valor de log_acao
	 * @param mixed $log_acao
	 */
	public function setAcao ($log_acao) {
		$this->log_acao = $log_acao;
	} // eof setAcao

	/**
	 * Define o valor de log_parms
	 * @param mixed $log_parms
	 */
	public function setParms ($log_parms) {
		$this->log_parms = substr($log_parms,0,1020);
	} // eof setParms

	/**
	 * Define o valor de log_msg_erro
	 * @param mixed $log_msg_erro
	 */
	public function setMsgErro ($log_msg_erro) {
		$this->log_msg_erro = substr($log_msg_erro,0,1020);
	} // eof setMsgErro

	/**
	 * Define o valor de log_descr_erro
	 * @param mixed $log_descr_erro
	 */
	public function setDescrErro ($log_descr_erro) {
		$this->log_descr_erro = substr($log_descr_erro,0,1020);
	} // eof setDescrErro


	public function __construct() {
		parent::__construct('sys_log_erro',array('log_id'));
	}


}


?>