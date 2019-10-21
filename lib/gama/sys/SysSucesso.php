<?php // $Rev: $ $Author: $ $Date: $//


/**
 * Classe usada para devolver situações de sucesso...
 * A pedido do Kaléu...
 * Será que vamos precisar?
 */
class SysSucesso extends Exception {

	function __construct($msg,$codigo=0) {
		parent::__construct($msg,$codigo);
	}
}


?>