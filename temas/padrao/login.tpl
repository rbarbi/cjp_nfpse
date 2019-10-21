<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="XHTML" content="VALIDO - http://validator.w3.org/check?uri=referer" />
<title>Sistema Administrativo - v4</title>
<link href="css/login_admin.css" rel="stylesheet" type="text/css" />

 <script src="js/lib/prototype.js" type="text/javascript"></script>
 <script src="js/src/scriptaculous.js" type="text/javascript"></script>
 <script src="js/src/unittest.js" type="text/javascript"></script>
</head>
<body>
<div id="centralizador">

<div id="base">
<div id="tudo">
<div id="topo"></div>

<!--Logo do Cliente aqui-->
<div id="logo"><img src="img/logocliente.gif" alt="Icone Design" /></div>
<!--Logo do Cliente aqui END-->

<div id="conteudo">
<div class="titulo"></div>
<div class="form">

<!--Formulario entrada-->
<form action="?" method="post" >
<div class="form_left"></div>
<div class="form_center"><input name="cd_login_usuario" type="text" value="Usu&aacute;rio:" class="user" onfocus="if(this.value=='Usu&aacute;rio:')this.value=''" onblur="if(this.value=='')this.value='Usu&aacute;rio:'" id="login" />
</div>
<div class="form_right"></div>
<div class="form_left"></div>
<div class="form_center"><input name="cd_senha_usuario" type="password" value="Senha:" class="senha"  id="pass" />
</div>
<div class="form_right"></div>
<div class="form_bt"><input name="" type="image" src="img/login_ok.gif" alt="Enviar Dados" class="bt" /></div>
</form>
<!--Formulario entrada End-->

</div>



<div style="color:red;text-align:center">{$msg}</div>


&nbsp;
<!-- Exibidor do lost account -->
<div id="lost_pass"><a id="toggle" href="#" onclick="Effect.toggle('lost_pass_hidden','slide'); return false;"><img src="img/login_esqueci_senha.gif" alt="Esqueci minha senha." /></a></div>


<div id="lost_pass_hidden" style="display:none;">
<form action="?" method="post" name="recupera" id="recupera">
<p class="lost_pass">Recupere sua senha.</p>
<input name="email" type="text" value="Insira seu e-mail:" class="email" onfocus="if(this.value=='Insira seu e-mail:')this.value=''" onblur="if(this.value=='')this.value='Insira seu e-mail:'" /><input name="envSenha" type="submit" value="" class="bt" />
</form>
</div>



</div>
<div id="footer"></div>

</div>
</div>
</div>
</body>
</html>
