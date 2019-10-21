/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.TreeFieldTrigger
 * @extends Ext.form.TriggerField
 * Cria um TriggerField que exibe uma janela para seleção de ítens em uma árvore.
 * @param {Object} config Configuration options
 * @implements gama3.form.InterfaceFieldFormAlt
 */
gama3.form.TreeFieldTrigger = Ext.extend(Ext.form.TriggerField, gama3.useInterface(gama3.form.InterfaceFieldFormAlt, {

    /**
     * @cfg {String} hiddenName se especificado, um campo hidden com este nome será dinamicamente gerado
     * e guardará o id do nó clicado. Este campo também será enviado no submit do formulário.
     */

    /**
     * @cfg {String} hiddenId se {@link #hiddenName} este, este atributo definirá o id do campo criado.
     * Se não existir o id do campo hidden será o valor do name.
     */

    /**
     * @cfg {String} titleWindow Contém título da janela que é exibida com as opções de seleção
     */

    /**
     * @cfg root {Ext.tree.TreeNode} Nó root do TreePanel. Se não for indicado será construído um nó padrão a partir da URL
     */

    /**
     * @cfg url {String} url padrão do nó root caso {@link #root} não tenha sido indicado.
     */

	/**
     * @cfg onlySelectLeaf {String} Informa se deve apenas selecionar nós folha.!
     */

    /**
     * @cfg saveOnSelect {boolean} Indica se ao clicar em um ítem a janela deve ser fechada e o ítem selecionado. Caso seja false, ao selecionar um ítem o usuário terá que clicar em um botão "selecionar item" para finalizá-la. {default: true}
     */
     saveOnSelect: true,

    /**
     * janela exibida quando trigger for lançado.
     * @type Ext.Window
     * @public
     */
    window: null,

    /*
     * Lista de usuários que será exibida na janela
     * @type Ext.tree.TreePanel
     * @public
     */
    treePanel: null,

    /*
     * Se janela já está aberta
     * @type boolean
     * @private
     */
    exists: false,

    /*
     * Botão para cancelar seleção
     * @type Ext.Button
     * @private
     */
    buttonCancel: null,

    /*
     * Botão para salvar seleção
     * @type Ext.Button
     * @private
     */
    buttonSave: null,

    /*
     * Campo de formulário hidden criado caso exista config 'hiddenName'
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
            //Quando botão for clicado executa esta função
            onTriggerClick: this.openSearch.createDelegate(this),
            //Não permite escrita no ítem
            readOnly: true,
            //Classe que adiciona ícone de inclusão ao trigger
            triggerClass: this.triggerClass || "x-form-tree-trigger"
        });

        this.initTree();

        //Chama método superior
        gama3.form.TreeFieldTrigger.superclass.initComponent.apply(this, arguments);

        this.addEvents(
            /**
             * @event save
             * Lançado quando usuário clica no botão "salvar"
             * @param {gama3.form.TreeFieldTrigger}
             */
            "save",
            /**
             * @event save
             * Lançado quando usuário clica no botão "Cancelar"
             * @param {gama3.form.TreeFieldTrigger}
             */
            "cancel"
        );
    },
    onRender: function()
    {
        gama3.form.TreeFieldTrigger.superclass.onRender.apply(this, arguments);

        //Se existe hiddenName criar campo de formulário hidden que conterá o id do Nó selecionado
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
     * Quando componente for destruído fecha janela caso esteja aberta
     * @override
     * @private
     */
    onDestroy: function()
    {
        //Quando componente for destruído fecha janela.
        if(this.window)
        {
            this.window.hide();
            delete this.window;
        }

        gama3.form.TreeFieldTrigger.superclass.onDestroy.apply(this, arguments);
    },

    /**
     * Cria Toolbar com ítem pra busca, expansão e colapso dos ítens
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
                tooltip: 'Expande todos os nós',
                handler: function(){ this.tree.root.expand(true); }.createDelegate(this)
            }, '-', {
                iconCls: 'icon-collapse-all',
                tooltip: 'Colapsa todos os nós',
                handler: function(){ this.tree.root.collapse(true); }.createDelegate(this)
            }];

        return tbar;
    },

    /**
     * Exibe janela com listagem de ítens para busca
     */
    openSearch: function()
    {
        //SE janela não existe, a cria.
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
            //Cria botão para salvamento
            this.buttonSave = new Ext.Button({
                text: "Selecionar Ítem",
                handler: this.save.createDelegate(this),
                iconCls: "gama3-tree-icon-save",
                cls:"x-btn-text-icon",
                disabled: true
            });
            itemsButton.push(this.buttonSave)
        }

        itemsButton.push("->");

        //Cria botão de cancelamento
        this.buttonClear = new Ext.Button({
            text: "Limpar Seleção",
            handler: this.clear.createDelegate(this),
            iconCls: "gama3-tree-icon-clear",
            cls:"x-btn-text-icon"
        });
        itemsButton.push(this.buttonClear);

        //Cria botão de cancelamento
        this.buttonCancel = new Ext.Button({
            text: G3.text.button.cancel,
            handler: this.cancel.createDelegate(this),
            iconCls: "gama3-tree-icon-cancel",
            cls:"x-btn-text-icon"
        });
        itemsButton.push(this.buttonCancel);

        //Cria janela
        this.window = new Ext.Window({
            title: this.titleWindow || "Selecione um ítem da árvore",
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
     * Inicializa eventos para manipulação da janela.
     * Quando o componente superior (inserido no tabPanel) for escondido,
     * Também deve esconder a janela deste componente. O mesmo é válido para a exibição.
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
     * Inicializa Painel de Árvore
     */
    initTree: function()
    {
        if(!this.root)
        {
            if(this.url)
                this.root = new Ext.tree.AsyncTreeNode({
                    loader: new Ext.tree.TreeLoader({dataUrl: this.url}),
                    text: "Ítens",
                    expanded: true
                });
            else
                this.root = new Ext.tree.TreeNode({
                    text: "Ítens",
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
     * Filtra árvore
     * @eventHandler
     * @param field {Ext.form.TextField} campo de formulário que contém o valor para a filtragem
     */
    filtraTree: function(field)
    {
        //pega nó principal
        //E para cada nó chama função de filtragem
        this.tree.getRootNode().eachChild(this.filtraNode.createDelegate(this, [field.getValue()], 1))
    },

    /**
     * Filtra nó, exibindo apenas os nós que contenham no atributo 'text' o 'value'.
     * @private
     * @param node {Ext.tree.TreeNode} nó para filtrar
     * @param value {String}
     */
    filtraNode: function(node, value)
    {
        if(!node.isLeaf())
        {
            //expand nó
            node.expand();
            //pega filhos
            var childs = node.childNodes;
            //variácvel se nó raiz deverá ser escondido
            var esconde = true;
            //Para todos os filhos...
            for(var i = 0; i<childs.length; i++)
            {
                //tenta filtrar e caso algum retorne true significa que há ítens para exibir, portanto, não deve ser escondido
                if(this.filtraNode(childs[i], value) == 1)
                    esconde = false;
            }

            //Se é para esconder
            if(esconde && !node.text.toLowerCase().match(value.toLowerCase()))
            {
                //esconde e retorna false indicando ao nível acima que nada dentro desse nó será exibido.
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
     * Esconde janela e remove valores salvos nos campos de formulário
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
     * Salva Valores do nó selecionado nos campos de formulário
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
     * Função executada sempre que este campo é carregado em um formulário de alteração
     * @param data {Object} objeto retornado na requisição showFormAlt
     * @interface gama3.form.InterfaceFieldFormAlt
     */
    showFormAltLoad: function(data)
    {
        //Pega ítens retornados no ajax
        var itens = eval(data);

        //pega nó root do field
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
     * seleciona um ítem da árvore a partir do ID de um nó
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

