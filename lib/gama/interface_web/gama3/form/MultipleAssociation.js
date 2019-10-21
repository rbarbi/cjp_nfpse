gama3.form.MultipleAssociation = Ext.extend(Ext.form.Field, gama3.useInterface(gama3.form.InterfaceFieldFormAlt, {

	/**
	* @cfg configLockup {JSON} Objeto de configura��o do Lockup
	*/

	/**
	* @cfg fields {JSON[]} Campos que ser�o usados no grid que exibe os �tens associados
	*/

	/**
	* @cfg columns {JSON[]} Colunas que ser�o exibidas no GRID
	*/

	/**
	* @cfg serializeFunction {Function} Fun��o que serializa os dados que s�o enviados ao servidor
	*/

	/**
	* @cfg gridHeight {int} default: 140
	*/

	/**
	* @override
	*/
	hidden: true,

	/**
	* @override
	*/
	initComponent: function()
	{
		gama3.form.MultipleAssociation.superclass.initComponent.apply(this, arguments);

		//Inicializa Pain�is
		this.initPanelInclude();
		this.initGrid();
		this.initPanelMain();
	},

	/**
	* @override
	*/
	onRender: function()
	{
		gama3.form.MultipleAssociation.superclass.onRender.apply(this, arguments);

		this.wrap = this.el.wrap();
		this.panelMain.render(this.wrap);
		
		this.panelMain.getEl().child('.x-form-search-trigger').show();
	},

	/* ---- INIT �tens ---- */

	/**
	* Inicializa Panel com campo para inclus�o dos �tens
	*/
	initPanelInclude: function()
	{
		//Referencia MultipleAssociation
		this.configLockup.multipleAssociation = this;
		//Cria Lockup
		this.field = new gama3.form.MultipleAssociationLockup(this.configLockup);
		//Deleta cfg
		delete this.configLockup;
		//Quando for clicado no bot�o "Adicionar" executa addRecord
		this.field.on("multipleAssociationAdd", this.addRecord.createDelegate(this));

		//Cria Painel com campo de lockup e bot�o para adicionar os �tens
		this.panelInclude = new Ext.Panel({
			items: [
			this.field
			],
			region: "north",
			height: 30
		})
	},

	/**
	* Inicializa GRID
	*/
	initGrid: function()
	{
		//GRID com os �TENS Inclu�dos
		this.grid = new gama3.form.MultipleAssociationList({
			fields: this.fields,
			columns: this.columns,
			multipleAssociation: this,
			height: this.gridHeight || 140,
			serializeFunction: this.serializeFunction
		});

		delete this.fields
		delete this.columns;
	},

	/**
	* Inicializa Painel principal que conter� o campo de inclus�o e o grid
	*/
	initPanelMain: function()
	{
		//Panel Principal
		this.panelMain = new Ext.Panel({
			layout: "border",
			width: 540,
			height: (this.gridHeight || 140) + 40,
			items: [
			this.panelInclude,
			this.grid
			]
		});
	},

	/* ---- FIM INIT �tens ---- */

	/**
	* Adiciona Record ao Store da Listagem
	* @handler Evento "multipleAssociationAdd" do MultipleAssociationLockup
	* @param lockup {gama3.form.MultipleAssociationLockup} �tem que dispara o evento
	* @param record {Ext.data.Record} Record do �tem a ser inclu�do
	*/
	addRecord: function(lockup, record)
	{
		this.grid.getStore().add(record);
		//Limpa campo
		this.field.clear();		
		//Coloca os novos valores no campo hidden
		this.setValueInField();
		//Desabilita bot�o
		this.field.disableButton();

                this.grid.removeTabIndex();
	},

	/**
	* Remove Record da Listagem
	* @param record {Ext.data.Record}
	*/
	removeRecord: function(record)
	{
		this.grid.getStore().remove(record);
		this.setValueInField();

		var rec = this.field.getRecordSave();
		if(rec)
		this.field.isRecordInclude(null, rec);

                this.grid.removeTabIndex();
	},

	/**
	* Verifica se Record j� est� inclu�do no GRID
	* @param record {Ext.data.Record}
	* @return boolean
	*/
	isRecordInclude: function(record){
		if(this.grid.getStore().find("id", record.get("id")) != -1)
		return true;

		return false;
	},

	/* ---- Manipula��o do campo Hidden ---- */

	/**
	* Coloca o valor dos ids selecionados separados por ";" no campo hidden
	* Ex: "1;9;234;13"
	*/
	setValueInField: function()
	{
		var value = this.grid.getStore().serializeFunction ? this.grid.getStore().serializeFunction.apply(this.grid.getStore()) : this.grid.getStore().serialize();
		this.getEl().dom.value = value;
	},

	/**
	* @interface {gama3.form.InterfaceFieldFormAlt}
	*/
	showFormAltLoad: function(data)
	{
		this.grid.loadRecords(data);
		this.setValueInField();

                this.grid.removeTabIndex();
	}
}, "gama3.form.MultipleAssociation"));




gama3.form.MultipleAssociationLockup = Ext.extend(gama3.form.LockupField, {

	//Bot�o de Adicionar
	button: null,

	//Refer�ncia ao MultipleAssociation
	multipleAssociation: null,

	/**
	* @override
	*/
	initComponent: function()
	{
		gama3.form.MultipleAssociationLockup.superclass.initComponent.apply(this, arguments);

		this.addEvents(
		/**
		* @event multipleAssociationAdd
		* Lan�ado quando um �tem for adicionado
		* @param {gama3.form.LockupField}
		* @param {Record} �tem adicionado
		*/
		"multipleAssociationAdd");
	},

	/**
	* @override
	*/
	initEvents: function()
	{
		gama3.form.MultipleAssociationLockup.superclass.initEvents.apply(this, arguments);

		this.on("invalid", this.addMargin.createDelegate(this));
		this.on("valid", this.removeMargin.createDelegate(this));
		//Verifica se �tem selecionado j� existe no GRID
		this.on("save", this.isRecordInclude.createDelegate(this));
	},

	/**
	* Adiciona Bot�o para inclus�o do �tem na listagem
	* @override
	*/
	onRender: function()
	{
		gama3.form.MultipleAssociationLockup.superclass.onRender.apply(this, arguments);

		//Adiciona Bot�o "Adicionar"

		//Pega Wrap
		var el = this.getEl().up(".x-form-field-wrap");

		//Cria bot�o
		this.button = el.createChild({
			tag:"input",
			type:"button",
			value:"Adicionar",
			style: {
			"margin-left": "20px",
			height: "22px",
			"font-size": "12px",
			"vertical-align":"middle"
			},
			disabled: true
		});
		this.button.on("click", this.fireMultipleAssociationAdd.createDelegate(this));

		//Cria span de erro
		this.spanError = el.createChild({
			tag:"span",
			style: {
			"margin-left": "5px",
			height: "22px",
			"font-size": "11px",
			"vertical-align":"middle",
			color: "red"
			}
		});
	},

	/**
	* Lan�a evento para Inclus�o de �tem.
	* Executado quando o usu�rio clica no bot�o "Adicionar"
	* @fireEvent {multipleAssociationAdd}
	*/
	fireMultipleAssociationAdd: function()
	{
		this.fireEvent("multipleAssociationAdd", this, this.getRecordSave())
	},


	/**
	* Verifica se Record est� ou n�o inclu�do nos �tens selecionados
	* @handler Evento "Save" do pr�prio Lockup.
	* @param lockup {gama3.form.LockupField}
	* @param record {Ext.data.Record}
	*/
	isRecordInclude: function(lockup, record)
	{
		//Pergunta ao MultipleAssociation se �tem est�n inclu�do
		if(this.multipleAssociation.isRecordInclude(record))
		{
			//Se sim marfca erro e desabilita bot�o
			this.markError("O �tem selecionado j� est� inclu�do");
			this.disableButton();
		}
		else
		{
			//se n�o, limpa erro e habilita bot�o
			this.clearError();
			this.enableButton();
		}
		lockup.focus();
	},

	/* ---- Manipula��o do Bot�o --- */

	/**
	* Desabilita Bot�o "Adicionar"
	* @public
	*/
	disableButton: function()
	{
		this.button.dom.disabled = true;
	},

	/**
	* Habilita bot�o "Adicionar"
	* @public
	*/
	enableButton: function()
	{
		this.button.dom.disabled = false;
	},

	/* ---- Mensagens de Erro ---- */

	/**
	* Exibe mensagem de erro.
	* Obs. O espa�o dedicado a ela � pequeno e portanto alguns textos podem ser exibidos cortados
	* @param msg {string} Mensagem de erro a ser exibida
	*/
	markError: function(msg)
	{
		this.spanError.update("*"+msg);
	},

	/**
	* Limpa mensagem de erro
	* @public
	*/
	clearError: function()
	{
		this.spanError.update("");
	},

	/* ---- Controle de LAYOUT do Bot�o "Adicionar" ---- */

	/**
	* Adiciona Margim ao bot�o adicionar quando campo for maracado como inv�lido
	* @handler
	* @private
	*/
	addMargin: function()
	{
		this.button.set({style: {
		"margin-left": "36px"
		}})
	},

	/**
	* Remove margem extra quando campo for novamente marcado como v�lido
	* @handler
	* @private
	*/
	removeMargin: function()
	{
		this.button.set({style: {
		"margin-left": "20px"
		}})
	}
})



/**
* Listagfem do componente gama3.ux.AgileInclude
* @extend {Ext.grid.GridPanel}
*/
gama3.form.MultipleAssociationList = Ext.extend(Ext.grid.GridPanel, {

	Record: null,

	//Refer�ncia ao MultipleAssociation
	multipleAssocciation: null,

	initComponent: function()
	{
		//Cria Record que ser� usado pelos �tens inseridos na listagem
		this.Record = Ext.data.Record.create(this.fields);

		this.addControlColumns();

		this.createSelectionModel();

		Ext.apply(this, {
			width: 400,
			region: "center",
			frame: true,
			autoScroll: true,
			columns: this.columns,
			store: new gama3.form.MultipleAssociationStore({
				data: [],
				reader: new Ext.data.JsonReader({}, this.Record),
				serializeFunction: this.serializeFunction
			}),
			sm: this.cellSelectionModel
		});

		gama3.form.MultipleAssociationList.superclass.initComponent.apply(this, arguments);                
	},
        /**
         * Para evitar que as clickFunctios fossem disparadas pelo TAB do mouse do usu�rio
         * foram removidos todos os tabindex das c�lulas.
         * assim, o tab passa direto para o pr�ximo combo.
         * @public
         */
        removeTabIndex: function(){
            this.getEl().select(".x-grid3-cell").each(function(el){
                el.set({
                    tabIndex: -1
                })   
            }, this)
        },
	/**
	* Adiciona Colunas de Controle
	*/
	addControlColumns: function()
	{
		var columnsTemp = new Array();

		/*
		* Insere Colunas com �cone de Status e Edi��o � Listagem
		*/
		columnsTemp.push(
		//Delete
		{
			menuDisabled: true,
			hideable: false,
			header: "",
			clickFunction: this.clickFunctionRemove.createDelegate(this),
			width: 30,
			dataIndex:"id",
			renderer: this.rendererRemove.createDelegate(this)
		}
		);

		for(var i = 0; i< this.columns.length; i++)
		columnsTemp.push(this.columns[i]);

		this.columns = columnsTemp;
	},

	/**
	* Carrega um array de JSONS para o store do GRID
	* @param data {JSON[]}
	*/
	loadRecords: function(data)
	{
		var st = this.getStore();
		var rec;
		for(var i=0; i < data.length; i++)
		{
			rec = new this.Record(data[i]);
			st.add(rec);
		}
	},

	/* ---- clickFunction's ---- */

	/**
	* Caso Exista erro, o exibe em um Alert
	* @param record {Ext.data.Record}
	*/
	clickFunctionRemove: function(record)
	{	
            this.multipleAssociation.removeRecord(record);
	},

	/* ---- Renderer's ---- */

	/**
	* Renderiza Imagem correspondente ao Status
	*/
	rendererRemove: function(val)
	{
		return "<img style=\"cursor: pointer\" src='./lib/gama/interface_web/gama3/resources/img/icones/excluir.png' alt='Deletar' />"
	},

	/* ---- Modelo de Sele��o ---- */

	/**
	* Cria modelo de Sele��o do GRID
	* Este modelo � por c�lula e execua a fun��o do atributo "clickFunction" da coluna
	* enviando o record da linha como par�metro
	*/
	createSelectionModel: function()
	{
		//Modelo de Sele��o de C�lula
		this.cellSelectionModel = new Ext.grid.CellSelectionModel({
			singleSelect: true,
			listeners: {
				beforecellselect:  { fn: this.beforeCellSelect.createDelegate(this) },
				cellselect:  { fn: this.cellSelect.createDelegate(this) }
			}
		})
	},

	/*
	* Quando uma c�lula for clicada, verifica se existe o atributo "clickFunction" para a respectiva coluna.
	* Este hanlder tamb�m evita clicks em c�lulas que n�o possuam nenhuma fun��o cadastrada.
	* @param {Ext.RowSelectionModel} sm
	* @param {int} rowIndex �ndice da linha selecionada
	* @param {int} colIndex �ndice da coluna selecionada
	*/
	beforeCellSelect: function(sm, rowIndex, colIndex)
	{
		//Pega objeto da coluna clicada.
		var coluna = this.getColumnModel().getColumnById(colIndex);

		//Se existe, continua evento de sele��o, se n�o, o pausa.
		if(coluna.clickFunction)
		return true;
		else
		return false;
	},

	/*
	* executa fun��o "clickFunction" de uma coluna.
	* "clickFunction" receber� como atributo o Record do objeto clicado.
	* @param {Ext.RowSelectionModel} sm
	* @param {int} rowIndex �ndice da linha selecionada
	* @param {int} colIndex �ndice da coluna selecionada
	*/
	cellSelect: function(sm, rowIndex, colIndex)
	{            
		//Pega objeto da coluna clicada.
		var coluna = this.getColumnModel().getColumnById(colIndex);
		//Pega Record
		var record = this.getStore().getAt(rowIndex);
		//Executa Fun��o
		coluna.clickFunction(record);
	}

});


/**
* MultipleAssociationStore
* Classe Utilizada para o Store do Grid do Multiple association
*/
gama3.form.MultipleAssociationStore = Ext.extend(Ext.data.Store, {

	/**
	* Serializa os �tens do GRID pelo ID para o formato "1;4;2;54;3445"
	* @return {string}
	*/
	serialize: function()
	{
		var s = "";
		this.each(function(obj){
			if( (this.getCount()-1) == this.indexOf(obj))
			s += obj.get("id");
			else
			s += obj.get("id")+";";
		}, this);
		return s;
	}
})