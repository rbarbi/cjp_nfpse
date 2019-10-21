function filtrarTransacao(){
	var Dom = YAHOO.util.Dom;
	var tabela = arrTabela[tabelaAtv];
	var fonteDados = tabela.getDataSource();
	var paginador = tabela.get('paginator');
	var filtro = Dom.get('idFiltro'+tabelaAtv).value;
	Dom.get('idFiltroAtual'+tabelaAtv).value = filtro;
	fonteDados.sendRequest("m=acesso&admin=1&a=SysTransacao.action&acao=doListarTransacoes&startIndex=0&results=5&filtro=" + filtro,tabela.onDataReturnInitializeTable, tabela);
	paginador.fireEvent('changeRequest',paginador.getState({'page':1}));
}

function criarTabela(){
	var DataSource = YAHOO.util.DataSource,
	DataTable  = YAHOO.widget.DataTable,
	Dom = YAHOO.util.Dom,
	Paginator  = YAHOO.widget.Paginator;

	var fonteDados = new DataSource('index.php?');
	fonteDados.responseType   = DataSource.TYPE_JSON;
	fonteDados.responseSchema = {
		resultsList : 'records',
		fields      : ['id','nome', 'username', 'nivelAcesso','statusRegistro'],
		metaFields : {
			totalRecords: 'totalRecords'
		}
	};

	idDlgLista++;
	var pag = document.getElementById("paginador");
	pag.id = pag.id + idDlgLista;

	var filtro = document.getElementById("idFiltro");
	filtro.id = filtro.id + idDlgLista;

	var filtroAtual = document.getElementById("idFiltroAtual");
	filtroAtual.id = filtroAtual.id + idDlgLista;

	var bFiltro = document.getElementById("bFiltrar");
	bFiltro.id = bFiltro.id + idDlgLista;


	var consulta = function (state,dt) {
		return "m=acesso&admin=1&a=SysTransacao.action&acao=doListarTransacoes&startIndex=" + state.pagination.recordOffset +
		"&results=" + state.pagination.rowsPerPage + "&filtro=" + filtroAtual.value;
	};

	var paginador = new IaPaginator({
		containers         : [pag.id],
		pageLinks          : 5,
		rowsPerPage        : 3,
		template           : " {CurrentPageReport} {PreviousPageLink} {PageLinks} {NextPageLink} "
	});

	var myTableConfig = {
		initialRequest         : 'm=acesso&admin=1&a=SysTransacao.action&acao=doListarTransacoes&startIndex=0&results=0',
		generateRequest        : consulta,
		paginationEventHandler : DataTable.handleDataSourcePagination,
		paginator              : paginador
	};

	this.formatAtualizar = function(elCell, oRecord, oColumn, oData) {
		YAHOO.util.Dom.addClass(elCell, "atualizar");
		elCell.innerHTML = '<img src="./temas/app/img/stock_edit-16.png" id="atualizar">';
	};

	this.formatExcluir = function(elCell, oRecord, oColumn, oData) {
		YAHOO.util.Dom.addClass(elCell, "excluir");
		elCell.innerHTML = '<img src="./temas/app/img/icons_15.gif" id="excluir">';
	};

	this.formatVisualizar = function(elCell, oRecord, oColumn, oData) {
		YAHOO.util.Dom.addClass(elCell, "visualizar");
		elCell.innerHTML = '<img src="./temas/app/img/icons_13.gif" id="visualizar">';
	};

	var myColumnDefs = [
	{key:"id"},
	{key:"nome"},
	{key:"username"},
	{key:"nivelAcesso"},
	{key:"statusRegistro"},
	{key:"atualizar",label:"", formatter:formatAtualizar},
	{key:"visualizar", label:"",formatter:formatVisualizar},
	{key:"excluir", label:"",formatter:formatExcluir}
	];


	// Subscribe to events for row selection
	var tab = document.getElementById("tabela");
	tab.id = tab.id + idDlgLista;
	var tabela = new DataTable(tab.id, myColumnDefs, fonteDados, myTableConfig);

	function onFocus() {
		tabelaAtv = tab.id.substring(6);
	}

	tabela.subscribe("rowMouseoverEvent", tabela.onEventHighlightRow);
	tabela.subscribe("rowMouseoutEvent", tabela.onEventUnhighlightRow);
	tabela.subscribe("rowClickEvent", tabela.onEventSelectRow);
	tabela.subscribe("tableFocusEvent", onFocus);
	tabela.subscribe("cellClickEvent", function(oArgs){
		var record = this.getRecord(oArgs.target);
		switch (oArgs.event.target.id){
			case "atualizar":{
				var paramAlt = [];
				paramAlt[0] = "showFormAltTransacao";
				paramAlt[1] = "criarDlgAlterar()";
				paramAlt[2] = record.getData().id;
				executaMenu(null,paramAlt);
				break;
			}
			case "visualizar":{
				var paramView = [];
				paramView[0] = "showTransacao";
				paramView[1] = "criarDlgVisualizar()";
				paramView[2] = record.getData().id;
				executaMenu(null,paramView);
				break;
			}
			case "excluir":{
				var div = document.createElement("div");
				div.id = "dlgConfirmar";
				document.body.appendChild(div);

				var handleSim = function() {
					this.hide();
					var handleSuccess = function(o){
						alert(o.responseText);
						var tabela = arrTabela[tabelaAtv];
						var paginador = tabela.get('paginator');
						tabela.initializeTable();
						paginador.fireEvent('changeRequest',paginador.getState({'page': paginador.getCurrentPage()}));
					};

					var handleFailure = function(o){
						alert("erro");
					};

					var callback =
					{
						success:handleSuccess,
						failure:handleFailure
					};
					var sUrl = "index.php";
					var postData = "m=acesso&admin=1&a=SysTransacao.action&acao=doDelTransacao&oid="+record.getData().id;
					var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);
				};

				var handleNao = function() {
					this.hide();
				};

				// Instantiate the Dialog
				var dlgConfirmar = new YAHOO.widget.SimpleDialog("dlgConfirmacao",
				{ width: "300px",
				fixedcenter: true,
				draggable: false,
				close: true,
				text: "Deseja realmente deletar esse registro?",
				icon: YAHOO.widget.SimpleDialog.ICON_HELP,
				constraintoviewport: true,
				buttons: [ { text:"Sim", handler:handleSim },
				{ text:"Não",  handler:handleNao, isDefault:true } ]
				} );
				dlgConfirmar.setHeader("Deletar Tipo Objeto?");
				// Render the Dialog
				dlgConfirmar.render("dlgConfirmar");
				break;
			}
		}
	});

	function executaFiltro(){
		tabelaAtv = tab.id.substring(6);
		filtrarTransacao();
	}

	var botaoFiltro = new YAHOO.widget.Button(bFiltro.id, { onclick: { fn: executaFiltro } });

	arrTabela[idDlgLista] = tabela;
	tabelaAtv = tab.id.substring(6);
	return tabela;
}

function criarDlgListaTransacao(){
	criarTabela();
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

	};

	//executado caso o formulario nao seja enviar corretamente
	var handleFailure = function(o) {
		alert("Submission failed: " + o.status);
	};

	//busca o elemnto html que foi inserido pelo smarty
	var dlgLst = document.getElementById("dlgLista");
	//muda o id da dialog para que possam existir varias dialogs
	dlgLst.id = dlgLst.id + idDlgLista;

	// cria adialog
	//	YAHOO.example.container.dialog1 = new YAHOO.widget.Dialog("dialog1",
	dlgLista = new YAHOO.widget.Dialog(dlgLst.id,
	{ fixedcenter : true,
	constraintoviewport : true
	});

	// valida dados
	dlgLista.validate = function() {
		var data = this.getData();
		if (data.nome == '' || data.descricao == '') {
			alert(dlgCadastro.id);
			return false;
		} else {
			return true;
		}
	};

	//define os handlers de sucesso e falha
	dlgLista.callback = { success: handleSuccess,
	failure: handleFailure };
	// Render the Dialog
	dlgLista.render();
}