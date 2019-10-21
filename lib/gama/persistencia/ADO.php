<?php

global $ADODB_NEWCONNECTION;

$ADODB_NEWCONNECTION = 'gama_ado_factory';

function & gama_ado_factory($driver) {


	if ($driver !== 'mysql' && $driver !== 'postgres8' && $driver !== 'mysqli') {
		return false;
	} else {

		switch ($driver) {
			case 'postgres8': $nomeDriver = 'GamaAdoPostgres8'; break;
			case 'mysqli': $nomeDriver = 'GamaAdoMysqlI'; break;
		}

//		$driver = 'hack_'.$driver;
		$obj = new $nomeDriver();
		return $obj;
	}
}

?>