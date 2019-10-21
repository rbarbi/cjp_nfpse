<?php // $Rev: $ $Author: $ $Date: $//
/**
 * Auxilia a criacao de Associacoes NxN Simples, na qual uma tabela contem
 * apenas o id dos dois itens associados
 *
 * @author Kaléu
 */
class SimpleNxNAssociationDAO extends BaseDAO {


	 /**
	  * @var string tb
	  */
	 private $tb;

	 /**
	  * @var string att1
	  */
	 private $att1;

	 /**
	  * @var string att2
	  */
	 private $att2;

//--------------------------------------------

	/**
	 * Retorna o valor de tb
	 * @return mixed
	 */
	public function getNomeTabela () {
		return $this->tb;
	} // eof getTb

	/**
	 * Retorna o valor de att1
	 * @return mixed
	 */
	public function getAtt1 () {
		return $this->att1;
	} // eof getAtt1

	/**
	 * Retorna o valor de att2
	 * @return mixed
	 */
	public function getAtt2 () {
		return $this->att2;
	} // eof getAtt2



//--------------------------------------------

	/**
	 * Define o valor de tb
	 * @param mixed $tb
	 */
	public function setNomeTabela ($tb) {
		$this->tb = $tb;
	} // eof setTb

	/**
	 * Define o valor de att1
	 * @param mixed $att1
	 */
	public function setAtt1 ($att1) {
		$this->att1 = $att1;
	} // eof setAtt1

	/**
	 * Define o valor de att2
	 * @param mixed $att2
	 */
	public function setAtt2 ($att2) {
		$this->att2 = $att2;
	} // eof setAtt2




	/**
	 * Construtor da classe de associacao
	 *
	 * @param string $idConn
	 * @param string $nomeTabela
	 * @param string $nomeAtt1
	 * @param string $nomeAtt2
	 */
	public function __construct($idConn=false,$nomeTabela=null,$nomeAtt1=null,$nomeAtt2=null) {
		$this->setNomeTabela($nomeTabela);
		$this->setAtt1($nomeAtt1);
		$this->setAtt2($nomeAtt2);

		parent::BaseDAO($idConn);
	} // __construct



    /**
     * Insere associacao
     * @param string $val1
     * @param string $val2
     */
    public function insert($val1, $val2)
    {
        $sql = "INSERT INTO ".$this->getNomeTabela() .
        		"(" . $this->getAtt1() . "," .
        		   	  $this->getAtt2() . ") VALUES ($val1, $val2);";

        $this->getCon()->Execute($sql);
    } // insert



    /**
     * Remove Associacao
     * @param int $com_id
     * @param int $usu_id
     */
    public function remove($val1, $val2)
    {
        $sql = "DELETE FROM " . $this->getNomeTabela() . " WHERE " .  $this->getAtt1() . " = $val1 AND " . $this->getAtt2() . " = $val2;";
        $this->getCon()->Execute($sql);
    } // remove



    /**
     * Remove todas associacoes
     *
     * @param int $val
     * @param string $nomeAttr
     */
    public function removeAll($val,$nomeAttr=false) {
    	if (!$nomeAttr) {
    		$nomeAttr = $this->getAtt1();
    	}
    	$sql = "DELETE FROM " . $this->getNomeTabela() . " WHERE $nomeAttr = $val ;";
        $this->getCon()->Execute($sql);
    } // removeAll


} // SimpleNxNAssociationDAO
?>