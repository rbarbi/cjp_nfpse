<?php // $Rev: $ $Author: $ $Date: $//


/**
 * Classe usada para devolver situa��es de sucesso...
 * A pedido do Kal�u...
 * Ser� que vamos precisar?
 */
class SysSucesso extends Exception {

	function __construct($msg,$codigo=0) {
		parent::__construct($msg,$codigo);
	}
}


?>