gama3.form.LockupField = Ext.extend(Ext.form.TriggerField, gama3.useInterface(gama3.form.InterfaceFieldFormAlt, {

    /**
     * @cfg displayField {String} Nome do campo que ser� exibido na visualiza��o do textField
     * @required
     */

    /**
     * @cfg valueField {String} Nome do campo que ser� salvo para envio ao servidor     
     */
    valueField: "id",

    /**
     * @cfg pageSize {int} N�mero de �tens por p�gina da lista do lockup     
     */
    pageSize: 10,

    
    /**
    *  essa propriedade dir� quando o registro ser� selecionado automaticamente.
    */
    
    _selecionaItem:false,
    
    /*
    * valor que o cara digitou.
    */
    _valueDigitado:null, 
    
    /** 
     numero do campo que por padr�o ele ir� pesquisar.
    */
    _findField:null,
    
    /**
     * @cfg store {Ext.data.Store} Store usado para a listagem dos �tens
     * @required
     */
    store: null,

    /**
     * @cfg columns {Array} Colunas que ser�o exibidas na listagem
     * @required
     */

    /**
     * @cfg searchFields {array} Colunas para busca na listagem
     * @required
     */

    /**
     * @cfg clearButton {boolean} Indica se bot�o para limpar sele��o deve existir
     */
    clearButton: true,

    /**
     * @cfg windowWidth {number} Largura da Janela
     */
    windowWidth: 400,

    /*
     * Cont�m o Formul�rio de cadastro de �tem
     * @cfg
     * @public
     */
    formAdd: null,

    /**
     * R�tulo do bot�o de inclus�o de �tem     
     * @cfg
     */
    textAddButton: null,

    /*
     * Fun��o para manipula��o de erros do formul�rio
     * @cfg
     */
    errorHandler: null,

    /**
     * T�tulo da janela do Lockup
     * @cfg
     */
    windowTitle: null,

    //Record Salvo
    recordSave: null,

    //janela exibida quando trigger for lan�ado.
    window: null,

    //Lista de ativos que ser� exibida na janela
    list: null,

    //Se janela j� est� aberta
    exists: false,

    /*
     * Campo de formul�rio para salvar id do �tem selecionado
     * @type Ext.form.Field
     * @private
     */
    hiddenField: false,

    /**
     * Dados de refer�ncia para setar valor na inicializa��o de um formul�rio de altera��o.
     * Dever� ser preenchido com uma lista retornada do action showFormalt
     */
    dataRef: null,

    
    /**
    *  caso esse parametro foi FALSE, o campo poder� ser editado.
    */
    _readOnly:true,

    /**
     * Indica se deve carregar o Store do Lockup remotamente
     */
    loadStore: true,    
    
    /**
     * Inicializa componente
     * @override
     */
    initComponent: function()
    {
        //Inicializa Lista
        this.initList();
        //inicializa janela.
        this.initWindow();                          

        //Inicializa Objeto
    	Ext.apply(this, {       	        	
            //Quando bot�o for clicado executa esta fun��o
            onTriggerClick:this.openSearch.createDelegate(this),            
            //N�o permite escrita no �tem
            readOnly: this._readOnly,
            //Classe que adiciona �cone de inclusão ao trigger
            triggerClass: "x-form-search-trigger"
        });
                
        //Chama m�todo superior
        gama3.form.LockupField.superclass.initComponent.apply(this, arguments);

        this.addEvents(
        /**
         * @event save
         * Lan�ado quando usu�rio seleciona um �tem
         * @param {gama3.form.TreeFieldTrigger}
         * @param {Ext.data.Record} Record do �tem selecionado
         */
        "save");    
    },

    /**
     * @override
     */
    onRender: function()
    {
        gama3.form.LockupField.superclass.onRender.apply(this, arguments);

        //Cria campo escondido para salvar o ID do �tem
        this.hiddenField = this.el.insertSibling({
            tag:'input',
            type:'hidden',
            name: this.name,
            id: (this.id||this.name)
        }, 'before', true);

        // prevent input submission and getName() return hiddenName
        this.el.dom.removeAttribute('name');

        this.initEventsToHandlerWindow();			
    },

	afterRender: function(){
		gama3.form.LockupField.superclass.afterRender.apply(this, arguments);

		var trigger = this.el.up('.x-form-element').child('.x-form-search-trigger')
		if(!trigger.isVisible()){
			trigger.show();
		}
	},

     /**
     * Inicializa Janela
     */
    initWindow: function()
    {
        var bbar = false;
        if(this.clearButton)
        {
            bbar = new Array();
            bbar.push(
                "->",
                new Ext.Button({
                    text: "Limpar Sele��o",
                    handler: this.clear.createDelegate(this),
                    iconCls: "gama3-tree-icon-clear",
                    cls:"x-btn-text-icon"
                })
            )
        }

        var items = new Array();
        var tbar = false;

        items.push(this.list);
        if(this.formAdd)
        {
            this.initFormAdd();
            items.push(this.formAdd);
            tbar = new Array();
            tbar.push(this.getButtonAdd());

        }

        this.window = new Ext.Window({
            title: this.windowTitle || "Listagem de Objetos",
            items: items,
            width: this.windowWidth || 400,
            height: this.windowHeight ||  380,
            bbar: bbar,
            tbar: tbar,
            autoScroll: true
        });        
    },

    /**
     * Inicializa eventos para manipula��o da janela.
     * Quando o componente superior (inserido no tabPanel) for escondido,
     * Tamb�m deve esconder a janela deste componente. O mesmo � v�lido para a exibi��o.
     * @private
     */
    initEventsToHandlerWindow: function()
    {
        //Procura objeto superior tendo atributo hash (significa que foi inserido no tabPanel).        
        var obj = this.findParentBy(function(obj) {            
            if(obj.hash)
                return true;
        });

        if(obj)
            obj.on("hide", this.hideWindowOnly.createDelegate(this));
    },

    /**
     * Esconde Janela
     * @public
     */
    hideWindow: function()
    {        
        if(!this.validate())
        {
            this.setValue("", true);
            this.setHiddenValue("");
            this.recordSave = null;
        }
        
        try{this.window.hide();}catch(e){}
        this.exists = false;
    },

    /**
     * Esconde Janela
     * @eventHanlder
     * @public
     */
    hideWindowOnly: function()
    {
        try{this.window.hide();}catch(e){}
        this.exists = false;
    },

    /**
     * Inicializa Lista
     */
    initList: function()
    {
        //Se tem um tamanho de p�gina definido salva-o no store
        this.store.baseParams.pageSize = this.pageSize;

        this.list = new gama3.list.ListLockup({
            store: this.store,
            loadStore: this.loadStore,
            height: this.gridHeight || 320,
            _valueDigitado:this._valueDigitado,
            _findField:this._findField,
            width: this.windowWidth - 20,
            bbar: this.loadStore ? new Ext.PagingToolbar({pageSize:this.pageSize,store: this.store}):false,
            ///Colunas da listagem
            columns: this.columns,
            //Campos de busca
            searchFields: this.loadStore ?  this.searchFields : false
        });
    },

    /**
     * Inicliaza Eventos
     * @override
     */
    initEvents: function()
    {
        gama3.form.LockupField.superclass.initEvents.apply(this, arguments);

        //LIST
        //Ao selecionar um �tem do GRID grava o id e displayField no Lockup
        this.list.on('select', this.selectItem.createDelegate(this));
        
        //WINDOW
        //Ao inv�s de fechar a janela, apenas a esconde caso o usu�rio clique no �cone "fechar
        this.window.on("beforeclose",this.onBeforeCloseWindow, this);

        //STORE
        //Ao carregar store, verifica se apenas um foi selecionado, caso sim, seleciona-o automaticamente
        this.store.on('load',this.onLoadStore,this);

        //THIS
        //Ao pressionar Enter abre janela de busca e filtra valores automaticamente
        this.on("specialkey", this.enterEvent, this);
        //Ao tirar o foco do evento, exibe pesquisa
        this.on("blur", this.onBlurLockupField, this);
    },            

    /**
     * Recebe evento "specialkey" deste campo
     * Se for enter, ent�o abre janela de Filtragem
     * @handlerEvent
     * @private
     */
    enterEvent: function(field, event)
    {
        /*if((event.getKey() == Ext.EventObject.ENTER )&&(this.getValue()!="")) {            */
        //coloquei assim pra mesmo que n�o tenha nada no campo ele abra a janela do lookup pra pesquiar... FARINHA
        if(event.getKey() == Ext.EventObject.ENTER ) {            
            this._selecionaItem = true;
            this.openSearch();
        }
    },

    /**
     * Depois de carregar os dados do Store
     * Verifica se tem apenas um resultado, caso sim, o seleciona
     * @eventHandler
     * @private
     */
    onLoadStore: function()
    {
        //explicando... adiciono um listener no evento load que conta os registro
        //se for igual a 1 registro a busco foi bem sucedida
        if (this.store.getCount() == 1 && this._selecionaItem){
            this.selectItem(this.store.getAt(0));           
        }
        else if(this.store.getCount() == 0 && this._selecionaItem){
            Ext.Msg.alert("Aten��o", "Nenhum registro encontrado");            
        }
        this._selecionaItem = false;
    },

    /**
     * Ao inv�s de fechar a janela, apenas a esconde.
     * @handlerEvent
     * @private
     */
    onBeforeCloseWindow: function(w){
        this.exists = false;
        try{w.hide();}catch(e){};
        return false;
    },

    /**
     * Ao tirar o foco do campo, pesquisa valor digitado
     * @eventHandler
     * @private
     */
    onBlurLockupField: function()
    {        
        if(this.getValue().length == 0 || this.validate() || this.exists)
            return true;
        
        this._selecionaItem = true;
        this.openSearch();       
    },

    /**
     * Exibe janela com listagem de �tens para busca
     */
    openSearch: function()
    {
        //N�o deixa executar caso campo esteja desabilitado
        if(this.disabled)
            return;

        this.exists = true;
            
        this.window.show();
        this.list.show();

        this.list.setSearchValue(this.getValue());

        //Recarrega Store
        if(this.loadStore){
            this.list.reloadStore();
        }
        if(this.formAdd)
            this.formAdd.hide();
    },   

    /**
     * Quando componente for destru�do fecha janela caso esteja aberta
     * @override
     * @private
     */
    onDestroy: function()
    {        
        //Quando componente for destru�do fecha janela.
        if(this.window) {
            try{this.window.hide();}catch(e){}
            delete this.window;
        }
        
        gama3.form.LockupField.superclass.onDestroy.apply(this, arguments);        
    },       

    /**
     * Carrega os dados retornados no showFormAlt
     * Essencial para exibir displayField do �tem selecionado
     */
    showFormAltLoad: function(data)
    {
        this.dataRef = data;
    },

    /**
     * Limpa valores selecionados
     */
    clear: function()
    {
        this.setValue("", true);
        this.setHiddenValue("");
        this.recordSave = null;
        if(this.window)
            this.hideWindow();
        this.focus();
    },

    /**
     * Seleciona �tem a partir do clique em alguma linha da listagem
     */
    selectItem: function(record)
    {
        this.recordSave = record;
        
        this.setValue(record.get(this.displayField), true);
        this.setHiddenValue(record.get(this.valueField));
        this.focus();
        
        this.list.getSelectionModel().clearSelections();
        this.window.close();
       
        //Limpa Campo de Busca
        this.list.clearSearchValue();
            
        this.fireEvent("save", this, record);
    },

    /*
     * Valida Campo Lockup
     * @override
     */
    validateValue: function()
    {
        //Chama superior e se tiver erro, retorna falso.
        if(!gama3.form.LockupField.superclass.validateValue.apply(this, arguments))
            return false;

        //Se tem Record Salva, confere se valor digitado � como deveria ser.
        if(this.getRecordSave())
        {            
            if(this.getValue() == this.recordSave.get(this.displayField)) {         
                this.clearInvalid();
                return true;
            }
            else if(this.getValue().length == 0) {
                this.setHiddenValue("");
                this.clearInvalid();
                return true;
            }        
        }
        else if(this.getValue().length > 0)
        {
            this.markInvalid("O �tem selecionado n�o � v�lido. Para selecionar um �tem v�lido use o bot�o ao lado.");
            return false;
        }
        else
        {
            this.clearInvalid();
            return true;
        }
    },

    /* ---- Fun��es de Inclus�o de �tem ---- */

    /**
     * Inicializa formul�rio de inclus�o de �tem
     * @private
     */
    initFormAdd: function(){
       // this.formAdd.hide();

        this.formAdd.on("save", this.saveItem, this);
        this.formAdd.on("cancel", this.hideForm, this);
    },

    /**
     * Pega bot�o de inclus�o de �tem da Toolbar
     * @private
     */
    getButtonAdd: function(){        
        var tbButton = new Ext.Toolbar.Button({
            text: this.textAddButton || "Adicionar �tem",
            cls: 'x-btn-text-icon',
            icon: "./lib/gama/interface_web/gama3/resources/img/icones/add.png",
            handler: this.showForm,
            scope: this
        });
        delete this.textAddButton;        
        return tbButton;
    },
    
    /*
     * Exibe formul�rio
     * @public
     */
    showForm: function(){                
        this.list.hide();
        this.formAdd.show();
        this.window.doLayout();        
    },

    /**
     * Esconde formul�rio
     * @public
     */
    hideForm: function(){        
        this.list.show();
        if(this.loadStore){
            this.list.getStore().reload();
        }
        this.formAdd.hide();        
        this.window.doLayout();        
    },    

    /**
     * Envia requisi��o do formul�rio
     * Caso cadastro seja ok, seleciona �tem adicionado
     * @private
     */
    saveItem: function(formPanel){
        var form = formPanel.getForm();             
        if(form.isValid())
        {
            this.mascara = new Ext.LoadMask(this.window.getEl(), {msg:"Cadastrando �tem..."});
            this.mascara.show();

            form.submit({
                success: function(form,a){
                    var data = a.result.data;

                    this.mascara.hide();
                    delete this.mascara;

                    //Cria Record e o selecionado
                    var rec = new Ext.data.Record(form.getValues())                    
                    rec.set(this.valueField, data[this.valueField]);                    
                    this.selectItem(rec);
                    //Reseta formul�rio
                    this.formAdd.getForm().reset();
                    //Exibe Mensagem de Sucesso
                    gama3.Message.show("Sucesso", "Cadastro efetuado com sucesso.");
                }.createDelegate(this),
                failure: function(form, action) {
                    this.mascara.hide();
                    delete this.mascara;                    
                    if(this.errorHandler)
                        this.errorHandler(action.result.data);
                    else
                        Ext.Message.show("Erro", "Ocorreu um erro no cadastro do formul�rio")
                }.createDelegate(this)
            });
        } else {
                Ext.Msg.alert("Erro no Formulario", "Por favor, complete o preenchimento do formulario.");
        }
    },

    /** ---- Encapsulamento ---- */
    
    /**
     * Recupera Store deste lockup
     * @return {Ext.data.Store}
     */
    getStore: function(){
        return this.store;
    },

    /**
     * Recupra record Salvo
     * @return {Ext.data.Record}
     */
    getRecordSave: function()
    {
        return this.recordSave;
    },

    /**
     * Define valor do campo
     * Coloca um nome amig�vel na caixa de texto e o id do �tem no campo hidden
     * @param val {string}
     * @param callSuperclassOnly {boolean} Indica se deve apenas chamar o m�todo construtor
     */
    setValue: function(val, callSuperclassOnly)
    {
        if(callSuperclassOnly)
        {
            gama3.form.LockupField.superclass.setValue.call(this, val);
            return true;
        }

        //A partir daqui � usado apenas para edi��o do campo, quando � preciso selecionar automaticamente um record a partri de um id
        if(!this.dataRef)
            return false;

        for(var i = 0; i < this.dataRef.length; i++)
        {
            if(this.dataRef[i][this.valueField] == val)
            {
                gama3.form.LockupField.superclass.setValue.call(this, this.dataRef[i][this.displayField]);                
                this.recordSave = new Ext.data.Record(this.dataRef[i]);                
                this.setHiddenValue(val);
                this.validate();
                break;
            }
        }
    },

    /**
     * Valor que ser� inserido no campo hidden desse campo
     * @param value {string}
     */
    setHiddenValue: function(val)
    {
        this.hiddenField.value = val;
    },

    /**
     * Pega Valor que est� inserido no campo hidden desse campo
     * @return {string}
     */
    getHiddenValue: function(val)
    {
        return this.hiddenField.value;
    }

}, "gama3.form.LockupField"));

