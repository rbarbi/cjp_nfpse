var idDlgVisualizar=0;
function criarDlgVisualizar(){		
	idDlgVisualizar++;		
	//busca o elemento html que foi inserido pelo smarty
	var dlgView = document.getElementById("dlgVisualizar");		
	//muda o id da dialog para que possam existir varias dialogs
	dlgView.id = dlgView.id + idDlgVisualizar;	
		
	// cria a dialog	
	dlgVisualizar = new YAHOO.widget.Dialog(dlgView.id,
	{  width : "200px", 
	fixedcenter : true,
	constraintoviewport : true
	});	
	// plota a dialog
	dlgVisualizar.render();		
}
