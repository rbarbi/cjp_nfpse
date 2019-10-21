function criarDlgAlterar(){
	idDlgAlterar++;
	//hadle para submit
	var handleSubmit = function() {
		this.submit();
	};
	//handle para cancelar
	var handleCancel = function() {
		this.cancel();
	};
	//executado se o formulario for enviado com sucesso
	var handleSuccess = function(o) {
		alert(o.responseText);
		//pega a tabela "ativa"
		var tabela = arrTabela[tabelaAtv];
		var paginador = tabela.get('paginator');
		//força a atualização da tabela "ativa"
		tabela.initializeTable(); 
		paginador.fireEvent('changeRequest',paginador.getState({'page': paginador.getCurrentPage()}));

	};
	//executado caso o formulario nao seja enviar corretamente
	var handleFailure = function(o) {
		alert("Submission failed: " + o.status);
	};
	//busca o elemento html que foi inserido pelo smarty
	var dlgAlt = document.getElementById("dlgAlterar");
	//muda o id da dialog para que possam existir varias dialogs
	dlgAlt.id = dlgAlt.id + idDlgAlterar;

	// cria adialog
	dlgAlterar = new YAHOO.widget.Dialog(dlgAlt.id,
	{  width : "350px",
	fixedcenter : true,
	constraintoviewport : true,
	buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true },
	{ text:"Cancel", handler:handleCancel } ]
	});

	// valida dados
	dlgAlterar.validate = function() {
		var data = this.getData();
		if (data.nome == '' || data.descricao == '' || data.id == '') {
			alert("Todos os campos são obrigatorios");
			return false;
		} else {
			return true;
		}
	};
	//define os handlers de sucesso e falha
	dlgAlterar.callback = {
		success: handleSuccess,
		failure: handleFailure
	};

	// "plota" a dialog
	dlgAlterar.render();
}