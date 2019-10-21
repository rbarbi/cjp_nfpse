/**
 * Classe de Grid Padrão para listagem de objetos
 * Possibilita a inserção de uma função mapeada com uma coluna que sempre que esta for clicada,
   aquela será executada enviando como parâmetro um Record com os dados.
 */

gama3.list.List = Ext.extend(gama3.list.CellSelectGrid, {

    /**
     * @cfg Indica se tem Coluna Com CheckBox
     * @type {boolean}
     */
    checkboxColumn: true,    
    /**
     * @cfg Indica se tem coluna com ícone de Visualização
     * @type {boolean}
     */
    viewColumn: true,
    /**
     * @cfg Indica se tem coluna com ícone de edição
     * @type {boolean}
     */    
    editColumn: true,
    /**
     * @cfg Indica se tem coluna com ícone de exclusão
     * @type {boolean}
     */   
    removeColumn: true,    
    /**
     * @cfg Indica se um store deve ser carregado ao exibir o painel
     * @type {boolean}
     */
    storeLoad: true,
    /**
     * @cfg Indica se deve ser criado atributo pageSize no Store da Lista informando o número de ítens por página
     * @type {boolean}
     */
    insertPageSize: true,

    closable: true,

    /*
     * Inicializa Componente
     * @override
     */
    initComponent: function()
    {
        this._initColumns();
        //Ítens selecionados -> Usados apenas quando checkboxColumn está ativo
        if(!this.selectedRecords)
            this.selectedRecords = new Ext.util.MixedCollection();
        //Se tiver pageSize cria um PagingToolbar
        var bbar = this.pageSize ? this.createPagingToolbar() : false;

        //Sobrescreve atributos - Não podem ser modificados pelo config;
        Ext.apply(this, {                        
            iconCls: this.iconCls || "gama3-panel-icon-list",
            bbar: this.bbar || bbar
        })

        gama3.list.List.superclass.initComponent.apply(this);

        //Events
        this.addEvents("addItem", "showItem", "editItem", "deleteItem");
    },

    /*
     * Renderiza Componente
     * @override
     */
    onRender: function()
    {
        gama3.list.List.superclass.onRender.apply(this, arguments);

        this.loadMascara = new Ext.LoadMask(this.getId(), {store: this.store, msg:this.waitMessage || "Carregando..."});
        
        if(this.storeLoad)
        	this.store.load();

        var view = this.getView();
        //Copiado de CheckboxSelectionModel -> Pega header do grid e adiciona evento
        Ext.fly(view.innerHd).on('mousedown', this._eventToggleSelect, this);
    },
    /**
     * Cria PagingToolbar e coloca PageSize no Store
     */
    createPagingToolbar: function(){
        var paging = new Ext.PagingToolbar({pageSize: this.pageSize, store: this.store});
        this.store.baseParams.pageSize = this.pageSize;
        return paging;
    },
    /**
     * Inicializa Colunas Default
     */
    _initColumns: function()
    {
        //SE não vier array, não inicializa colunas default
        if(!Ext.isArray(this.columns)){
            return true
        }

        //Cria objeto com a definição das colunas
        this.defaultColumns = {
            checkbox: {header: '<div class="x-grid3-hd-checker" >&#160;</div>', menuDisabled: true, hideable: false, clickFunction: this.checkboxSelected.createDelegate(this), width: 30, dataIndex:"id", renderer: function(val) {
                    return '<div class="x-grid3-hd-checker checkbox-' + val + '" >&#160;</div>';
                }},
            view: {menuDisabled: true, hideable: false, header: "", clickFunction: this.showItem.createDelegate(this), width: 30, dataIndex:"id", renderer: function(val) {return "<a href='#show:"+val+"' ><img src='./lib/gama/interface_web/gama3/resources/img/icones/visualizar.png' alt='Visualizar' /></a>"}},
            edit: {menuDisabled: true, hideable: false, header: "", clickFunction: this.editItem.createDelegate(this), width: 30, dataIndex:"id", renderer: function(val) {return "<a href='#edit:"+val+"' ><img src='./lib/gama/interface_web/gama3/resources/img/icones/editar.png' alt='Editar' /></a>"}},
            remove: {menuDisabled: true, hideable: false, header: "", clickFunction: this.deleteItem.createDelegate(this), width: 30, dataIndex:"id", renderer: function(val) {return "<a href='#delete:"+val+"' ><img src='./lib/gama/interface_web/gama3/resources/img/icones/excluir.png' alt='Deletar' /></a>"}}
        }

        //Adiciona ítens conforme variáveis de controle

        //Remove
        if(this.removeColumn)
            this._addColumn(this.defaultColumns.remove);
        //Edit
        if(this.editColumn)
            this._addColumn(this.defaultColumns.edit);
        //View
        if(this.viewColumn)
            this._addColumn(this.defaultColumns.view);
        //Checkbox
        if(this.checkboxColumn)        
            this._addColumn(this.defaultColumns.checkbox);                  
    },

    /**
     * Adiciona Coluna ao ColumnModel
     * @param column {Object}
     */
    _addColumn: function(column)
    {
        this.columns = [column].concat(this.columns);
    },

    // ---- EVENTOS ---- //

    /**
     * Dispara evento para criação de novo ítem.
     */
    addItem: function()
    {
        this.fireEvent("addItem", this);
    },

    /**
     * Dispara evento para visualização de ítem.
     * @param record {Ext.data.Record}
     */
    showItem: function(record)
    {
        this.fireEvent("showItem", this, record);
    },

    /**
     * Dispara evento para edição de ítem.
     * @param record {Ext.data.Record}
     */
    editItem: function(record)
    {
        this.fireEvent("editItem", this, record);
    },

    /**
     * Dispara evento para remoção de ítem.
     * @param record {Ext.data.Record}
     */
    deleteItem: function(record)
    {
        this.fireEvent("deleteItem", this, record);
    },

    // ---- SELEÃ‡ÃƒO COM CHECKBOX ---- //

    /**
     * Retorna coleção de ítens selecionados
     * @return selectedRecords {MixedCollection}
     */
    getSelectedRecords: function()
    {
        if(!this.selectedRecords)
            this.selectedRecords = new Ext.util.MixedCollection();
        
        return this.selectedRecords;
    },

    /**
     * Limpa seleção de ítens
     */
    clearSelectedRecords: function()
    {
        this.selectedRecords.clear();
    },

    /**
     * Adiciona aos ítens selecionados
     * @param record {Ext.data.Record}
     */
    _addSelect: function(record)
    {
        this.selectedRecords.add(record.get("id"), record);
    },

    /**
     * Remove dos ítens selecionados
     * @param record {Ext.data.Record}
     */
    _removeSelect: function(record)
    {
        this.selectedRecords.remove(record);
    },

    /**
     * Seleciona um CheckBox a partir do record correspondente e o adiciona ao this.selectedRecords
     * @param record {Ext.data.Record}
     */
    checkboxSelected: function(record)
    {
        var arr = this.getGridEl().query(".checkbox-"+record.get("id"));
        var el = arr[0];
        if(el.className.match('x-grid3-hd-checker'))
        {
            el = Ext.fly(el.parentNode);
            var isChecked = el.hasClass('x-grid3-hd-checker-on');
            if(isChecked)
            {
                el.removeClass('x-grid3-hd-checker-on');
                this._removeSelect(record);
                //this.clearSelections();
            }else{
                el.addClass('x-grid3-hd-checker-on');
                this._addSelect(record);
                //this.selectAll();
            }
        }
    },

    /**
     * Seleciona todos os ítens exibidos no GRID
     */
    selectAll: function()
    {
        //Pega do corpo do GRID todos os checkbox
        var arr = this.getView().mainBody.query(".x-grid3-hd-checker");
        //Para cada um deles, maraca como selecionado
        for(var i = 0; i < arr.length; i++) {
            var el = Ext.get(arr[i].parentNode);
            var isChecked = el.hasClass('x-grid3-hd-checker-on');
            if(!isChecked)
                el.addClass('x-grid3-hd-checker-on');
        }

        //Limpa ítens selecionados
        this.clearSelectedRecords();

        //Cria nova coleção
        var collection = new Ext.util.MixedCollection();
        //Pega array de ítens e adiciona a coleção
        collection.addAll(this.store.getRange());
        //Para cada ítem o adiciona aos ítens selecionados
        collection.each(function(obj){
            this._addSelect(obj);
        }, this)
    },

    /**
     * Des - Seleciona todos os ítens exibidos no GRID
     */
    unselectAll: function()
    {
        var arr = this.getView().mainBody.query(".x-grid3-hd-checker");
         for(var i = 0; i < arr.length; i++) {
            var el = Ext.get(arr[i].parentNode);
            var isChecked = el.hasClass('x-grid3-hd-checker-on');
            if(isChecked)
                el.removeClass('x-grid3-hd-checker-on');
        }

        //Limpa ítens selecionados
        this.clearSelectedRecords();

        var collection = new Ext.util.MixedCollection();
        collection.addAll(this.store.getRange());
        collection.each(function(obj){
            this._removeSelect(obj);
        }, this)
    },

    /**
     * Pega evento de click no header da página
     * @param e {Event}
     * @param t {HTML Element} clicado
     */
    _eventToggleSelect : function(e, t)
    {
        // Só executa se o click foi efetuado no checkbox do header
        if(t.className == 'x-grid3-hd-checker'){
            //Pega nó superior para adicionar classe que marca o checkbox
            var hd = Ext.fly(t.parentNode);
            var isChecked = hd.hasClass('x-grid3-hd-checker-on');
            if(isChecked){
                //Remove marcador de seleção
                hd.removeClass('x-grid3-hd-checker-on');
                this.unselectAll();
            }else{
                //Adiciona marcador de seleção
                hd.addClass('x-grid3-hd-checker-on');
                this.selectAll();
            }
        }
    }

});