{literal}
<script>

	function showFormAltUsuario(oid) {
		document.FrmUsuario.acao.value = 'showFormAltUsuario';
		document.FrmUsuario.oid.value = oid;
		document.FrmUsuario.submit();
	}

	function doDelUsuario(oid) {
		if (confirm('Deseja excluir este usuário?')) {
			document.FrmUsuario.acao.value = 'doDelUsuario';
			document.FrmUsuario.oid.value = oid;
			document.FrmUsuario.submit();
		}
	}

	function doReativarUsuario(oid) {
		if (confirm('Deseja reativar este usuário?')) {
			document.FrmUsuario.acao.value = 'doReativarUsuario';
			document.FrmUsuario.oid.value = oid;
			document.FrmUsuario.submit();
		}
	}


</script>
{/literal}

<form name='FrmUsuario' action="index.php" method="POST">
	<input type="hidden" name="admin" value="1">
	<input type="hidden" name="m" value="acesso">
	<input type="hidden" name="a" value="SysUsuario.action">
	<input type="hidden" name="acao" value="">
	<input type="hidden" name="oid" value="">
</form>

<table>
<tr>
	<th>&nbsp;</th>
	<th>Id</th>
	<th>Nome</th>
	<th>Username</th>
	<th>Nivel</th>
	<th>Status</th>
</tr>
{foreach from=$listaUsuarios item=usuario}
<tr>
	<td>
		<a href="#" onclick="showFormAltUsuario({$usuario->getId()})">Editar</a>
		{if $usuario->getStatusRegistro() == 'A'}
		<a href="#" onclick="doDelUsuario({$usuario->getId()})">Excluir</a>
		{else}
		<a href="#" onclick="doReativarUsuario({$usuario->getId()})">Reativar</a>
		{/if}
	</td>
	<td>{$usuario->getId()}</td>
	<td>{$usuario->getNome()}</td>
	<td>{$usuario->getUsername()}</td>
	<td>{$usuario->getNivelAcesso()}</td>
	<td>{$usuario->getStatusRegistro()}</td>
</tr>
{/foreach}
</table>