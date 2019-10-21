<? // $Rev: 119 $ - $Author: eduluz $ $Date: 2008-09-05 18:05:00 -0300 (sex, 05 set 2008) $//

/**
 * Classe que contém os atributos necessários para a montagem da lista de 
 * grupos de usuários do sistema.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.sistema.grupo
 */
class SysGrupoVO  {


	 /**
	  * @var mixed ID 
	  */
	 private $ID;

	 /**
	  * @var mixed nome 
	  */
	 private $nome;

	 /**
	  * @var mixed descricao 
	  */
	 private $descricao;

	 /**
	  * @var mixed idUsuarioAdmin 
	  */
	 private $idUsuarioAdmin;

	 /**
	  * @var mixed status 
	  */
	 private $status;

//--------------------------------------------

	/**
	 * Retorna o valor de ID
	 * @return mixed
	 */
	public function getID () {
		return $this->ID;
	} // eof getID 

	/**
	 * Retorna o valor de nome
	 * @return mixed
	 */
	public function getNome () {
		return $this->nome;
	} // eof getNome 

	/**
	 * Retorna o valor de descricao
	 * @return mixed
	 */
	public function getDescricao () {
		return $this->descricao;
	} // eof getDescricao 

	/**
	 * Retorna o valor de idUsuarioAdmin
	 * @return mixed
	 */
	public function getIdUsuarioAdmin () {
		return $this->idUsuarioAdmin;
	} // eof getIdUsuarioAdmin 

	/**
	 * Retorna o valor de status
	 * @return mixed
	 */
	public function getStatus () {
		return $this->status;
	} // eof getStatus 



//--------------------------------------------

	/**
	 * Define o valor de ID
	 * @param mixed $ID
	 */
	public function setID ($ID) {
		$this->ID = $ID;
	} // eof setID 

	/**
	 * Define o valor de nome
	 * @param mixed $nome
	 */
	public function setNome ($nome) {
		$this->nome = $nome;
	} // eof setNome 

	/**
	 * Define o valor de descricao
	 * @param mixed $descricao
	 */
	public function setDescricao ($descricao) {
		$this->descricao = $descricao;
	} // eof setDescricao 

	/**
	 * Define o valor de idUsuarioAdmin
	 * @param mixed $idUsuarioAdmin
	 */
	public function setIdUsuarioAdmin ($idUsuarioAdmin) {
		$this->idUsuarioAdmin = $idUsuarioAdmin;
	} // eof setIdUsuarioAdmin 

	/**
	 * Define o valor de status
	 * @param mixed $status
	 */
	public function setStatus ($status) {
		$this->status = $status;
	} // eof setStatus 


} // eoc SysGrupoVO

?>