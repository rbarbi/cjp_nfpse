{literal}
<script>

	function showFormCadGrupo(oid) {
		document.FrmMenuGrupo.acao.value = 'showFormCadGrupo';
		document.FrmMenuGrupo.submit();
	}

	function showFormListaGrupos(oid) {
		document.FrmMenuGrupo.acao.value = 'showFormListaGrupos';
		document.FrmMenuGrupo.submit();
	}



</script>
{/literal}

<form name='FrmMenuGrupo' action="index.php" method="POST">
	<input type="hidden" name="admin" value="1">
	<input type="hidden" name="m" value="acesso">
	<input type="hidden" name="a" value="SysGrupo.action">
	<input type="hidden" name="acao" value="">
	<input type="hidden" name="oid" value="">
</form>



<table>
	<tr>
		<th>Transacao</th>
		<th>Descricao</th>
	</tr>
	<tr>
		<td>
			<a href="#" onclick="showFormCadGrupo()">Incluir</a>
		</td>
		<td>
			Incluir um registro de grupo de usuários.
		</td>
	</tr>
	<tr>
		<td>
			<a href="#" onclick="showFormListaGrupos()">Listar</a>
		</td>
		<td>
			Listar Grupos de Usuários.
		</td>
	</tr>
</table>
<hr>
<br>
