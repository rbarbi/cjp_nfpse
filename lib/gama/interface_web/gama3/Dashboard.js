gama3.Dashboard = new Ext.extend(Ext.Viewport, {

    /**
     *  PAINEL TOPO
     *  Painel utilizado no topo do Dashboard, por padr�o conter� um painel com um t�tulo e outro com �cones gerais.
     *  obs: USa Layout "border"
     */
    panelTop: null,

    /**
     * PAINEL NAVEGA��O
     * Painel utilizado a esquerda do Dashboard que conter� os �tens para navega��o do Sistema
     */
    panelNav: null,

    /**
     * PAINEL PRINCIPAL
     * Conter� as abas do sistema, � o painel principal da aplica��o
     */
    panelMain: null,

    /**
     * PAINEL RODAPE
     * Cont�m o rodap� do sistema. Cont�m informa��es gerais como contato de suporte e CopyRight
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
     * Inicializa Verifica��o dos atributos de configura��o exibidos
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
     * Retorna painel de navega��o
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