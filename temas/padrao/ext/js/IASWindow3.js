

Ext.namespace('Ext.ux.iasoft');



Ext.ux.iasoft.MeuForm = function(config) {
	Ext.apply(this,{
		bodyStyle: 'padding: 5 5 5 5',
		style: 'padding: 5 5 5 5',
		url:'index.php',
		m:'sistema',
		u:'usuario',
		a:'SysUsuario',
		/*acao:'doCadUsuario2',*/
		acao:'showIndex',
		gravar : function() {
			//				alert('Enviando para '+this.url);
			var urlAux = this.url + '?' + 'm='+this.m+'&u='+this.u+'&a='+this.a+'&acao='+this.acao;
			this.getForm().submit({
				method:'POST',
				waitTitle:'Comunicando',
				waitMsg:'Gravando... ' + urlAux,
				url : urlAux,
				success:this.success,
				failure:this.failure
			})

		},
		success : function() { alert('ok'); },
		failure:function(form, action){

			var failureMessage = "Um erro tentando gravar os dados.";

			if (action.failureType == Ext.form.Action.LOAD_FAILURE) {
				failureMessage = action.result.message;
			} else if (action.failureType == Ext.form.Action.CONNECT_FAILURE) {
				failureMessage = "Favor avisar o suporte do seguinte erro: " +
				"Status: " + action.response.status +
				", Mensagem: " + action.response.statusText;
			} else if (action.failureType == Ext.form.Action.SERVER_INVALID) {
				failureMessage = action.result.errors.message;
			} else if (action.failureType == Ext.form.Action.CLIENT_INVALID) {
				failureMessage = "Favor preencher todos os campos obrigatorios";
			} else {
				failureMessage = action.result.errors.message;
			}

			Ext.MessageBox.alert('Mensagem de erro:', failureMessage);
		}

	}
	)
	Ext.ux.iasoft.MeuForm.superclass.constructor.apply(this,arguments);
};

Ext.extend(Ext.ux.iasoft.MeuForm, Ext.form.FormPanel, {});

/*
#################################################################################
##########
#################################################################################
*/







Ext.ux.iasoft.MeuMenu = function(config) {
	Ext.apply(this,{
		collapseMode: 'mini',
		collapsible: true,
		split: true,
		autoHeight : true,
		useArrows: true,
		rootVisible: false,
		margins: '5 5 5 5',
		draggable : false,
		bodyStyle:'padding:3px',
		valor : '',		
		getValor : function() {
			return this.valor;
		}
	})
	Ext.ux.iasoft.MeuMenu.superclass.constructor.apply(this,arguments);
};

Ext.extend(Ext.ux.iasoft.MeuMenu, Ext.tree.TreePanel, {
	onRender: function(){
		Ext.ux.iasoft.MeuMenu.superclass.onRender.apply(this, arguments);
	}
});





/*
#################################################################################
##########
#################################################################################
*/






Ext.ux.iasoft.MinhaToolbar = function(config) {
	Ext.apply(this,{})
	Ext.ux.iasoft.MinhaToolbar.superclass.constructor.apply(this,arguments);
};

Ext.extend(Ext.ux.iasoft.MinhaToolbar, Ext.Toolbar, {
	onRender: function(){
		Ext.ux.iasoft.MinhaToolbar.superclass.onRender.apply(this, arguments);
	}
});





/*
#################################################################################
##########
#################################################################################
*/






Ext.ux.iasoft.MinhaJanela = function(config) {
	Ext.apply(this,{
		iconeAjuda:true,
		title: 'Aplicacao',
		width: 600,
		height:350,
		minWidth: 300,
		minHeight: 200,
		layout: 'fit',
		plain:true,
		minimizable : true,
		bodyStyle:'padding:5px;',
		buttonAlign:'center',
		tbar: new Ext.ux.iasoft.MinhaToolbar({
			/*		items : [{
			text : 'Menu Principal',
			menu : this.mnu
			}
			],
			*/		height:32
		}),
		bbar: new Ext.StatusBar({
			id: 'app-status-bar',
			defaultText: 'Default status',
			items: ['-','    ',this.stbAlerta]
		}),
		ajuda : function(){
			alert('ajuda');
		}

	});
	this.addEvents('ajuda');
	//	this.stbAlerta.on('click',function(btn,source) {alert(btn.descricao);});
	this.stbAlerta.on('click',this.hndClickBtnAlerta);
	Ext.ux.iasoft.MinhaJanela.superclass.constructor.apply(this,arguments);
};


Ext.extend(Ext.ux.iasoft.MinhaJanela, Ext.Window, {
	initTools : function () {
		Ext.ux.iasoft.MinhaJanela.superclass.initTools.call(this);
		if (this.iconeAjuda) {
			this.addTool({
				id: 'ajuda',
				iconCls:'ajuda',
				hidden:false
				, handler: this.ajuda.createDelegate(this, [])
			});
		}
	},
	hndClickBtnAlerta:function(btn,source) {
		alert(btn.descricao);
	},
	stbAlerta : new Ext.Button({
		id : 'idAlerta',
		itemId : 'idAlerta',
		text : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
		descricao:'-'/*,
		onClick : function() {
		alert(this.descricao);
		}*/
	}),
	mnu : new Ext.menu.Menu(),

	iconeLogotipo : {
		xtype:'panel',
		cls:'btn-logotipo',
		overCls:'btn-logotipo-over',
		text:'',
		width:30,
		heigth:30
	},
	incluiComponente: function (componente) {
		this.getTopToolbar().add(componente);
	},

	incluiItemMenu : function(menu) {
		this.mnu.addItem(menu);
	},
	addBotaoLogotipo : function() {
		this.getTopToolbar().addFill();
		this.getTopToolbar().add(this.iconeLogotipo);
	},
	afterRender : function(){
		Ext.ux.iasoft.MinhaJanela.superclass.afterRender.call(this);
	}
});