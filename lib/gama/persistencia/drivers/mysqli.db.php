<?php // $Rev: $ $Author: $ $Date: $//

require('./lib/adodb/drivers/adodb-mysqli.inc.php');

class GamaAdoMysqlI extends ADODB_mysqli {


	function &Execute($sql,$inputarr=false) {
		$this->logar($sql,$inputarr);
		return parent::Execute($sql,$inputarr);
	}


	function logar($sql,$inputarr=false) {

		if (MainGama::getApp()->getConfig('debug')) {
			if ($inputarr) {

				$i = strpos($sql,'(');
				$k = strpos($sql,')');
				$txt = substr($sql,$i+1,$k-$i-1);
				$lista = explode(',',$txt);


				//			MainGama::getApp()->getDebug(true)->log(array($i,$k),'indices');
				MainGama::getApp()->getDebug(true)->log(array_combine($lista,$inputarr),'campos');
				//			MainGama::getApp()->getDebug(true)->log(array($sql,$inputarr),'logar');
			}
		}
	}

} // GamaAdoMysqlI



?>