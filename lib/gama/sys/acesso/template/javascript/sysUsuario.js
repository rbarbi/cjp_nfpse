var idDlgAlterar=0;
var contador=0;
var resposta;
var arrTabela= [];
var tabelaAtv=-1;
var idDlgLista =0;
var idDialog=0;


function executaMenuUsuario(e,param) {
	executaMenu(e,param,'SysUsuario.action');
}


function executaMenuTransacao(e,param) {
	executaMenu(e,param,'SysTransacao.action');
}


function executaMenu(e,param,action){
	/*
	param � um array de string onde a posi��o:
	0: deve conter o metodo do action a ser chamado
	1: deve conter a fun��o javascript que utilizar� o html inserido na pagina
	2: caso seja uma tela de altera��o o parametro 3 deve conter o id a ser alterado.
	ex:
	param[0] = "showFormListaTipoObjeto";
	param[1] = "criarDlgLista()";
	*/
	contador++;
	//cria novo div onde o tempalte  passado via JSON ser� adicionado
	var div = document.createElement("div");
	div.id = contador;
	document.body.appendChild(div);
	//defini o handle de sucesso da requisi��o
	var handleSuccess = function(o){
		//se o responseText tiver sido definido...
		if(o.responseText != undefined){
			try {
				//decodifica o response text de JSON para um objeto
				resposta = YAHOO.lang.JSON.parse(o.responseText);
			}
			catch (x) {
				alert("JSON Parse failed!");
				return;
			}
			//verifica se o script j� n�o foi carregado anteriomente
			if(document.getElementById(param[0])== null){
				//cria um novo elemento script
				var novo = document.createElement("script");
				//decodifica o caminho do js e seta comoo source do script
				novo.src = decodeBase64(resposta.js);
				//seta como id o parametro[0]
				novo.id = param[0];
				document.body.appendChild(novo);
			}
			//decodifica o template e adiciona ao div criadoanteriomente
			div.innerHTML = decodeBase64(resposta.template);
			//executa a fun��o passada no param[1] ap�s 100ms
			setTimeout(param[1],100);
		}

	};
	//handle de falha
	var handleFailure = function(o){
		if(o.responseText !== undefined){
			alert("Erro:");
		}
	};
	//define o callback de sucesso e falha
	var callback =
	{
		success:handleSuccess,
		failure:handleFailure
	};
	//define a url d arequisi��o
	var sUrl = "index.php";
	//define os parametros
	var postData = "m=acesso&admin=1&a="+action+"&acao="+param[0];
	//verifica se o terceiro parametro foi informado
	if (param.length==3){
		//se sima diona nos parametros da requisi��o
		postData += '&oid='+param[2];
	}
	//efetua a requisi��o executando o metodo passado no param[0] do action tabelas.action
	var request = YAHOO.util.Connect.asyncRequest('POST', sUrl, callback, postData);
}

function criarMenuBar(){

	//cria o array de dados que ir�o compor as op��es do menubar
	var aItemData = [
	{
		text: "Usuario",
		submenu: {
			id: "mUsuario",
			itemdata: [
			{text: "Cadastrar", id: "miCadUsuario"},
			{text: "Listar", id: "miListaUsuario"}
			]
		}
	},
	{
		text: "Transa&ccedil;&atilde;o",
		submenu: {
			id: "mTransacao",
			itemdata: [
			{text: "Cadastrar", id:"miCadTransacao"},
			{text: "Listar", id:"miListaTransacao"},
			]
		}
	}

	];

	//cria nova instancia de menubar
	var oMenuBar = new YAHOO.widget.MenuBar("menu", {
		lazyload: true,
		itemdata: aItemData //array contendo os dados do menu
	});

	//metodo que ser� executado quando menu for exibido
	function onSubmenuShow() {
		if (this.id == "mUsuario") {
			YAHOO.util.Dom.setX(this.element, 0);//seta a posi��o de X para 0px, o padr�o � 10px
		}
	}

	//redefine o evento "onShow" do menubar utilzandoa fun��o definida acima.
	oMenuBar.subscribe("show", onSubmenuShow);
	//o metodo render server para "plotar" os objetos
	oMenuBar.render(document.body);

	//array contendo os parametros da fun��o que ser� executada ao clicar no bot�o
	var paramListaUsuario = [];
	paramListaUsuario[0] = "showFormListaUsuarios";
	paramListaUsuario[1] = "criarDlgListaUsuario()";
	//associa o evento ao submenu "alterar" do menu "Tipo Objeto"
	YAHOO.util.Event.addListener("miListaUsuario", "click",executaMenuUsuario,paramListaUsuario);

	var paramCadUsuario = [];
	paramCadUsuario[0] = "showFormCadUsuario";
	paramCadUsuario[1] = "criarDialogCadastro()";
	//associa o evento ao submenu "cadastrar" do menu "Tipo Objeto"
	YAHOO.util.Event.addListener("miCadUsuario", "click",executaMenuUsuario,paramCadUsuario);

	var paramCadTransacao = [];
	paramCadTransacao[0] = "showFormCadTransacao";
	paramCadTransacao[1] = "criarDialogCadastro()";
	YAHOO.util.Event.addListener("miCadTransacao", "click",executaMenuTransacao,paramCadTransacao);

	var paramListaTransacao = [];
	paramListaTransacao[0] = "showFormListaTransacao";
	paramListaTransacao[1] = "criarDlgListaTransacao()";
	YAHOO.util.Event.addListener("miListaTransacao", "click",executaMenuTransacao,paramListaTransacao);

}

YAHOO.util.Event.onDOMReady(criarMenuBar);