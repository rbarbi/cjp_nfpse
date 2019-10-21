<?php


class AutoExecGama extends MainGama {

	function AutoExecGama($rootPath,$modPath) {
//		if ($this->getApp()->isLogged()) {
//			echo 'Conectado<hr>';
//		} else {
//			echo '---<hr>';
//		}
		$this->MainGama($rootPath,$modPath);
		if ($this->getParms('doLogout',false)) {
			$this->getSess()->set('conectado',false);
			$this->getSess()->setProfile(null);
		}
	}

	function preExec() {
//		Teste para verificar o funcionamento das mudanças de comportamento da
//		aplicação com base no
//
//		if ($_SERVER['REQUEST_URI'] == '/apoio/') {
//			$_POST['m'] = 'inventario';
//		}
//
//
//		if ($_SERVER['SERVER_NAME'] == 'localhost') {
//			$this->setNomeINI('AutoExecIASoft.ini');
//			$this->loadConfigINI();
//		}




//		echo '<pre>';
//		print_r($this->config);
//		exit;
	}


	function checkAutorizacao() {
		return true;
	}

}

$autoexec = new AutoExecGama($rootPath,$modPath);
return $autoexec;
?>