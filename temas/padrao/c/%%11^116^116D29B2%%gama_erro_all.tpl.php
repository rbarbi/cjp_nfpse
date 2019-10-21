<?php /* Smarty version 2.6.18, created on 2016-05-24 21:23:03
         compiled from gama_erro_all.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Erro</title>
</head>
<body>
<?php echo '
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
.e {
background-color:#efefef;
color:#000000;
font-weight:bold;
text-align:right;
vertical-align:top;
}
.v {
text-align:left;
}
.center {
	text-align:center;
}
</style>
'; ?>


	<div class="center">
	<center>
		<table width="800px"  cellpadding="3" border="0">
		<tbody>

			<tr>
				<td class="t" colspan="2">
					<?php echo $this->_tpl_vars['errtype']; ?>

				</td>
			</tr>


			<tr>
				<td class="e">
					Mensagem:
				</td>
				<td class="v">
					<strong><?php echo $this->_tpl_vars['errstr']; ?>
</strong>
				</td>
			</tr>

			<tr>
				<td class="e">
					Arquivo:
				</td>
				<td class="v">
					<?php echo $this->_tpl_vars['errfile']; ?>
 <br>
					<?php echo $this->_tpl_vars['errline']; ?>

				</td>
			</tr>

			<tr>
				<td class="e">
					Modulo:
				</td>
				<td class="v">
					<?php echo $this->_tpl_vars['app']->getM(); ?>

				</td>
			</tr>

			<tr>
				<td class="e">
					SubModulo:
				</td>
				<td class="v">
					<?php echo $this->_tpl_vars['app']->getU(); ?>

				</td>
			</tr>
			<tr>
				<td class="e">
					Action:
				</td>
				<td class="v">
					<?php echo $this->_tpl_vars['app']->getA(); ?>

				</td>
			</tr>
			<tr>
				<td class="e">
					Transacao:
				</td>
				<td class="v">
					<?php echo $this->_tpl_vars['app']->getAcao(); ?>

				</td>
			</tr>

			<tr>
				<td class="e">
					URL:
				</td>
				<td class="v">
					<?php echo $this->_tpl_vars['server']['SCRIPT_NAME']; ?>

				</td>
			</tr>

			<tr>
				<td class="e">
					Parametros
				</td>
				<td class="v">
<?php $_from = $this->_tpl_vars['lsParms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['chave'] => $this->_tpl_vars['valor']):
?>
					<?php echo $this->_tpl_vars['chave']; ?>
 = <?php echo $this->_tpl_vars['valor']; ?>
<br />
<?php endforeach; endif; unset($_from); ?>
				</td>
			</tr>
			<tr>
				<td class="t" colspan="2">
					Configuracoes/versao
				</td>
			</tr>
			<tr>
				<td class="e">
					Gama versao:
				</td>
				<td class="v">
					<?php echo $this->_tpl_vars['ver']['versao']; ?>

				</td>
			</tr>

<?php $_from = $this->_tpl_vars['listaParmConfig']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['chave'] => $this->_tpl_vars['valor']):
?>
			<tr>
				<td class="e">
					<?php echo $this->_tpl_vars['chave']; ?>

				</td>
				<td class="v">
					<?php echo $this->_tpl_vars['valor']; ?>

				</td>
			</tr>
		<?php endforeach; endif; unset($_from); ?>


		</tbody>
		</table>

		</center>
	</div>

