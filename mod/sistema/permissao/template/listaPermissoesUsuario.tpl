{if count($lista) > 0}
<html>
<head>
<script src="./lib/prototype.js" language="javascript"></script>
<script src="./lib/gama3.js" language="javascript"></script>
{literal}
<script>
<!--
function exibirPermissao(usuID, mm, uu, aa, acao2) {
//	window.alert('permID='+permID+'&usuID='+usuID);
	var s = '';
	s += getts('usuID',usuID);
	s += getts('mm',mm);
	s += getts('uu',uu);
	s += getts('aa',aa);
	s += getts('acao2',acao2);
	doGoTo('{/literal}{$_adm_m}{literal}','autorizacao','SysAutorizacao','showIndex',s);
}

function excluirPermissao(peID) {
	var s = getts('peID',peID);
	doGoTo('{/literal}{$_adm_m}{literal}','permissao','SysPermissaoUsuario','doDelPermissaoUsuario',s);
}
-->
</script>
{/literal}
</head>
<body>
<table border="1">
	<tr>
		<th colspan="1">&nbsp;</th>
		<th>Nome</th>
		<th>Permissao</th>
	</tr>
	{foreach from=$lista item=vo}
	<tr>
		<td><a href='javascript:excluirPermissao("{$vo->getID()}")'>excluir</a></td>
		<td>{$vo->getNomeTransacao()|escape:"htmlall"}</td>
		<td>{$vo->getPermissao()|escape:"htmlall"}</td>
	</tr>
	{/foreach}
</table>
</body>
</html>
{else}
Nenhum registro encontrado.
{/if}