gama3.Dashboard = new Ext.extend(Ext.Viewport, {

    /**
     *  PAINEL TOPO
     *  Painel utilizado no topo do Dashboard, por padrão conterá um painel com um título e outro com ícones gerais.
     *  obs: USa Layout "border"
     */
    panelTop: null,

    /**
     * PAINEL NAVEGAÇÃO
     * Painel utilizado a esquerda do Dashboard que conterá os ítens para navegação do Sistema
     */
    panelNav: null,

    /**
     * PAINEL PRINCIPAL
     * Conterá as abas do sistema, é o painel principal da aplicação
     */
    panelMain: null,

    /**
     * PAINEL RODAPE
     * Contém o rodapé do sistema. Contém informaçãµes gerais como contato de suporte e CopyRight
     */
     panelFooter: null,

    initComponent: function()
    {
        //Inicializa Atributos
        this.initAtt();

        //Panel Top
        this.panelTop = this.top || false;
        //Panel Footer
        this.panelFooter = this.footer || false;
        //Painel da Esquerda
        this.panelNav = new gama3.dashboard.Nav({itemsMenu: this.itemsMenu, itemsNav: this.itemsNav});
        //Painel Principal
        this.panelMain = new gama3.dashboard.Main({itemsPortlet: this.itemsPortlet, panelIndex:this.panelIndex});


        Ext.apply(this, {
            layout: 'border',
            renderTo: Ext.getBody(),
            split: true,
            items: [
                this.panelTop,
                this.panelNav,
                this.panelMain,
                this.panelFooter
            ]
        });

        gama3.Dashboard.superclass.initComponent.call(this);
    },

    /**
     * Inicializa Verificação dos atributos de configuração exibidos
     */
    initAtt: function()
    {
       //Opcionais
       if(!this.itemsPortlet)
           this.itemsPortlet = false;

       if(!this.itemsMenu)
           this.itemsMenu = false;

       if(!this.itemsNav)
           this.itemsNav = [];
    },

    /*
     * Retorna painel do Topo
     * @return Ext.Panel
     */
    getTop: function()
    {
        return this.panelTop;
    },

    /*
     * Retorna painel principal
     * @return Ext.Panel
     */
    getMain: function()
    {
        return this.panelMain;
    },

    /*
     * Retorna painel de navegação
     * @return Ext.Panel
     */
    getNav: function()
    {
        return this.panelNav;
    },

    /*
     * Retorna painel footer
     * @return Ext.Panel
     */
    getFooter: function()
    {
        return this.panelFooter;
    }

});