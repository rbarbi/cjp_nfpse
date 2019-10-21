<?php // $Rev: 84 $ - $Author: eduluz $ $Date: 2008-09-01 17:24:09 -0300 (seg, 01 set 2008) $

/**
 * Classe que transporta as mensagens e codigos de erros entre as camadas.
 *
 * @author Eduardo S. da Luz
 * @copyright IASoft Desenvolvimento de Sistemas
 * @package gama3.utils.msg
 */
class SysErro  extends SysMsg  {

	/**
	 * Construtor da classe SysErro.
	 *
	 * @param integer $codigo
	 * @param string $msg
	 * @param integer $nivel
	 * @return SysErro
	 */
	function SysErro($codigo,$msg,$nivel=10) {
		$this->SysMsg($codigo,$msg,$nivel);
	}
}



?>