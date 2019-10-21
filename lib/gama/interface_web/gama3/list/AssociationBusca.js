gama3.list.AssociationBusca = Ext.extend(gama3.list.Association, {	

    initComponent: function()
    {
        //Inicializa Store        
        	
        Ext.apply(this, {            
            frame: true,            
            bbar: new Ext.PagingToolbar({pageSize: iadoc.c.numItensPagina, store: this.store}),
            tbar: this.createTBar(),            
            store: this.store,            
            columns: [			
            {
                                    header: "<img alt=\"S\" title=\"Status do Processo\" src=\"./mod/"+iadoc.c.m+"/interface_web/resources/img/status.png\">",
                                    tooltip: "Status do processo",
                                    dataIndex:"statusProcesso",
                                    width: 25,
                                    hideable: false,
                                    menuDisabled: true,
                                    renderer: function(val){
                                        if(val == "A")
                                            return "<img alt=\"Ativo\" title=\"Processo Ativo\" src=\"./mod/"+iadoc.c.m+"/interface_web/resources/img/status_ativo.png\">";
                                        else
                                            return "<img alt=\"Ativo\" title=\"Processo Inativo\" src=\"./mod/"+iadoc.c.m+"/interface_web/resources/img/status_inativo.png\">";
                                }}	
				
                                 ,{header: "ID", dataIndex:"id", width: 30}

                                ,{header: "Número", dataIndex:"numero", width: 130}

                                ,{header: "Instância", width: 110, dataIndex:"instanciaAtualNome"}
                            
				
                
            ]
        });

        gama3.list.AssociationBusca.superclass.initComponent.apply(this);

       this.addEvents("deleteItems");
    },


    onShow: function()
    {
        //Recarrega Store
        this.store.reload();
        //Seleciona ítem de menu
        this.selectItemMenu();
        //Limpa ítens selecionados
        this.selectedRecords.clear();

		gama3.list.AssociationBusca.superclass.onShow.apply(this, arguments);

    },
        

    /**
     * Cria ítens da barra de ferramentas da listagem
     * @return Array
     */
    createTBar: function()
    {        

        //retorna array dos ítens da barra
        return this.tbar.concat([{xtype: 'tbseparator'}]).concat(this.createBarSearch());
         
    },

    /**
     * Cria ítens para a barra de busca
     * @return Array
     */
    createBarSearch: function()
    {
        //Cria Combo dos Campos
        this.comboCampos = new gama3.form.SearchFieldSelect({
        			width: 65,
                    searchFields: [
                        ["0", "ID", "id"]
                        ,["1", "Número", "numero"]                        
                    ]
                });
        

        this.searchField = new gama3.form.SearchField({
            width: 100,
            store: this.store,
            comboCampos: this.comboCampos
        });
                
        var barSearch = [
                new Ext.form.Label({html: " Busca: "}),
                {xtype: 'tbspacer'},
                this.comboCampos,
                {xtype: 'tbspacer'},
                this.searchField               
            ];

       return barSearch;
    },

    /**
     * Seleciona um filtro para o status do processo
     * @param status {string}
     */
    selectStatusProcesso: function(status)
    {
        if(status)
            this.getStore().baseParams.status = status;
        else
            delete this.getStore().baseParams.status;
        this.getStore().reload({params: {start: 0}});
    },

    /**
     * Seleciona um filtro para a instância do processo
     * @param instancia {int}
     */
    selectInstanciaProcesso: function(instancia)
    {
        if(instancia)
            this.getStore().baseParams.instancia = instancia;
        else
            delete this.getStore().baseParams.instancia;
        
        this.getStore().reload({params: {start: 0}});
    },

    limparFiltros: function()
    {
        this.getStore().baseParams.status = '';
        this.getStore().baseParams.instancia = '';
        this.getStore().baseParams.coluna = '';
        this.getStore().baseParams.valor = '';

        this.searchField.setValue("");
        this.comboCampos.setValue("");
        Ext.getCmp("iadoc.processo.list.filtro.status.todos").setChecked(true);
        Ext.getCmp("iadoc.processo.list.filtro.instancia.todos").setChecked(true);

        this.getStore().reload();
    },

    /*
     * Seleciona um ítem de menu correspondente a esta aba
     */
    selectItemMenu: function()
    {
        iadoc.menu.Processo.expand(true);
        iadoc.Ds.getNav().selectItem(iadoc.treeNode.Processo, "menu.processo.listar");
    }

       
});

Ext.reg("gama3.list.AssociationBusca", gama3.list.AssociationBusca)
	
	