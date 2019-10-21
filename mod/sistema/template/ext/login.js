
/*
Aqui vai a lista de empresas, que depois deverá ser substituída por um
objeto que faz a consulta a um Action, que retornará uma string Json
com estes dados;
*/
Ext.listaEmpresas = [
['1', 'IASoft'],
['2', 'LogTruck']
]






/*
Construtor...
*/
Ext.onReady(function(){

	// Inicializa a opção dos alertas nos campos
	Ext.QuickTips.init();

	Ext.form.Field.prototype.msgTarget = "side"; // Aqui defino que os alertas devem aparecer ao lado dos campos






	// Campo com a lista de empresas.
	var cpEmpresa = new Ext.form.ComboBox({
		fieldLabel: 'Empresa',
		hiddenName:'cdEmpresa',
		store: new Ext.data.SimpleStore({
			fields: ['id', 'nome'],
			data : Ext.listaEmpresas // from states.js
		}),
		valueField:'id',
		displayField:'nome',
		typeAhead: true,
		mode: 'local',
		triggerAction: 'all',
		emptyText:'Selecione uma empresa',
		selectOnFocus:true,
		width:190
	}
	);

	// Campo de username do login. É Obrigatório.
	var cpLoginUsername = new Ext.form.TextField({
		fieldLabel:'Username',
		name:'loginUsername',
		allowBlank:false,
		blankText: 'Favor preencher com seu username'
	}
	);

	// Campo de senha do login. É Obrigatório.
	var cpLoginSenha = new Ext.form.TextField ({
		fieldLabel:'Senha',
		name:'loginPassword',
		allowBlank:false,
		inputType:'password',
		blankText: 'A senha &eacute; obrigat&oacute;ria'
	});





	// Create a variable to hold our EXT Form Panel.
	// Assign various config options as seen.
	var login = new Ext.FormPanel({
		labelWidth:80,
		url:'login.php',
		frame:true,
		title:'Acesso LogTruck',
		defaultType:'textfield',
		monitorValid:true,
		// Specific attributes for the text fields for username / password.
		// The "name" attribute defines the name of variables sent to the server.
		items:[
		cpEmpresa,
		cpLoginUsername,
		cpLoginSenha
		],

		// All the magic happens after the user clicks the button
		buttons:[{
			text:'Login',
			formBind: true,
			// Function that fires when user clicks the button
			handler:function(){
				login.getForm().submit({
					method:'POST',
					waitTitle:'Comunicando',
					waitMsg:'Conectando...',

					// Functions that fire (success or failure) when the server responds.
					// The one that executes is determined by the
					// response that comes from login.asp as seen below. The server would
					// actually respond with valid JSON,
					// something like: response.write "{ success: true}" or
					// response.write "{ success: false, errors: { reason: 'Login failed. Try again.' }}"
					// depending on the logic contained within your server script.
					// If a success occurs, the user is notified with an alert messagebox,
					// and when they click "OK", they are redirected to whatever page
					// you define as redirect.

					success:function(form, action){
						obj = Ext.util.JSON.decode(action.response.responseText);

						Ext.Msg.alert('Status', 'Acesso permitido! '+obj.empresa, function(btn, text){
							if (btn == 'ok'){
								var redirect = '../desktop2/desktop.php?cdEmpresa='+obj.empresa;
								window.location = redirect;
							}
						});
					},

					// Failure function, see comment above re: success and failure.
					// You can see here, if login fails, it throws a messagebox
					// at the user telling him / her as much.

					failure:function(form, action){
						if(action.failureType == 'server'){
							obj = Ext.util.JSON.decode(action.response.responseText);
							titulo = 'Login falhou!';
							msg = obj.errors.reason;
						} else {
							titulo = 'Warning!';
							msg = 'Authentication server is unreachable : ' + action.response.responseText;
						}

						Ext.Msg.show({
							title:titulo,
							msg: msg,
							buttons: Ext.Msg.OK,
							fn: function(){login.inicializar();},
							animEl: 'elId',
							icon: Ext.MessageBox.WARNING
						});
					}
				});
			}
		}],
		inicializar:function() {
			//login.getForm().reset();
			cpLoginUsername.focus();
		}
	});


	// This just creates a window to wrap the login form.
	// The login object is passed to the items collection.
	var win = new Ext.Window({
		layout:'fit',
		width:300,
		height:180,
		closable: false,
		resizable: false,
		plain: true,
		border: false,
		items: [login]
	});
	win.show();
});