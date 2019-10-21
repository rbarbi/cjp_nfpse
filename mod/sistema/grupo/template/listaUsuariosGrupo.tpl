{if count($lista) > 0}
<html>
<head>
<script src="./lib/prototype.js" language="javascript"></script>
<script src="./lib/gama3.js" language="javascript"></script>
{literal}
<script>
<!--

function excluirUsuarioGrupo(usuarioID,grupoID) {
//	alert(usuarioID+" "+grupoID);
	var s = getts('usuID',usuarioID);
	    s += "&"+getts('grupoID',grupoID);
	doGoTo('{/literal}{$_adm_m}{literal}','grupo','SysGrupo','doDelUsuarioGrupo',s);
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
		<th>Nivel</th>
	</tr>
	{foreach from=$lista item=vo}
	<tr>
		<td><a href='javascript:excluirUsuarioGrupo({$vo->getUsuarioID()},{$vo->getGrupoID()})'>excluir</a></td>
		<td>{$vo->getNomeUsuario()|escape:"htmlall"}</td>
		<td>{$vo->getNivel()}</td>
	</tr>
	{/foreach}
</table>
</body>
</html>
{else}
Nenhum registro encontrado.
{/if}