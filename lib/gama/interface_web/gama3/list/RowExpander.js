Ext.ns('gama3.list');

gama3.list.RowExpander = Ext.extend(Ext.ux.grid.RowExpander, {

    expandAllEnabled: false,
    header: '',
    expandedCount: 0,

    constructor: function(config){
        Ext.apply(this, config);
        //Se expandir tudo estiver habilitado, mostra botão para expandir tudo no cabeçalho.
        if(this.expandAllEnabled)
            this.header = '<div class="x-grid3-hd-expanded" >&#160;</div>';
        gama3.list.RowExpander.superclass.constructor.call(this,config);
    },

    /*
     * Renderiza Componente
     * @override
     */
    onRender: function()
    {
        gama3.list.RowExpander.superclass.onRender.apply(this, arguments);

        var view = this.grid.getView();
        //Copiado de CheckboxSelectionModel -> Pega header do grid e adiciona evento
        Ext.fly(view.innerHd).on('mousedown', this._eventToggleSelect, this);
    },

    /*
     * Expande todas as linhas.
     */
    expandAll : function(){
        this.expandedCount = 0;
        for(var i = 0; i < this.grid.store.getCount(); i++) {
            var row = this.grid.view.getRow(i);
            this.expandRow(row);
        }
    },

    /*
     * Encolhe todas as linhas.
     */
    collapseAll : function(){
        this.expandedCount = this.grid.store.getCount();
        for(var i = 0; i < this.grid.store.getCount(); i++) {
            var row = this.grid.view.getRow(i);
            this.collapseRow(row);
        }
    },

    /*
     * Expande um linha.
     * @override
     */
    expandRow : function(row){
        gama3.list.RowExpander.superclass.expandRow.apply(this,arguments);
        this.verifyAllExpanded();
    },

    /*
     * Encolhe uma linha.
     * @override
     */
    collapseRow : function(row){
        gama3.list.RowExpander.superclass.collapseRow.apply(this,arguments);
        this.verifyAllCollapsed();
    },

    /*
     * Verifica se todas as linhas já foram expandidas.
     * @override
     */
    verifyAllExpanded: function(){
        this.expandedCount++;
        if(this.expandedCount == this.grid.store.getCount()){
            var view = this.grid.getView();
            var div = Ext.fly(Ext.fly(view.innerHd).query('.x-grid3-hd-expanded')[0]);
            if(div != undefined){
                div.replaceClass('x-grid3-hd-expanded','x-grid3-hd-collapsed');
            }
        }
    },

    /*
     * Verifica se todas as linhas já foram encolhidas.
     * @override
     */
    verifyAllCollapsed: function(){
        this.expandedCount--;
        if(this.expandedCount == 0){
            var view = this.grid.getView();
            var div = Ext.fly(Ext.fly(view.innerHd).query('.x-grid3-hd-collapsed')[0]);
            if(div != undefined){
                div.replaceClass('x-grid3-hd-collapsed','x-grid3-hd-expanded');
            }
        }
    },

    /**
     * Pega evento de click no header da página
     * @param e {Event}
     * @param t {HTML Element} clicado
     */
    _eventToggleSelect : function(e, t)
    {
        // Só executa se o click foi efetuado na header de expand
        if(t.className.trim() == 'x-grid3-hd-expanded'){
            this.expandAll();
        }else if(t.className.trim() == 'x-grid3-hd-collapsed'){
            this.collapseAll();
        }
    }

});