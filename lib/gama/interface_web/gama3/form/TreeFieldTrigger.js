/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.TreeFieldTrigger
 * @extends Ext.form.TriggerField
 * Cria um TriggerField que exibe uma janela para sele��o de �tens em uma �rvore.
 * @param {Object} config Configuration options
 * @implements gama3.form.InterfaceFieldFormAlt
 */
gama3.form.TreeFieldTrigger = Ext.extend(Ext.form.TriggerField, gama3.useInterface(gama3.form.InterfaceFieldFormAlt, {

    /**
     * @cfg {String} hiddenName se especificado, um campo hidden com este nome ser� dinamicamente gerado
     * e guardar� o id do n� clicado. Este campo tamb�m ser� enviado no submit do formul�rio.
     */

    /**
     * @cfg {String} hiddenId se {@link #hiddenName} este, este atributo definir� o id do campo criado.
     * Se n�o existir o id do campo hidden ser� o valor do name.
     */

    /**
     * @cfg {String} titleWindow Cont�m t�tulo da janela que � exibida com as op��es de sele��o
     */

    /**
     * @cfg root {Ext.tree.TreeNode} N� root do TreePanel. Se n�o for indicado ser� constru�do um n� padr�o a partir da URL
     */

    /**
     * @cfg url {String} url padr�o do n� root caso {@link #root} n�o tenha sido indicado.
     */

	/**
     * @cfg onlySelectLeaf {String} Informa se deve apenas selecionar n�s folha.!
     */

    /**
     * @cfg saveOnSelect {boolean} Indica se ao clicar em um �tem a janela deve ser fechada e o �tem selecionado. Caso seja false, ao selecionar um �tem o usu�rio ter� que clicar em um bot�o "selecionar item" para finaliz�-la. {default: true}
     */
     saveOnSelect: true,

    /**
     * janela exibida quando trigger for lan�ado.
     * @type Ext.Window
     * @public
     */
    window: null,

    /*
     * Lista de usu�rios que ser� exibida na janela
     * @type Ext.tree.TreePanel
     * @public
     */
    treePanel: null,

    /*
     * Se janela j� est� aberta
     * @type boolean
     * @private
     */
    exists: false,

    /*
     * Bot�o para cancelar sele��o
     * @type Ext.Button
     * @private
     */
    buttonCancel: null,

    /*
     * Bot�o para salvar sele��o
     * @type Ext.Button
     * @private
     */
    buttonSave: null,

    /*
     * Campo de formul�rio hidden criado caso exista config 'hiddenName'
     * @type Ext.form.TextField
     * @private
     */
    hiddenField: false,

    /**
     * Inicializa componente
     * @override
     */
    initComponent: function()
    {
        Ext.apply(this, {
            //Quando bot�o for clicado executa esta fun��o
            onTriggerClick: this.openSearch.createDelegate(this),
            //N�o permite escrita no �tem
            readOnly: true,
            //Classe que adiciona �cone de inclus�o ao trigger
            triggerClass: this.triggerClass || "x-form-tree-trigger"
        });

        this.initTree();

        //Chama m�todo superior
        gama3.form.TreeFieldTrigger.superclass.initComponent.apply(this, arguments);

        this.addEvents(
            /**
             * @event save
             * Lan�ado quando usu�rio clica no bot�o "salvar"
             * @param {gama3.form.TreeFieldTrigger}
             */
            "save",
            /**
             * @event save
             * Lan�ado quando usu�rio clica no bot�o "Cancelar"
             * @param {gama3.form.TreeFieldTrigger}
             */
            "cancel"
        );
    },
    onRender: function()
    {
        gama3.form.TreeFieldTrigger.superclass.onRender.apply(this, arguments);

        //Se existe hiddenName criar campo de formul�rio hidden que conter� o id do N� selecionado
         if(this.hiddenName){
            this.hiddenField = this.el.insertSibling({
                tag:'input',
                type:'hidden',
                name: this.hiddenName,
                id: (this.hiddenId||this.hiddenName)
            }, 'before', true);

            // prevent input submission and getName() return hiddenName
            this.el.dom.removeAttribute('name');
        }
    },

	afterRender: function(){
		gama3.form.TreeFieldTrigger.superclass.afterRender.apply(this, arguments);

		var trigger = this.el.up('.x-form-element').child('.'+this.triggerClass);
		if(!trigger.isVisible()){
			trigger.show();
		}
	},

    /**
     * Quando componente for destru�do fecha janela caso esteja aberta
     * @override
     * @private
     */
    onDestroy: function()
    {
        //Quando componente for destru�do fecha janela.
        if(this.window)
        {
            this.window.hide();
            delete this.window;
        }

        gama3.form.TreeFieldTrigger.superclass.onDestroy.apply(this, arguments);
    },

    /**
     * Cria Toolbar com �tem pra busca, expans�o e colapso dos �tens
     * @return {Component[]}
     */
    createTbar: function()
    {
        //Cria campo de texto de filtro
        this.filtro = new Ext.form.TextField({
            width: 200,
            enableKeyEvents: true,
            listeners: {keyup: this.filtraTree.createDelegate(this) }
        });

        var tbar = [this.filtro,
            '->',
            {
                iconCls: 'icon-expand-all',
                tooltip: 'Expande todos os n�s',
                handler: function(){ this.tree.root.expand(true); }.createDelegate(this)
            }, '-', {
                iconCls: 'icon-collapse-all',
                tooltip: 'Colapsa todos os n�s',
                handler: function(){ this.tree.root.collapse(true); }.createDelegate(this)
            }];

        return tbar;
    },

    /**
     * Exibe janela com listagem de �tens para busca
     */
    openSearch: function()
    {
        //SE janela n�o existe, a cria.
        if(!this.exists)
           this.initWindow();

        this.window.show();
        this.exists = true;
    },

    /**
     * Inicializa Janela
     */
    initWindow: function()
    {
        var itemsButton = new Array();

        if(!this.saveOnSelect)
        {
            //Cria bot�o para salvamento
            this.buttonSave = new Ext.Button({
                text: "Selecionar �tem",
                handler: this.save.createDelegate(this),
                iconCls: "gama3-tree-icon-save",
                cls:"x-btn-text-icon",
                disabled: true
            });
            itemsButton.push(this.buttonSave)
        }

        itemsButton.push("->");

        //Cria bot�o de cancelamento
        this.buttonClear = new Ext.Button({
            text: "Limpar Sele��o",
            handler: this.clear.createDelegate(this),
            iconCls: "gama3-tree-icon-clear",
            cls:"x-btn-text-icon"
        });
        itemsButton.push(this.buttonClear);

        //Cria bot�o de cancelamento
        this.buttonCancel = new Ext.Button({
            text: G3.text.button.cancel,
            handler: this.cancel.createDelegate(this),
            iconCls: "gama3-tree-icon-cancel",
            cls:"x-btn-text-icon"
        });
        itemsButton.push(this.buttonCancel);

        //Cria janela
        this.window = new Ext.Window({
            title: this.titleWindow || "Selecione um �tem da �rvore",
            items: [this.tree],
            width: 400,
            height: 380,
            resizable: false,
            closable: false,
            autoScroll: true,
            tbar: this.createTbar(),
            bbar: itemsButton
        });

        this.initEventsToHandlerWindow();
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
        //Add Events
        obj.on("hide", this.hideWindow.createDelegate(this));
        //obj.on("show", this.showWindow.createDelegate(this));
    },

    /**
     * Inicializa Painel de �rvore
     */
    initTree: function()
    {
        if(!this.root)
        {
            if(this.url)
                this.root = new Ext.tree.AsyncTreeNode({
                    loader: new Ext.tree.TreeLoader({dataUrl: this.url}),
                    text: "�tens",
                    expanded: true
                });
            else
                this.root = new Ext.tree.TreeNode({
                    text: "�tens",
                    expanded: true
                })
        }

        this.tree = new Ext.tree.TreePanel({
           width: 370,
           frame: true,
           border: false,
           cls: "gama3-margin-5-all",
           root: this.root,
           rootVisible: false,
           listeners: {
               click: this.setNodeSelect.createDelegate(this)
           }
        });
    },

    /**
     * Filtra �rvore
     * @eventHandler
     * @param field {Ext.form.TextField} campo de formul�rio que cont�m o valor para a filtragem
     */
    filtraTree: function(field)
    {
        //pega n� principal
        //E para cada n� chama fun��o de filtragem
        this.tree.getRootNode().eachChild(this.filtraNode.createDelegate(this, [field.getValue()], 1))
    },

    /**
     * Filtra n�, exibindo apenas os n�s que contenham no atributo 'text' o 'value'.
     * @private
     * @param node {Ext.tree.TreeNode} n� para filtrar
     * @param value {String}
     */
    filtraNode: function(node, value)
    {
        if(!node.isLeaf())
        {
            //expand n�
            node.expand();
            //pega filhos
            var childs = node.childNodes;
            //vari�cvel se n� raiz dever� ser escondido
            var esconde = true;
            //Para todos os filhos...
            for(var i = 0; i<childs.length; i++)
            {
                //tenta filtrar e caso algum retorne true significa que h� �tens para exibir, portanto, n�o deve ser escondido
                if(this.filtraNode(childs[i], value) == 1)
                    esconde = false;
            }

            //Se � para esconder
            if(esconde && !node.text.toLowerCase().match(value.toLowerCase()))
            {
                //esconde e retorna false indicando ao n�vel acima que nada dentro desse n� ser� exibido.
                node.getUI().hide();
                return -1;
            }
            else
            {
                node.getUI().show();
                return 1;
            }
            //node.eachChild(this.filtraNode.createDelegate(this, [value], 1));
        }

        if(!node.text.toLowerCase().match(value.toLowerCase()))
        {
            node.getUI().hide();
            return -1;
        }
        else
        {
            node.getUI().show();
            return 1;
        }

    },

    /**
     * Esconde janela e remove valores salvos nos campos de formul�rio
     * @eventHandler
     * @launchEvent {cancel}
     * @private
     */
    cancel: function()
    {
        this.window.hide();

        this.fireEvent("cancel", this);
    },

    /**
     * Esconde janela e destaca valor selecionado.
     * @eventHandler
     * @launchEvent {save}
     * @private
     */
    save: function()
    {
        if(this.window)
            this.window.hide();

        this.getEl().highlight('99CC32',{
            duration: 0.5,
            attr: 'color'
        });

        this.fireEvent("save", this);
    },

    /**
     * Esconde janela e destaca valor selecionado.
     * @eventHandler
     * @launchEvent {save}
     * @private
     */
    clear: function()
    {
        this.setValue("", true);
        this.setHiddenValue("");
        this.window.hide();

        this.fireEvent("clear", this);
    },

    /**
     * Salva Valores do n� selecionado nos campos de formul�rio
     * @eventHandler
     * @private
     * @param node {Ext.tree.TreeNode}
     */
    setNodeSelect: function(node)
    {
		if(this.onlySelectLeaf && !node.isLeaf()){
			return false;
		}
		
        this.setValue(node.text, true);
        this.setHiddenValue(node.id);

        if(this.saveOnSelect)
            this.save();
        else
            if(this.buttonSave)
                this.buttonSave.enable();
    },

    /**
     * Salva valor do campo hidden
     * @public
     * @param v {String}
     */
    setHiddenValue: function(v)
    {
        if(this.hiddenField)
            this.hiddenField.value = v;
    },

     /**
     * Pega valor do campo hidden
     * @public
     * @return {String || false}
     */
    getHiddenValue: function(v)
    {
        if(this.hiddenField)
            return this.hiddenField.value = v;
        else
            return false;
    },

    /**
     * Esconde Janela
     * @eventHanlder
     * @public
     */
    hideWindow: function()
    {
        this.window.hide();
    },

    /**
     * Exibe Janela
     * @eventHanlder
     * @public
     */
    showWindow: function()
    {
        this.window.show();
    },

    /**
     * Retorna TreePanel
     * @public
     */
    getTree: function()
    {
        return this.tree;
    },

    /**
     * Fun��o executada sempre que este campo � carregado em um formul�rio de altera��o
     * @param data {Object} objeto retornado na requisi��o showFormAlt
     * @interface gama3.form.InterfaceFieldFormAlt
     */
    showFormAltLoad: function(data)
    {
        //Pega �tens retornados no ajax
        var itens = eval(data);

        //pega n� root do field
        var root = this.getTree().getRootNode();
        //Adiciona Filhos
        root.appendChild(itens);
    },

    /**
     * Define valor deste campo
     * @override
     */
    setValue: function(value, callSuper)
    {
        if(callSuper)
            gama3.form.TreeFieldTrigger.superclass.setValue.call(this, value);
        else
            this.setValueById(value);
    },

    /**
     * seleciona um �tem da �rvore a partir do ID de um n�
     * @public
     */
    setValueById: function(id)
    {
        this.getTree().getRootNode().expandChildNodes(true);

        var node = this.getTree().getNodeById(id);
        if(node)
            this.setNodeSelect(node);
    }

}, "gama3.form.TreeFieldTrigger"));

