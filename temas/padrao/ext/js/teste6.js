
Ext.onReady(function() {




	var menuCadastro = new Ext.menu.Menu({
		text:'meu Menu',
		items: [{
			text: 'Veiculos',
			menu: [{
				text: 'Fabricante',
				handler: doShowFormCadFabricante
			}, {
				text: 'Modelo',
				handler: clickHandler
			}
			]
		},
		new Ext.menu.Item({
			text: 'Tanques de Caminh&otilde;es',
			handler: clickHandler,
			menu:[{
				text: 'Fabricante',
				handler: clickHandler
			},{
				text: 'Modelo',
				handler: clickHandler
			}]
		}),
		'-',
		new Ext.menu.CheckItem({
			text: 'A check item',
			checkHandler: checkHandler
		}),
		new Ext.menu.CheckItem({
			text: 'Another check item',
			checkHandler: checkHandler
		})
		]
	});
	
	
	

	var itemMenuCadastro = new Ext.menu.Item({
		text:'meu item Menu',
		menu: [{
			text: 'Veiculo',
			handler: doShowFormCadFabricante,
			menu: [
			{
				text: 'Fabricante',
				handler: doShowFormCadFabricante
			},'-',{
				text: 'Modelo',
				handler: doShowFormCadFabricante
			}]
		}, {
			text: 'Tanque',
			handler: clickHandler
		}
		]
	});





	function renderMensagem(tipo){
		var urlBase = 'http://localhost/dev/dotproject/images/';
		switch (tipo) {
			case 1: url = urlBase + 'log-notice.gif'; break;
			case 2: url = urlBase + 'log-info.gif'; break;
			case 3: url = urlBase + 'log-error.gif'; break;
		}
		return '<img src="' + url + '">';
	}




	var myData = [
	[1,1,'2008-12-04 12:01:03','Eduardo logou no sistema',100],
	[2,2,'2008-12-04 12:05:45','Erro no sistema',60],
	[3,3,'2008-12-04 12:05:45','Eduardo saiu no sistema',10]
	];



	var store = new Ext.data.SimpleStore({
		fields: [
		{name: 'id'},
		{name: 'tipoMensagem'},
		{name: 'dhMensagem', type:'date', dateFormat:'Y-j-d h:i:s'},
		{name: 'mensagem'},
		{name: 'feito'}
		]
	});
	store.loadData(myData);

	var sm = new Ext.grid.ProgressBarSelectionModel({header: "Completado", text:'%', dataIndex:'feito'});





	var window = new Ext.ux.iasoft.MinhaJanela({
		items: new Ext.grid.GridPanel({
			store: store,
			columns: [
			{id:'id',header: "#", width: 20, sortable: false, dataIndex: 'id'},
			{header: "Tipo", width: 35, renderer: renderMensagem, dataIndex: 'tipoMensagem'},
			{header: "Data/Hora", width: 95, sortable: true, dataIndex: 'dhMensagem', renderer: Ext.util.Format.dateRenderer('d/m/Y h:i')},
			{header: "Mensagem", width: 320, sortable: true, dataIndex: 'mensagem'},
			sm
			],
			stripeRows: true,
			height:350,
			width:600,
			title:'Mensagens do sistema'
		})
	});




	
	
	window.show();
	
	function clickHandler() {
		//		window.stbAlerta.setText('aa');
		Ext.log('Clicou no menu');
		alert('Clicked on a menu item');
	}

	function doShowFormCadFabricante() {
		alert('Abrindo o formulario');
	}

	function checkHandler() {
		alert('Checked a menu item');
	}

	
	window.incluiComponente({
	text: 'Menu',
	menu : menuCadastro
	});


	menuCadastro.addSeparator();

	var item = menuCadastro.add({
	text: 'Dynamically added Item'
	});	



	window.incluiItemMenu(itemMenuCadastro);
	window.addBotaoLogotipo();

});