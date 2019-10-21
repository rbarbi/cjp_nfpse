/**
* Se for necessário alterar o script principal de 'index.php' para
* qualquer outro, basta incluir no template:
*
* <script>var _rootScript = '{$_rootScript}'</script>
*
* Dessa forma estas rotinas vão usar o nome do novo script para
* realizar suas operações.
*
*/
//if (!_rootScript) {
var _rootScript = 'index.php';
//}


function gett(nomeCampo) {
	return getts(nomeCampo,document.getElementById(nomeCampo).value);
}

function getts(nomeCampo,valor) {
	var s = '';
	switch (nomeCampo) {
		case 'm':
		case 'u':
		case 'a':
		case 'acao': break;
		default: s = '&'+nomeCampo+'='+valor;
	}
	return s;
}

function buildParms_(formID) {
	//	window.alert(formID);
	var formulario = document.getElementById(formID);
	//	window.alert(formulario);
	var s = '';
	for (i=0;i<formulario.length ; i++) {
		//
		if (formulario.elements[i].name.length == 0) {
			s += getts(formulario.elements[i].id,formulario.elements[i].value);
		} else {
			s += getts(formulario.elements[i].name,formulario.elements[i].value);
		}
	}
//	alert(s);
	return s;
}

function doGoTo(m,u,a,acao,demaisDados) {
	g3Get(m,u,a,acao,demaisDados);
} // eof doGoTo


function doGoTo_(formulario) {
	g3Post(formulario);
} // eof doGoTo_


function g3Post(formulario) {
	var demaisDados = buildParms_(formulario);
	var m = document.getElementById(formulario).elements['m'].value;
	var u = document.getElementById(formulario).elements['u'].value;
	var a = document.getElementById(formulario).elements['a'].value;
	var acao = document.getElementById(formulario).elements['acao'].value;
	g3Get(m,u,a,acao,demaisDados);
}



function g3Get(m,u,a,acao,demaisDados) {
	var url = _rootScript;
	var myObject;

	//	window.alert('acao = ' + acao + "\ndemais = "+demaisDados);
	var ajax = new Ajax.Request(url,
	{
		method:'post',
		parameters: {m: m, u: u, a: a, acao: acao,clsb3DoRedirect:1, clsb3Dados: demaisDados},
		onSuccess: g3GetOk,
		onFailure: g3GetErro
	});
}


function g3GetOk(transport,json) {

	var response = transport.responseText || "no response text";
	//			window.alert(response);
	try {
		var myObject = transport.responseText.evalJSON(true);
		if (!myObject) {
			window.alert('erro ' + response);
		}
	} catch (e) {
		window.alert(e);
	}

	switch (myObject.tipo) {
		case 'url' :
			if (myObject._target != undefined) {
				parent.window.frames[myObject._target].location.href=_rootScript+'?'+myObject.url;
			} else {
				location.href=_rootScript+'?'+myObject.url;
			}
			break;
		case 'comando':
			eval('{' + myObject.url + '}');
			break;
		case 'alerta':
			window.alert(myObject.url);
			break;
		default: //	window.alert(response);
	}
}


function g3GetErro(req,exception){
	var response = req.responseText || "no response text";
	if (req.responseText) {
//	window.alert(req);
		window.alert('Erro: ' + req.status + ' ' + exception + ' - ' + response);
	}
}

