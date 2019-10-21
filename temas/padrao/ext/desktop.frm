<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Sistema da IASoft</title>

	<script>
	var nomeUsuarioLogado = '<<$nomeUsuario>>';
	</script>


    <link rel="stylesheet" type="text/css" href="./lib/extJS/resources/css/ext-all.css" />
    <!-- GC -->
 	<!-- LIBS -->
 	<script type="text/javascript" src="./lib/extJS/adapter/ext/ext-base.js"></script>
 	<!-- ENDLIBS -->

    <script type="text/javascript" src="./lib/extJS/ext-all.js"></script>

    <!-- DESKTOP -->


    <script type="text/javascript" src="./temas/padrao/ext/js/StartMenu.js"></script>
    <script type="text/javascript" src="./temas/padrao/ext/js/TaskBar.js"></script>
    <script type="text/javascript" src="./temas/padrao/ext/js/Desktop.js"></script>
    <script type="text/javascript" src="./temas/padrao/ext/js/App.js"></script>
    <script type="text/javascript" src="./temas/padrao/ext/js/Module.js"></script>
    <script type="text/javascript" src="./temas/padrao/ext/js/IASWindow3.js"></script>
    
    <!--<script type="text/javascript" src="./temas/padrao/ext/js/ipfield.js"></script>-->
    <!--<script type="text/javascript" src="./temas/padrao/ext/js/timefield.js"></script>-->
    <script type="text/javascript" src="./temas/padrao/ext/js/fieldpanel.js"></script>
    
    <script type="text/javascript" src="./temas/padrao/ext/js/ProgressBarSelectionModel.js"></script>

    
<script type="text/javascript">
<<include file="ext/desktop.tjs">>
</script>

    <script type="text/javascript" src="./lib/gama/extJS/terceiros/recordform/js/Ext.ux.IconMenu.js"></script>
    <script type="text/javascript" src="./lib/gama/extJS/terceiros/recordform/js/Ext.ux.form.DateTime.js"></script>
    <script type="text/javascript" src="./lib/gama/extJS/terceiros/recordform/js/Ext.ux.grid.RecordForm.js"></script>
    <script type="text/javascript" src="./lib/gama/extJS/terceiros/recordform/js/Ext.ux.grid.RowActions.js"></script>
    <script type="text/javascript" src="./lib/gama/extJS/terceiros/recordform/js/Ext.ux.grid.Search.js"></script>

    <script type="text/javascript" src="./lib/gama/extJS/terceiros/webblocks/Ext.ux.RecordFormGrid.js"></script>
    
    <script type="text/javascript" src="./temas/padrao/ext/desktop.php?js=<<$jsAIncluir>>"></script>


    <link rel="stylesheet" type="text/css" href="./temas/padrao/ext/css/desktop.css" />
<<foreach from=$listaCSS item=itemCSS>>
	<link rel="stylesheet" type="text/css" href="<<$itemCSS>>" />
<</foreach>>    

</head>
<body scroll="no">

<div id="x-desktop">
    <a href="http://www.iasoft.com.br" target="_blank" style="margin:5px; float:right;"><img src="./temas/padrao/ext/images/Logo-Iasoft.png" /></a>

    <dl id="x-shortcuts">
<<*    
        <dt id="grid-win-shortcut">
            <a href="#"><img src="./temas/padrao/ext/images/s.gif" />
            <div>Grid Window</div></a>
        </dt>
        <dt id="acc-win-shortcut">
            <a href="#"><img src="./temas/padrao/ext/images/s.gif" />
            <div>Accordion Window</div></a>
        </dt>
        <dt id="teste-win-shortcut">
            <a href="#"><img src="./temas/padrao/ext/images/s.gif" />
            <div>Janela de Teste</div></a>
        </dt>
*>>
<<foreach from=$listaAplicacoes item=aplicacao>>
        <dt id="<<$aplicacao.id>>-win-shortcut">
            <a href="#"><img src="./temas/padrao/ext/images/s.gif" />
            <div><<$aplicacao.label>></div></a>
        </dt>
<</foreach>>
    </dl>
</div>

<div id="ux-taskbar">
	<div id="ux-taskbar-start"></div>
	<div id="ux-taskbuttons-panel"></div>
	<div class="x-clear"></div>
</div>

</body>
</html>
