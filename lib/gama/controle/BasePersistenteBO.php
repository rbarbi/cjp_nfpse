<?php // $Rev: 536 $ - $Author: eduluz $ $Date: 2010-08-11 18:14:11 -0300 (qua, 11 ago 2010) $

/**
 * Classe que gerencia as operações de persistência do BO.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.base.controle
 */
class BasePersistenteBO extends BaseBO {



	const AUDITA_INSERT = 1;
	const AUDITA_UPDATE = 2;
	const AUDITA_DELETE = 4;
	const AUDITA_LOAD   = 8;



	/**
	 * Flag indicativa de que o objeto foi carregado ou não.
	 *
	 * @var boolean
	 */
	protected $_isCarregado = false;



	protected $__auditoriaAtiva = false;
	protected $__aud_labelObjeto = 'n/d';





	/**
	 * Valores de status de registros.
	 */
	const ST_REG_ATIVO   = 'A';
	const ST_REG_INATIVO = 'I';
	const ST_REG_AMBOS   = '=';


	const ERRO_CAMPO_REQUERIDO = -1;
	const ERRO_CAMPO_DUPLICADO = -5;




	/**
	 * Verifica se o objeto está carregado ou não.
	 *
	 * @param BaseAR $ar
	 */
	protected function _checkCarregado($ar=null) {
		if (!$this->_isCarregado) {
			if (is_null($ar)) {
				$ar = $this->getAR();
			}
			$this->load($ar);
			$this->_isCarregado = true;
		}
	} // eof _checkCarregado


	/**
	 * Este método é usado para retornar uma string que será
	 * usada na apresentação do nome de um objeto.
	 */
	function getLabel() {
		die('getLabel nao implementado na classe ' . get_class($this));
	} // eof getLabel


	/**
	 * Retorna o identificador do objeto.
	 */
	function getID() {
		die('getId nao implementado na classe ' . get_class($this));
	} // eof getID


	/**
	 * Executa uma lista de instruções passadas por parâmetro.
	 * Cada item do array é um método deste objeto.
	 *
	 * @param array $arr
	 */
	function executar($arr) {
		try {
			foreach ($arr as $item) {
				$this->$item();
			}
		} catch (Exception $e) {
			die($e->getMessage());
		}
	} // eof executar


	/**
	 * Método que retorna um AR deste BO, apenas com o ID
	 * setado. Deve ser implementado nas classes-filhas.
	 *
	 * @return BaseAR
	 */
	protected function getAR() {
		throw new SysException("Metodo getAR nao implementado",1);
	} // eof getAR




	// /////////////////////////////////////////////////////
	// ///  INSERT  ////////////////////////////////////////
	// /////////////////////////////////////////////////////

	/**
	 * Insere um registro no banco de dados.
	 *
	 * @param BaseAR $ar
	 * @throws SysException
	 */
	function insert(BaseAR $ar=null) {
		if (is_null($ar)) {
			$ar = $this->getAR();
			$ar->setID(null);
		} else if (!is_a($ar,'BaseAR')) {
			throw new SysException("Classe informada não é AR",1);
		}

		try {
			$this->checkInsert();
			$this->preInsert($ar);
			$res = $this->execInsert($ar);
			$this->posInsert($ar);
			return $res;
		} catch (Exception $e) {
			if (is_a($e,'SysException')) {
				$e->setParm('ar',$ar);
			}
			$this->_trataExcecao($e,'insert');
			throw $e;
		}

	} // eof insert


	/**
	 * Verifica se os campos requeridos estão preenchidos ou
	 * não. Se algum não estiver, lança uma exception.
	 */
	function checkInsert() {
		$this->checkFields();
	} // eof checkInsert


	/**
	 * Verifica se os campos requeridos estão preenchidos ou
	 * não. Se algum não estiver, lança uma exception.
	 */
	function checkUpdate() {
		$this->checkFields();
	} // eof checkInsert




	/**
	 * Realiza alguma atividade antes de proceder
	 * com a inclusão do registro.
	 *
	 * @param BaseAR $ar
	 */
	function preInsert(&$ar) { } // eof preInsert


	/**
	 * Executa a inclusão do registro.
	 *
	 * @param BaseAR $ar
	 * @throws SysException
	 */
	function execInsert(&$ar) {
		try {
			$ar->insert();
			if (!is_null($ar->getOID())) {
				$this->setID($ar->LastInsertID());
			}
			$this->__audita($ar,'insert');
		} catch (Exception $e) {
			$se = new SysException($e->getMessage(),$e->getCode());
			throw $se;
			/*
			switch ($e->getCode()) {
			case BasePersistenteBO::ERRO_CAMPO_REQUERIDO:
			$mensagem = 'Campo requerido nao informado';
			break;
			case BasePersistenteBO::ERRO_CAMPO_DUPLICADO :
			$mensagem = 'Campo duplicado';
			break;
			default:
			$mensagem = 'Erro desconhecido na inclusao do registro - ';
			}
			$se = new SysException($mensagem,$e->getCode());
			$se->setDescricao($e->getMessage());
			throw $se;*/
		}
	} // eof execInsert

	/**
	 * Realiza algum procedimento após a inclusão
	 * do registro.
	 *
	 * @param BaseAR $ar
	 */
	function posInsert(&$ar) {} // eof posInsert


	// /////////////////////////////////////////////////////
	// ///  UPDATE  ////////////////////////////////////////
	// /////////////////////////////////////////////////////


	/**
	 * Método que realiza a atualização de um
	 * registro na base de dados.
	 *
	 * @param BaseAR $ar
	 */
	function update(BaseAR $ar=null) {
		if (is_null($ar)) {
			$ar = $this->getAR();
		} else if (!is_a($ar,'BaseAR')) {
			throw new SysException("Classe informada não é AR",1);
		}
		try {
			$this->checkUpdate();
			$this->preUpdate($ar);
			$this->execUpdate($ar);
			$this->posUpdate($ar);
		} catch (Exception $e) {
			if (is_a($e,'SysException')) {
				$e->setParm('ar',$ar);
			}
			$this->_trataExcecao($e,'update');
			throw $e;
		}
	} // eof update


	/**
	 * Executa algum processamento antes da
	 * atualização do registro no banco.
	 *
	 * @param BaseAR $ar
	 */
	function preUpdate(BaseAR &$ar) { } // eof preUpdate


	/**
	 * Executa a atualização do registro no
	 * banco de dados.
	 *
	 * @param BaseAR $ar
	 * @throws SysException
	 */
	function execUpdate(BaseAR &$ar) {
		try {
			$sql = $ar->montaSQLUpdate();
			$ar->getDB()->Execute($sql);
			$this->__audita($ar,'update');
		} catch (Exception $e) {
			$se = new SysException($e->getMessage(),$e->getCode());
			throw $se;
		}
	} // eof execUpdate

	/**
	 * Realiza algum procedimento após a
	 * atualização do registro no banco.
	 *
	 * @param BaseAR $ar
	 */
	function posUpdate(BaseAR &$ar) {} // eof posUpdate


	// /////////////////////////////////////////////////////
	// ///  DELETE  ////////////////////////////////////////
	// /////////////////////////////////////////////////////



	/**
	 * Exclui um registro na base de dados.
	 *
	 * @param BaseAR $ar
	 * @throws SysException
	 */
	function delete(BaseAR $ar = null) {
		if (is_null($ar)) {
			$ar = $this->getAR();
		} else if (!is_a($ar,'BaseAR')) {
			throw new SysException("Classe informada não é AR",1);
		}
		if (!$ar->isValid()) {
			throw new SysException("Registro invalido",2);
		}

		try {
			$this->preDelete($ar);
			$this->execDelete($ar);
			$this->posDelete($ar);
		} catch (SysException $e) {
			if (is_a($e,'SysException')) {
				$e->setParm('ar',$ar);
			}
			$this->_trataExcecao($e,'delete');
			throw $e;
		}
	} // eof delete

	/**
	 * Trata a exceção
	 *
	 * @param SysException $excecao
	 * @param string $acao
	 */
	/**
	 * @param SysException $excecao
	 * @param string $acao
	 */
	protected function _trataExcecao(&$excecao,$acao='insert') {

		// Invoca a classe que processa a string de retorno do banco de dados,
		// e altera os parâmetros para exibir na tela.
		TrataErroBancoDadosFactory::getParametrosExcecao($excecao,$acao);

		$msg = $excecao->getParm('msg'); // Recuperando a mensagem definida pelo tratador.

		// Dependendo do código de erro, altera a mensagem - aqui pode não ser tão útil,
		// mas no método que sobreescreve este será.
		switch ($excecao->getParm('codigo')) {
			case '51' : break; // erro no insert
			case '61' : break; // erro no update
			case '71' : $msg = 'Erro na exclusão do Registro (vide detalhes)'; break;
			case '72' : $msg = 'Registro possui registros dependentes, e por isso não pode ser excluído.'; break;
			/*default: $msg = 'Erro no banco de dados (Vide detalhes)';*/
		}
		$se = new SysException($msg,$excecao->getParm('codigo'));
		$se->setDescricao($excecao->getMessage());
		$excecao = $se;
	} // eof _trataExcecao



	function preDelete(BaseAR &$ar) {}

	/**
	 * Executa a exclusão do registro na base
	 * de dados.
	 *
	 * @param BaseAR $ar
	 * @throws SysException
	 */
	function execDelete(BaseAR &$ar) {
		try {
			$ar->delete();
			$this->__audita($ar,'delete');
		} catch (Exception $e) {
			$se = new SysException($e->getMessage(),$e->getCode());
			throw $se;
		}
	} // eof execDelete


	/**
	 * Executa algum procedimento após a exclusão
	 * do registro.
	 *
	 * @param BaseAR $ar
	 */
	function posDelete(BaseAR &$ar) {} // eof posDelete



	// /////////////////////////////////////////////////////
	// ///  LOAD  //////////////////////////////////////////
	// /////////////////////////////////////////////////////



	/**
	 * Recupera uma instância deste objeto.
	 *
	 * @param BaseAR|string $ar
	 */
	function load($ar = null) {
		if (!$this->_isCarregado) {
			if (is_null($ar)) {
				$ar = $this->getAR();
			} else if (!($ar instanceof BaseAR) && (!is_string($ar))) {
				throw new SysException("Classe informada não é AR",1);
			}
			$this->preLoad($ar);
			$this->execLoad($ar);
			$this->posLoad($ar);
		} else {
			$this->_isCarregado = true;
		}
	} // eof load


	function forceLoad($ar = null) {
		$this->_isCarregado = false;
		$this->load($ar);
	}



	/**
	 * Executa instruções antes da carga do objeto.
	 *
	 * @param BaseAR $ar
	 */
	function preLoad(&$ar) {} // eof preLoad

	/**
	 * Executa a carga do objeto, com base no AR passado por parâmetro.
	 *
	 * @param BaseAR|string $ar
	 */
	function execLoad(&$ar) {
		try {
			if (($ar instanceof BaseAR)) {
				$ar->Load($ar->getPK());
			} else {
				$where = $ar;
				$ar = $this->getAR();
				$ar->Load($where);
			}
			$this->bind($ar);
			$this->__audita($ar,'load');
		} catch (Exception $e) {
			$se = new SysException($e->getMessage(),$e->getCode());
			throw $se;
		}
	} // eof execLoad


	/**
	 * Executa instruções após da carga do objeto.
	 *
	 * @param BaseAR $ar
	 */
	function posLoad(&$ar) {} // eof posLoad


	// *********************************************************************
	// *********************************************************************
	// *********************************************************************
	// *********************************************************************


	/**
	 * Método que testa se o atributo do objeto passado como
	 * parâmetro é requerido e está vazio. Em caso afirmativo,
	 * retorna true (ou seja, precisa ser preenchido).
	 *
	 * @param Object $obj
	 * @param string $nomeAtributo
	 * @return boolean
	 */
	protected function testProperty($nomeAtributo) {
		if (substr($nomeAtributo,0,1) == '_') {
			return false;
		}

		$prop = new ReflectionProperty(get_class($this),$nomeAtributo);
		$s = $prop->getDocComment();
		$nomeMetodoGet = $this->formatGetMethod($nomeAtributo);

		if (((stripos($s,'@required')>0)) || (stripos($s,'@requerid')>0) ) {
			$v = $this->$nomeMetodoGet();
			if (is_null($v)) {
				return true;
			} else if (is_scalar($v) && (strlen($v) == 0)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} // eof testProperty



	/**
	 * Método que, dado um objeto, verifica se seus atributos
	 * obrigatórios estão preenchidos ou não. Aqueles que
	 * quebrarem a restrição
	 *
	 * @return array
	 */
	public function checkAllRequired() {
		$nomeClasse = get_class($this);
		$class = new ReflectionClass($nomeClasse);
		$lista = $class->getProperties();
		$listaAux = array();
		foreach ($lista as $atributo) {

			if ($this->testProperty($atributo->name)) {
				$listaAux[] = "\"" . $atributo->name . "\"" ;
			}
		}
		return $listaAux;
	} // eof checkAllRequired



	/**
	 * Com base no nome do atributo, retorna o nome do método
	 * getter dele.
	 *
	 * @param string $attName
	 * @return string
	 */
	public function formatGetMethod($attName) {
		$s = 'get' . strtoupper(substr($attName,0,1));
		$s .= substr($attName,1);
		return $s;
	} // eof formatGetMethod



	/**
	 * Verifica quais campos obrigatórios não estão preenchidos, e se algum for encontrado,
	 * então emite uma exception.
	 */
	protected function checkFields() {
		$arr = $this->checkAllRequired();
		if (count($arr) > 0) {
			switch (count($arr)) {
				case 1: $msg = "O campo ".join(',',$arr)."; e obrigatorio, mas nao foi informado."; break;
				default: $msg = "Os campos: ".join(',',$arr)."; sao obrigatorios, mas nao foram informados.";
			}
			throw new SysException($msg,999);
		}
	} // eof checkFields




	/**
	 * Retorna a lista de status disponíveis.
	 *
	 * @return array
	 */
	function getListaStatus() {
		return array(	BasePersistenteBO::ST_REG_ATIVO => 'Ativo',
		BasePersistenteBO::ST_REG_INATIVO  => 'Inativo');
	} // eof getListaStatus



	/**
	 * Retorna a lista de status disponíveis para filtragem nas listagens.
	 *
	 * @return array
	 */
	function getListaFiltroStatus() {
		$status = BasePersistenteBO::getListaStatus();
		$status[BasePersistenteBO::ST_REG_AMBOS] = 'Ambos';
		return $status;
	} // eof getListaFiltroStatus



	/**
	 * Construtor da classe
	 *
	 * @param string $idCon
	 * @return BasePersistenteBO
	 */
	function __construct($idCon=false) {
		parent::__construct($idCon);
	} // eof BasePersistenteBO



	/**
	 * Inicializa a auditoria onde eventos são registrados em uma tabela
	 * do sistema.
	 *
	 * @param string $label Nome simbólico do objeto
	 */
	protected function __initAuditoria($nivel,$label='') {
		$this->__auditoriaAtiva = $nivel;
		$this->__aud_labelObjeto = $label;
	} // eof __initAuditoria




	/**
	 * Enter description here...
	 *
	 * @param BaseAR $arOrigem
	 * @param string $acao
	 */
	protected function __audita($arOrigem,$acao) {
		if ($this->__auditoriaAtiva > 0) {
			/**
			 * Instanciar um AR de RegistroAuditoria, setando seus atributos
			 * e realizando o insert, vendo para que qualquer exceção seja
			 * ignorada.
			 */

			switch ($acao) {
				case 'insert': $flag = BasePersistenteBO::AUDITA_INSERT; break;
				case 'update': $flag = BasePersistenteBO::AUDITA_UPDATE; break;
				case 'delete': $flag = BasePersistenteBO::AUDITA_DELETE; break;
				case 'load'  : $flag = BasePersistenteBO::AUDITA_LOAD;   break;
			}
			if ($flag & $this->__auditoriaAtiva) {


				try {
					$ar = new SysRegistroAuditoriaAR();

					$ar->setAcao($acao);
					$ar->setClasse(get_class($this));

					if (($this->getApp()->getSess()->getProfile()) /*&& !is_null($this->getApp()->getSess()->getProfile()->getUsuario())*/) {
						$ar->setUserID($this->getApp()->getSess()->getProfile()->getUsuario()->getID());
						$ar->setUsername($this->getApp()->getSess()->getProfile()->getUsuario()->getUsername());
					} else {
						$ar->setUserID(0);
						$ar->setUsername('n/i');
					}
					$ar->setDhEvento(date('Y-m-d h:i:s'));
					$ar->setObjetoID($arOrigem->getID());
					$ar->setIP($_SERVER['REMOTE_ADDR']);
					$ar->setObservacoes($arOrigem->_tostring());
					$ar->defHash();

					$ar->insert();
				} catch (Exception $e) {
					$se = new SysException('Erro de configuracao: Favor instalar a tabela de auditoria',SYSErroBancoDados::ERRO_SYS_TABELA_AUDITORIA_INEXISTENTE);
					$se->setDescricao($e->getMessage());
					throw $se;
				}

			}
		}
	}



} // eoc BasePersistenteBO

?>