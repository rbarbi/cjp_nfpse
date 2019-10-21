<?php


class DesktopAction extends BaseAction {




	function DesktopAction($app,$GET,$POST)
	{
		$this->BaseAction(MainGama::getApp(),$GET,$POST,'./mod/logtruck');
		$this->registraAcao('getNumMensagensDashBoard');
		$this->registraAcao('getListaMensagensDashBoard');
	}


	function getNumMensagensDashBoard() {
		$num = 10;

		echo json_encode(array(array('numMensagensPendentes'=>$num)));
		exit;
	}

	function getListaMensagensDashBoard() {

		$lsMensagens = array();

		$mensagem['id'] = 1;
		$mensagem['tipoMensagem'] = 1;
		$mensagem['dhMensagem'] = '2008-12-04 12:01:03';
		$mensagem['mensagem'] = 'Eduardo acessou no sistema';
		$mensagem['feito'] = 100;

		$lsMensagens[] = $mensagem;


		$mensagem['id'] = 2;
		$mensagem['tipoMensagem'] = 1;
		$mensagem['dhMensagem'] = '2008-12-05 12:01:13';
		$mensagem['mensagem'] = 'Eduardo saiu no sistema';
		$mensagem['feito'] = 80;

		$lsMensagens[] = $mensagem;

		$mensagem['id'] = 3;
		$mensagem['tipoMensagem'] = 2;
		$mensagem['dhMensagem'] = '2008-12-04 12:01:03';
		$mensagem['mensagem'] = 'Eduardo acessou no sistema';
		$mensagem['feito'] = 100;

		$lsMensagens[] = $mensagem;


		$mensagem['id'] = 4;
		$mensagem['tipoMensagem'] = 3;
		$mensagem['dhMensagem'] = '2008-12-05 12:01:13';
		$mensagem['mensagem'] = 'Eduardo saiu no sistema';
		$mensagem['feito'] = 10;

		$lsMensagens[] = $mensagem;

		$mensagem['id'] = 5;
		$mensagem['tipoMensagem'] = 4;
		$mensagem['dhMensagem'] = '2008-12-05 12:01:15';
		$mensagem['mensagem'] = 'Eduardo saiu no sistema';
		$mensagem['feito'] = 89;

		$lsMensagens[] = $mensagem;

		$mensagem['id'] = 6;
		$mensagem['tipoMensagem'] = 2;
		$mensagem['dhMensagem'] = '2008-12-04 12:01:03';
		$mensagem['mensagem'] = 'Eduardo acessou no sistema';
		$mensagem['feito'] = 50;

		$lsMensagens[] = $mensagem;


		$mensagem['id'] = 7;
		$mensagem['tipoMensagem'] = 3;
		$mensagem['dhMensagem'] = '2008-12-05 12:01:13';
		$mensagem['mensagem'] = 'Eduardo saiu no sistema';
		$mensagem['feito'] = 2;

		$lsMensagens[] = $mensagem;

		$mensagem['id'] = 8;
		$mensagem['tipoMensagem'] = 2;
		$mensagem['dhMensagem'] = '2008-12-05 12:01:13';
		$mensagem['mensagem'] = 'Eduardo saiu no sistema';
		$mensagem['feito'] = 72;

		$lsMensagens[] = $mensagem;

		$mensagem['id'] = 9;
		$mensagem['tipoMensagem'] = 3;
		$mensagem['dhMensagem'] = '2008-12-05 12:01:13';
		$mensagem['mensagem'] = 'Eduardo saiu no sistema';
		$mensagem['feito'] = 2;

		$lsMensagens[] = $mensagem;

		$mensagem['id'] = 10;
		$mensagem['tipoMensagem'] = 2;
		$mensagem['dhMensagem'] = '2008-12-05 12:01:13';
		$mensagem['mensagem'] = 'Eduardo saiu no sistema';
		$mensagem['feito'] = 72;

		$lsMensagens[] = $mensagem;


		echo json_encode($lsMensagens);
		exit;

	}



}



?>