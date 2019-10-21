gama3.form.AgileIncludeField = Ext.extend(Ext.form.Field, {

	/**
	* @cfg
	*/
	configForm: null,
	/**
	* @cfg
	*/
	configList: null,
	/*
	* Fun��o que serializa os dados do GRID
	* @cfg
	* @scope grid
	*/
	serializeFunction: null,
	/**
	* @cfg
	*/
	heightPanel: null,
	/**
	* @cfg
	*/
	widthPanel: null,
        /**
         * @cfg String usada para separar �tens da listagem no envio ao servidor
         */
        itemSeparator: "<;>",
        /**
         * @cfg String usada para separar valores dos �tens da listagem no envio ao servidor
         */
        valueSeparator: "<,>",
	/**
	* @public
	*/
	form: null,
	/**
	* @public
	*/
	list: null,
	/**
	* @override
	*/
	hidden: true,
        /**
         * Record salvo quando houver algum �tem em edi��o
         * @type {Ext.Data.Record}
         * @private
	 */
	recordEdit: null,
        /**
	 * @override
	 */
	initComponent: function(){
		gama3.form.AgileIncludeField.superclass.initComponent.apply(this, arguments);               
                
		//Inicializa Pain�is
		this.initPanel();
                //Pega evento do main que informa quando o formul�rio foi limpo
                this.relayEvents(this.form, ["clearForm"]);
                //Pega eventos da Listagem para informar que os campos do formul�rio foram carregados e quando s�o limpos
		this.relayEvents(this.list, ["loadEdit", "clearList"]);

                //Antes de inserir um �tem, lan�a evento
		this.addEvents("beforeInsert");
	},
	/**
	* @override
	*/
	onRender: function(){
		gama3.form.AgileIncludeField.superclass.onRender.apply(this, arguments);

		this.wrap = this.el.wrap();
		this.panelMain.render(this.wrap);

                this.form.on("save", this.saveForm, this);
	},
	/**
	* Inicializa Panel com campo para inclus�o dos �tens
	*/
	initPanel: function(){
            //initForm
            this.configForm.field = this;
            this.form = new gama3.form.AgileIncludeFieldForm(
                this.configForm
            );

            //initList
            this.configList.field = this;
            this.list = new gama3.form.AgileIncludeFieldList(
                this.configList
            );

            //initPanel
            this.panelMain = new Ext.Panel({
                title: this.title,
                height: this.heightPanel,
                width: this.widthPanel,
                layout: "border",
                items: [
                    this.form,
                    this.list
                ]
            });
	},
	/**
	* @interface {gama3.form.InterfaceFieldFormAlt}
        * @public
	*/
	showFormAltLoad: function(data){
            this.list.loadRecords(data);
            this.setValueInField();
	},
	/**
	* Coloca o valor dos ids selecionados a partir da fun��o serializeFuncion informada pelo usu�rio
	* Ex: "1;9;234;13"
        * @private
	*/
	setValueInField: function(){
            this.getEl().dom.value = this.serialize();
	},
        /**
         * Executa fun��o para serializa��o no escopo da Lista
         * @public
         */
	serialize: function(){
            if(this.serializeFunction)
                return this.serializeFunction.apply(this.list);
            else
                return this.list.serializeFunction();
	},
	/**
	* Limpa Valores do Campo (Tanto da Lista como do Form)
	* @public
	*/
	clear:function(){
            this.list.clearList();
            this.form.clearForm();
	},

	/**
	* Salva campos do formul�rio
	* Inclui �tem na listagem
	* @handler
	* @param form {gama3.ux.AgileInclude}
	*/
	saveForm: function(form)
	{
            if(!form.isValid())
                return;

            //Cria record com os novos valores do formul�rio
            var record = new this.list.Record(form.getValues());

            //Verifica se Existe Record de edi��o
            if(this.recordEdit){
                    //Altera os valores do Record antigo com os novos campos do formul�rio
                    for(var field in record.data)
                        this.recordEdit.set(field, record.get(field));

            } else {//Se n�o ent�o inclui novo �tem
                if(this.fireEvent("beforeInsert", this.list.getStore(), record)) {
                        //Insere Record no topo da Lista
                        this.list.getStore().insert(0, record);
                }
            }
            //Zera record de edi��o
            this.recordEdit = null;
            //Coloca Valores do GRID no hidden
            this.setValueInField();

            this.form.clearForm();		
	}

});


/**
* Classe representando o formul�rio do componente gama3.ux.AgileInclude
* @extend {Ext.form.FormPanel}
*/
gama3.form.AgileIncludeFieldForm = Ext.extend(Ext.Panel, {

	//Campos do formul�rio
	fields: null,        
	/**
	* @override
	*/
	initComponent: function()
	{
            this.formFields = new Array();
            for(var i=0; i<this.fields.length; i++)
                if(this.fields[i].getXType() != "label")
                    this.formFields.push(this.fields[i]);

            Ext.apply(this, {
                layout: "form",
                region: this.region,
                width: this.width,
                height: this.height,
                margins: "0 0 0 0",
                split: true,
                items: this.fields,
                buttonAlign: "left",
                title: this.title,
                frame:true,
                buttons: [
                    {
                        text: "Incluir",
                        handler: this.eventSave.createDelegate(this),
                        icon: "./lib/gama/interface_web/gama3/resources/img/icones/apply.png",
                        cls:"x-btn-text-icon"
                    }
                ]
            });

            gama3.form.AgileIncludeFieldForm.superclass.initComponent.apply(this, arguments);

            this.addEvents("save", "clearForm");
	},

	/* ---- Hendler Events ---- */

	/**
	* Lan�a evento de save caso o usu�rio use o bot�o "Enter"
	* @handler
	*/
	enterEvent: function(field, event){
            if(event.getKey() != Ext.EventObject.ENTER )
                return false;

            if(this.isValid())
                this.eventSave();
	},
	/*
	* @fireEvent save(FormPanel this)
	*/
	eventSave: function(){
            this.fireEvent("save", this);
	},
	/*
	 * Limpa Formul�rio
	 * @fireEvent clearForm(this)
	 */
	clearForm: function(){
            this.reset();
            //Define foco no formul�rio
            this.fields[0].focus();
            //Executa focus com delay de 100 milisegundos para corrigir bug no IE
            window.setTimeout(this.fields[0].focus.createDelegate(this.fields[0]), 100);
            //Fire Event
            this.fireEvent("clearForm", this);
	},
        /**
         * Limpa Campos do Formul�rio
         */
        reset: function(){
            for(var i = 0; i<this.formFields.length; i++){
                 this.formFields[i].setValue("");
                 this.formFields[i].clearInvalid();
            }
        },
        /**
         * Valida todos os campos e retorna se o formul�rio � v�lido ou n�o..
         */
        isValid: function(){
            var valid = true;
            for(var i = 0; i<this.formFields.length; i++){
                if(!this.formFields[i].validate())
                    valid = false;
            }
            return valid;
        },

        loadRecord: function(record){
            for(var x in record.data){
                this.getFieldByName(x).setValue(record.data[x]);
            }
        },

        getValues: function(){
            var values = {};
            for(var i = 0; i<this.formFields.length; i++){
                values[this.formFields[i].name] = this.formFields[i].getValue();
            }
            return values;
        },

        getFieldByName: function(name){
            for(var i = 0; i<this.formFields.length; i++){
                if(this.formFields[i].name == name)
                    return this.formFields[i];
            }
        }
});


/**
* Listagfem do componente gama3.ux.AgileInclude
* @extend {Ext.grid.GridPanel}
*/
gama3.form.AgileIncludeFieldList = Ext.extend(gama3.list.CellSelectGrid, {

	Record: null,

	initComponent: function()
	{
		//Cria Record que ser� usado pelos �tens inseridos na listagem
		this.Record = Ext.data.Record.create(this.fields);

		this.addControlColumns();		

		this.store = new Ext.data.Store({
                    data: [],
                    reader: new Ext.data.JsonReader({}, this.Record)
                })
		
		Ext.apply(this, {
			region: "center",
			title: this.title,
			frame: true,
			autoScroll: true,
			columns: this.columns,
			store: this.store			
		});

		gama3.form.AgileIncludeFieldList.superclass.initComponent.apply(this, arguments);

		this.addEvents("loadEdit", "clearList");
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
		//Edi��o
		{
			header: "",
			dataIndex: "_edit",
			width: 30,
			clickFunction: this.clickFunctionEdit.createDelegate(this),
			renderer: this.rendererEdit.createDelegate(this)
		},
		{
			header: "",
			dataIndex: "_remove",
			width: 30,
			clickFunction: this.clickFunctionRemove.createDelegate(this),
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
        /*
	* Limpa Listagem
	* @fireEvent clearList(this)
	*/
	clearList: function(){
            this.list.getStore().removeAll();
            this.fireEvent("clearList", this);
	},

	/* ---- clickFunction's ---- */

	/**
	* Executa Bind com o formul�rio para edi��o
	* @param record {Ext.data.Record}
	*/
	clickFunctionEdit: function(record)
	{
            //Carrega dados do Record para o form
            this.field.form.loadRecord(record);

            this.fireEvent("loadEdit", this.field, record);

            //Salva Record em edi��o
            this.field.recordEdit = record;
            //Coloca Foco no primeiro campo do formul�rio
            this.field.form.fields[0].focus();

	},
	/**
	* Remove �tem da listagem
	* @param record {Ext.data.Record}
	*/
	clickFunctionRemove: function(record)
	{
		//Carrega dados do Record para o form
		this.getStore().remove(record);
		//Seta valor no hidden
		this.field.setValueInField();
		//Limpa form
		this.field.form.clearForm();
                //Remove qualquer um em edi��o
                this.field.recordEdit = null;
	},

	/* ---- Renderer's ---- */

	/**
	* Renderiza Coluna com imagem de edi��o
	*/
	rendererEdit: function(val){
		return "<a href='#' ><img src='./lib/gama/interface_web/gama3/resources/img/icones/editar.png' alt='Editar' /></a>";
	},
	/**
	* Renderiza Coluna com imagem de exclus�o
	*/
	rendererRemove: function(val){
		return "<a href='#' ><img src='./lib/gama/interface_web/gama3/resources/img/icones/remove.png' alt='Remover' /></a>";
	},

        /* ---- Serialize ----- */

        serializeFunction: function(){

            var itemSeparator = this.field.itemSeparator;
            var valueSeparator = this.field.valueSeparator;
            var s = "";
            var sItem = "";
            this.getStore().each(function(obj){                                
                if(s.length > 0)
                    s+= itemSeparator;
                
                for(var x in obj.data){
                    if(sItem.length > 0)
                        sItem+= valueSeparator;

                    sItem += obj.data[x];
                }
                s += sItem;
                sItem = "";
            });
            return s;
        }
});