<?php /* Smarty version 2.6.18, created on 2016-01-08 14:26:07
         compiled from areaLogin.tpl */ ?>
<form>
	<label>Login/OAB e UF</label>
	<input type="text" name='username' id='username' class="campo" />

	<label>Senha</label>
	<input type="password" name='senha' id='senha'  class="campo" />

	<input type="button" id='btAutenticar' value="Entrar" class="botao" style='border:0px' />

	<a href="index.php?m=iadoc&u=main&a=ClienteSistema&acao=showFormEsqueciMinhaSenha" rel="prettyPopin">Esqueci minha Senha</a>
	<a href="index.php?m=iadoc&u=main&a=ClienteSistema&acao=showFormEsqueciMeuLogin" rel="prettyPopin">Esqueci meu Login</a>
	<a href="index.php?m=iadoc&u=main&a=Index&acao=downloadManual">Manual de uso</a>
</form>