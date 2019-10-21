<script src="./lib/prototype.js" ></script>
{literal}
<script>


function getts2(nomeCampo,valor) {
	var s = '';
	switch (nomeCampo) {
		case 'm':
		case 'u':
		case 'a':
		case 'acao': break;
		default: s = nomeCampo+" : '"+valor + "' ";
	}
	return s;
}

function processar() {

	var url = 'index.php';
	var form = document.getElementById('frm');
	var li = parseInt(form.nrcomponents.value);
	var nomeChave = 'chave_';
	var valorChave = 'valor_';
	var valor = '';
	var nome = '';
	var demaisDados = '';

	li = form.elements.length;

	for (var j = 0; j<li; j++) {
		if (form.elements[j].name.substring(0,6) == 'chave_') {
			i = form.elements[j].name.substring(6,8);
			valorChave = 'valor_' + i;
			nomeChave = 'chave_' + i;
			//			eval("nomeChave += " + i);
			//			eval("valorChave += " + i);
			eval("valor = form." + valorChave + '.value;');
			eval("nome = form." + nomeChave + '.value;');
			demaisDados += ', ' + getts2(nome,valor);
		}
	}

	var comando = "var ajax = new Ajax.Request(url,{method:'"+form.metodo.value+"',	parameters: {m: form.m.value, u: form.u.value, a: form.a.value, acao: form.acao.value"+demaisDados+"},onSuccess: processarOk,onFailure: processarErro});";
//	alert(comando);
	form.input.value = comando;
	eval(comando);
}



function processarOk(transport,json) {
	var form = document.getElementById('frm');
	var response = transport.responseText || "no response text";
	form.output.value = response;
}

function processarErro() {
	var response = req.responseText || "no response text";
	window.alert('Erro: ' + req.status);
}


function addComponent() {
	var form = document.getElementById('frm');
	var li = parseInt(form.nrcomponents.value);
	var line_nr = li+1;

	var ni = document.getElementById('tcomponents');
	var li = li+1;

	eval('oldType_'+line_nr+'=""');

	//      alert(li);

	var trIdName = 'component'+li+'_';
	var newtr = document.createElement('tr');
	var htmltxt = "";
	newtr.setAttribute("id",trIdName);
	oCell = document.createElement("td");
	oCell.setAttribute ("align","left");
	oCell.setAttribute ("width","5");
	htmltxt = "";
	htmltxt +="<a href='#bottom' onClick='removeComponent(\"component"+line_nr+"_\")'>excluir</a>";
	oCell.innerHTML =htmltxt;
	newtr.appendChild(oCell);
	oCell = document.createElement("td");
	htmltxt = "";

	htmltxt +="<input type='text' class='text' style='width:200px;' name='chave_"+line_nr+"' value='' />";
	oCell.innerHTML =htmltxt;
	newtr.appendChild(oCell);
	oCell = document.createElement("td");
	htmltxt = "";
	htmltxt +="<input type='text' class='text' style='width:250px' id='valor_"+line_nr+"' name='valor_"+line_nr+"' value='' />";
	oCell.innerHTML =htmltxt;
	newtr.appendChild(oCell);
	ni.appendChild(newtr);
	form.nrcomponents.value = li;


}

function removeComponent(tr_id) {
	var table_row = document.getElementById(tr_id);
	table = table_row.parentNode;
	table.removeChild(table_row);
	table.removeChild(table_row_description);
}
</script>
{/literal}
<form name='frm' id='frm' method="POST">
<input type="hidden" name="nrcomponents" value="0" />
<table >
	<tr>
		<td>Metodo</td>
		<td>
			<select name="metodo">
				<option value="GET">GET</option>
				<option value="POST">POST</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Parametros obrigatorios</td>
		<td>
			<table>
				<tr>
					<td>M</td>
					<td>U</td>
					<td>A</td>
					<td>Acao</td>
				</tr>
				<tr>
					<td><input type="text" size="20" name="m" value="{$m}"></td>
					<td><input type="text" size="20" name="u" value="{$u}"></td>
					<td><input type="text" size="30" name="a" value="{$a}"></td>
					<td><input type="text" size="30" name="acao" value="{$acao}"></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2">Parâmetros adicionais<br>
		<div>





      <table valign='top'>
      <tbody valign='top' id="tcomponents">
<tr>
      <th>
          &nbsp;
      </th>
      <th>
          <b>Nome</b>
      </th>
      <th>
          <b>Valor</b>
      </th>
</tr>
</tbody>
</table>


<table width="100%">
<tr>
	<td align="left" width="20">
 		<a href="#bottom" onClick="addComponent()">
 			Incluir
		</a>
	</td>
</tr>
</table>







		</div>
		</td>
	</tr>

	<tr>
		<td colspan="2">Código JSON a enviar (opcional)<br>
		<textarea name="input" rows="4" cols="80">{$input}</textarea></td>
	</tr>

	<tr>
		<td colspan="2"><input type="button" value="Enviar" onclick="processar()"></td>
	</tr>

	<tr>
		<td colspan="2">
		Codigo recebido:<br>
		<textarea name="output" rows="10" cols="80">{$output}</textarea></td>
	</tr>

</table>



</form>