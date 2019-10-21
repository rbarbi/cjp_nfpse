<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gama Info</title>
</head>
<body>
{literal}
<style>

vazio {
	border:none !important;
	text-align:center !important;
}
.t {
background-color:#dddddd;
color:#000000;
font-weight:bold;
}
</style>
{/literal}
	<div class="center">
		<table width="600px"  cellpadding="3" border="0">
		<tbody>
			<tr>
				<td>
					<a href="http://www.iasoft.com.br/">
					<img border="0" alt="Gama" src="./temas/padrao/img/logo-iasoft.gif"/>
					</a>
					<h1 class="p">Gama versao {$ver.versao}</h1>
				</td>
			</tr>
		</tbody>
		</table>
	</div>
	<HR>
	<div class="center">
		<table width="600px"  cellpadding="3" border="0">
		<tbody>

			<tr>
				<td class="t" colspan="2">
					DADOS DA REQUISICAO
				</td>
			</tr>


			<tr>
				<td class="e">
					m
				</td>
				<td class="v">
					{$app->getM()}
				</td>
			</tr>

			<tr>
				<td class="e">
					u
				</td>
				<td class="v">
					{$app->getU()}
				</td>
			</tr>

			<tr>
				<td class="e">
					a
				</td>
				<td class="v">
					{$app->getA()}
				</td>
			</tr>

			<tr>
				<td class="e">
					acao
				</td>
				<td class="v">
					{$app->getAcao()}
				</td>
			</tr>

			<tr>
				<td class="e">
					url
				</td>
				<td class="v">
					{$server.SCRIPT_NAME}
				</td>
			</tr>

		</tbody>
		</table>
	</div>


<HR>
	<div class="center">
		<table width="600px"  cellpadding="3" border="0">
		<tbody>

			<tr>
				<td class="t" colspan="2">
					PARAMETROS DE REQUISICAO
				</td>
			</tr>

		{foreach from=$listaParmRequest item=valor key=chave}
			<tr>
				<td class="e">
					{$chave}
				</td>
				<td class="v">
					{$valor}
				</td>
			</tr>
		{/foreach}
		</tbody>
		</table>
	</div>


<HR>
	<div class="center">
		<table width="600px"  cellpadding="3" border="0">
		<tbody>

			<tr>
				<td class="t" colspan="2">
					PARAMETROS DE CONFIGURACAO
				</td>
			</tr>

		{foreach from=$listaParmConfig item=valor key=chave}
			<tr>
				<td class="e">
					{$chave}
				</td>
				<td class="v">
					{$valor}
				</td>
			</tr>
		{/foreach}
		</tbody>
		</table>
	</div>



<HR>
	<div class="center">
		<table width="600px"  cellpadding="3" border="0">
		<tbody>

			<tr>
				<td class="t" colspan="2">
					PARAMETROS DA APLICACAO (config de desenvolvimento)
				</td>
			</tr>

		{foreach from=$listaParmManifesto item=valor key=chave}
			<tr>
				<td class="e">
					{$chave}
				</td>
				<td class="v">
					{$valor}
				</td>
			</tr>
		{/foreach}
		</tbody>
		</table>
	</div>

	<HR>
	<div class="center">
		<table width="600px"  cellpadding="3" border="0">
		<tbody>

			<tr>
				<td class="t" colspan="2">
					DADOS DA VERSAO
				</td>
			</tr>


			<tr>
				<td class="e">
					revisao
				</td>
				<td class="v">
					{$ver.versao}
				</td>
			</tr>

			<tr>
				<td class="e">
					URL SVN
				</td>
				<td class="v">
					{$ver.url}
				</td>
			</tr>

			<tr>
				<td class="e">
					Comandos admitidos (cmdGamaAdmin)
				</td>
				<td class="v">
				{foreach from=$lsComandosAdministrativos item=valor}
					{$valor} <br />
				{/foreach}
				</td>
			</tr>


		</tbody>
		</table>
	</div>
	<hr />
	{*
	<div class="center">
		<table width="600px"  cellpadding="0" border="0">
			<tbody>
				<tr class="vazio">
					<td class="vazio"><img src="./temas/padrao/img/bt_javascript.png" /></td>
					<td class="vazio"><img src="./temas/padrao/img/bt_xhtml_css.png" /></td>
					<td class="vazio"><img src="./temas/padrao/img/bt_php.png" /></td>
					<td class="vazio"><img src="./temas/padrao/img/bt_postgres.png" /></td>
					<td class="vazio"><img src="./temas/padrao/img/bt_gama-peq.png" /></td>
				</tr>
			</tbody>
		</table>
	</div>
*}
</body>
</html>
