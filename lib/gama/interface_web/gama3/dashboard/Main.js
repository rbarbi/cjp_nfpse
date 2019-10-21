//MAIN
gama3.dashboard.Main = Ext.extend(Ext.TabPanel, {

   panelIndex: null,

   /**
    * @cfg panelIndex {Ext.Panel}
    * Painel para ser usado como aba default
    */

    /**
     * @cgf itemsPortlet {Portlet[][]}
     * �tens para exibi��o no portal
     */

   initComponent: function()
   {
       if(this.itemsPortlet)
       {
            var columns = [];
            var portletsLength = this.itemsPortlet.length;
            var width = 1 / portletsLength;

            for(var i = 0; i < portletsLength; i++)
            {
                columns[i] = new Ext.ux.PortalColumn({
                     columnWidth: width,
                     frame: true,
                     style:'padding:10px 10px 10px 10px',
                     items: this.itemsPortlet[i]
                });

            }
            if(portletsLength == 0)
                columns = false;
        }
        else
            columns = false;

        //Se n�o existe panelIndex Cria o Portal
        if(!this.panelIndex)
            this.panelIndex = new Ext.ux.Portal({
                frame: true,
                title: G3.text.dashboard,
                closable: false,
                items: columns,
                hash: {name: "dashboard.panelIndex"}
            });

       Ext.apply(this, {
            frame: true,
            region: "center",
            activeTab: 0,
            enableTabScroll:true,
            plugins: [new Ext.ux.TabCloseMenu()],
            items: [this.panelIndex]
       })
       gama3.dashboard.Main.superclass.initComponent.apply(this, arguments);
   },

   /*
    * @return Ext.Panel
    */
   getIndex: function()
   {
       return this.panelIndex;
   },

   /*
     * Adiciona TreePanel ao Painel de Menus
     */
    addPortlet: function(portlet, _column)
    {
        var column = _column || 0;
        this.panelIndex.getComponent(column).add(portlet);
        this.panelIndex.doLayout();
    },

    /*
     * Adiciona um painel ao painel principal e o exibe em seguida
     * @param panelAdd {Ext.Panel}
     */
    addPanel: function(panelAdd)
    {
        if(!panelAdd)
            throw new gama3.util.Exception({msg: "Par�metro 'panelAdd' não definido em Exemplo.admin.UsuarioAction"});

        //Adiciona novo ítem ao painel
        var p = this.add(panelAdd);
        p.show();

        //Usa método doLayout para redefinir os tamanhos de largura e altura.
        this.doLayout();
    },

     /*
     * Adiciona um painel ao painel principal.
     * Caso painel não exista, será adicionado, senão, sua aba será ativada
     * @param panel {Ext.Panel}
     * @return {Ext.Panel}
     */
    addPanelIfNotExist: function(panel)
    {
        var panelFound = this.existPanel(panel);
        if(panelFound)
        {
            this.setActiveTab(panelFound);
            return panelFound;
        }
        else
        {
            this.addPanel(panel);
            return panel;
        }
    },

    /*
     *  Adiciona painel removendo-o caso o encontre
     *  @param panel Ext.Panel que será adicionado
     *  @return panel Ext.Panel
     */
    addPanelRemoveIfExist: function(panel)
    {
        var panelFound = this.existPanel(panel);
        if(panelFound)
        {
            this.remove(panelFound);
        }
        this.addPanel(panel);
        return panel;
    },

     /*
     * Verifica se determinado painel já existe no dashboard através do hashName
     * @param hashName String
     * @return Mixed retorna o painel caso o encontre ou falso se não encontrar
     */
    findByHashName: function(hashName)
    {
        //Variável que aramazena item se este for encontrado
        var itemFound = null;
        //Para cada ítem do painelIndex, o compara com o painel a ser adicionado pelo atributo hash.name
        this.items.each(function(item){
            if(item.hash.name == hashName)
            {
                itemFound = item;
                //Para execução da função each
                return false
            }
        });
        //Se ítem encontrado o retorna
        if(itemFound)
            return itemFound;
        else
            return false;
    },

    /*
     * Verifica se determinado painel já existe no dashboard
     * @param panel Ext.Panel
     * @return Mixed retorna o painel caso o encontre ou falso se não encontrar
     */
    existPanel: function(panel)
    {
        if(!panel.hash.name)
                throw gama3.util.Exception({name: "Hash.name not found", msg: "Todo Panel ao ser adicionado ao Dashboard deve conter um atributo hash que contém um atributo name identificando o painel"});

        return this.findByHashName(panel.hash.name);
    },

    /**
     * Remove Painel Pelo hashName
     */
    removeByHashName: function(hashName)
    {
        var itemFound = this.findByHashName(hashName);
        if(itemFound)
            this.remove(itemFound);
    },

    /*
     * Exibe Painel pelo hashName
     */
    showByHashName: function(hashName)
    {
        var itemFound = this.findByHashName(hashName);
        if(itemFound)
            itemFound.show();
    }
});