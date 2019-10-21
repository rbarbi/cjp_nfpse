gama3.list.Association = Ext.extend(Ext.grid.GridPanel, {

    /*
     * Inicializa Componente
     * @override
     */
    initComponent: function()
    {
        var sm = new Ext.grid.CheckboxSelectionModel();

        var columns = [
            {
                menuDisabled: true,
                hideable: false,
                header: sm.header,
                dataIndex: 'id',
                width: 30,
                renderer: sm.renderer
            }
        ].concat(this.columns);

        Ext.apply(this, {
            sm: sm,
            columns: columns,
            frame:true
        })

        gama3.list.Association.superclass.initComponent.apply(this);
    },

    /*
     * Renderiza Componente
     * @override
     */
    onRender: function()
    {
        gama3.list.Association.superclass.onRender.apply(this, arguments);

        this.loadMascara = new Ext.LoadMask(this.getId(), {store: this.store, msg:this.waitMessage || "Carregando"});
        this.store.load();
    }

});