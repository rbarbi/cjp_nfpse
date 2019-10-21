<?php // $Rev: $ $Author: $ $Date: $//

require('./lib/adodb/drivers/adodb-postgres7.inc.php');

class GamaAdoPostgres8 extends adodb_postgres7 {


	function &Execute($sql,$inputarr=false) {
		$this->logar($sql,$inputarr);
		return parent::Execute($sql,$inputarr);
	}


	function logar($sql,$inputarr=false) {

            // Para n�o dar recursividade, � necess�rio fazer o envio de dados para um arquivo auxiliar, que ser� usado em um
            // m�todo que vai execut�-lo depois no banco...
            //
            MainGama::getApp()->log(9,'sql',addslashes(var_export(array('sql' => $sql,'inputarray'=>$inputarr),true)),'MainGama');


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



        public function gamaExecute($sql) {
            error_log("\n $sql",3,'log.sql');
        }

        public function gamaCache($sql) {
            error_log("\n $sql",3,'log.sql');
        }

        public function directExec($sql) {
            pg_query($this->_connectionID,$sql);
            //error_log("\n $sql",3,'log.sql');
        }


        /**
         * Faz o tratamento de strings, para inclus�o/atualiza��o no banco de dados.
         * @param string $s
         */
        public function escape2($s) {
            return $s; //str_replace("'", "''", $s);
        }


} // GamaAdoPostgres7



?>