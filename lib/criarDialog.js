function criarDialogCadastro(){
	var handleSubmit = function() {
		this.submit();
	};
	var handleCancel = function() {
		this.cancel();
	};
	//executado se o formulario for enviado com sucesso
	var handleSuccess = function(o) {
		alert(o.responseText);
	};
	//executado caso o formulario nao seja enviar corretamente
	var handleFailure = function(o) {
		alert("Submission failed: " + o.status);
	};

	var dlgCad = document.getElementById("dlgCadastro");
	idDialog++;
	dlgCad.id = idDialog;

	// Instantiate the Dialog
	//	YAHOO.example.container.dialog1 = new YAHOO.widget.Dialog("dialog1",
	dlgCadastro = new YAHOO.widget.Dialog(dlgCad.id,
	{ fixedcenter : true,
	constraintoviewport : true,
	buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true },
				{ text:"Cancel", handler:handleCancel } ]
	});

	// valida dados
	dlgCadastro.validate = function() {
		var data = this.getData();
		if (data.nome == '' || data.descricao == '') {
			alert(dlgCadastro.id);
			return false;
		} else {
			return true;
		}
	};

	// Wire up the success and failure handlers
	dlgCadastro.callback = { success: handleSuccess,
	failure: handleFailure };
	// Render the Dialog
	dlgCadastro.render();
}