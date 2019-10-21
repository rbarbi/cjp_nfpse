<?php
/*
FAZER AQUI UM AR COM A DATA/HORA DO EVENTO, USERNAME DO USUÁRIO e ID DELE, 
CLASSE / LABEL EM QUE OCORREU, IP, E A AÇÃO QUE FOI REALIZADA (ALIAS E DETALHADA)
*/
class SysRegistroAuditoriaAR extends BaseAR  {
	

	 /**
	  * @var mixed ra_id 
	  */
	 public $ra_id;

	 /**
	  * @var mixed ra_usu_id 
	  */
	 public $ra_usu_id;

	 /**
	  * @var mixed ra_dh_evento 
	  */
	 public $ra_dh_evento;

	 /**
	  * @var mixed ra_username 
	  */
	 public $ra_username;

	 /**
	  * @var mixed ra_nome_classe 
	  */
	 public $ra_nome_classe;

	 /**
	  * @var mixed ra_acao 
	  */
	 public $ra_acao;

	 /**
	  * @var mixed ra_observacoes 
	  */
	 public $ra_observacoes;

	 /**
	  * @var mixed ra_ip 
	  */
	 public $ra_ip;


	 /**
	  * @var mixed ra_obj_id 
	  */
	 public $ra_obj_id;
	 

	 /**
	  * Usado para validar o conteúdo do registro de auditoria.
	  * @var mixed ra_hash 
	  */
	 public $ra_hash;

	  
//--------------------------------------------

	/**
	 * Retorna o valor de ra_id
	 * @return mixed
	 */
	public function getID () {
		return $this->ra_id;
	} // eof getID 

	
	/**
	 * Retorna o valor de ra_usu_id
	 * @return mixed
	 */
	public function getUserID () {
		return $this->ra_usu_id;
	} // eof getUserID 

	
	/**
	 * Retorna o valor de ra_dh_evento
	 * @return mixed
	 */
	public function getDhEvento () {
		return $this->ra_dh_evento;
	} // eof getDhEvento 

	
	/**
	 * Retorna o valor de ra_username
	 * @return mixed
	 */
	public function getUsername () {
		return $this->ra_username;
	} // eof getUsername 

	
	/**
	 * Retorna o valor de ra_nome_classe
	 * @return mixed
	 */
	public function getClasse () {
		return $this->ra_nome_classe;
	} // eof getClasse 

	
	/**
	 * Retorna o valor de ra_acao
	 * @return mixed
	 */
	public function getAcao () {
		return $this->ra_acao;
	} // eof getAcao 

	
	/**
	 * Retorna o valor de ra_observacoes
	 * @return mixed
	 */
	public function getObservacoes () {
		return $this->ra_observacoes;
	} // eof getObservacoes 

	
	/**
	 * Retorna o valor de ra_ip
	 * @return mixed
	 */
	public function getIP () {
		return $this->ra_ip;
	} // eof getIp 


	/**
	 * Retorna o valor de ra_obj_id
	 * @return mixed
	 */
	public function getObjetoID () {
		return $this->ra_obj_id;
	} // eof getObjetoID 


	
	/**
	 * Retorna o valor de ra_hash
	 * @return mixed
	 */
	public function getHash () {
		return $this->ra_hash;
	} // eof getHash 

	
	
//--------------------------------------------


	/**
	 * Define o valor de ra_id
	 * @param mixed $ra_id
	 */
	public function setID ($ra_id) {
		$this->ra_id = $ra_id;
	} // eof setID 

	
	/**
	 * Define o valor de ra_usu_id
	 * @param mixed $ra_usu_id
	 */
	public function setUserID ($ra_usu_id) {
		$this->ra_usu_id = $ra_usu_id;
	} // eof setUserID 

	
	/**
	 * Define o valor de ra_dh_evento
	 * @param mixed $ra_dh_evento
	 */
	public function setDhEvento ($ra_dh_evento) {
		$this->ra_dh_evento = $ra_dh_evento;
	} // eof setDhEvento 

	
	/**
	 * Define o valor de ra_username
	 * @param mixed $ra_username
	 */
	public function setUsername ($ra_username) {
		$this->ra_username = $ra_username;
	} // eof setUsername 

	
	/**
	 * Define o valor de ra_nome_classe
	 * @param mixed $ra_nome_classe
	 */
	public function setClasse ($ra_nome_classe) {
		$this->ra_nome_classe = $ra_nome_classe;
	} // eof setClasse 

	
	/**
	 * Define o valor de ra_acao
	 * @param mixed $ra_acao
	 */
	public function setAcao ($ra_acao) {
		$this->ra_acao = $ra_acao;
	} // eof setAcao 

	
	/**
	 * Define o valor de ra_observacoes
	 * @param mixed $ra_observacoes
	 */
	public function setObservacoes ($ra_observacoes) {
		$this->ra_observacoes = $ra_observacoes;
	} // eof setObservacoes 

	
	/**
	 * Define o valor de ra_ip
	 * @param mixed $ra_ip
	 */
	public function setIP ($ra_ip) {
		$this->ra_ip = $ra_ip;
	} // eof setIp 

	
	/**
	 * Define o valor de ra_obj_id
	 * @param mixed $ra_obj_id
	 */
	public function setObjetoID ($ra_obj_id) {
		$this->ra_obj_id = $ra_obj_id;
	} // eof setObjetoID 

	
	/**
	 * Define o valor de ra_hash
	 * @param mixed $ra_hash
	 */
	public function setHash ($ra_hash) {
		$this->ra_hash = $ra_hash;
	} // eof setHash 


	
	
	function SysRegistroAuditoriaAR() {
		BaseAR::init('tb_sys_registro_auditoria',array('ra_id'));
		$this->setOID('ra_id','tb_sys_registro_auditoria_ra_id_seq');
	} // eof SysRegistroAuditoriaAR
	
	
	function defHash($retornar=false) {
		$hash = md5($this->_tostring());
		if ($retornar) {
			return $hash;
		} else {
			$this->setHash($hash);
		}
	}
	
	
} // eoc SysRegistroAuditoriaAR


?>