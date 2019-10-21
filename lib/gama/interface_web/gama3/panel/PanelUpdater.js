/**
 * gama3.panel.PanelUpdater
 * Cria um painel que é atualizado com HTML vindo de uma requisição ao servidor.
 */

gama3.panel.PanelUpdater = Ext.extend(Ext.Panel, {

    /**
     * @cfg m
     * @required
     * Módulo da requisição AJAX
     */

    /**
     * @cfg u
     * @required
     * Sub-Módulo da requisição AJAX
     */

    /**
     * @cfg a
     * @required
     * Action da requisição AJAX
     */

    /**
     * @cfg acao
     * @required
     * Ação da requisição AJAX
     */

    /**
     * @cfg params
     * ParÃ¢metros da requisição AJAX
     */

    initComponent: function()
    {
        gama3.hasRequiredParms(this, ["m","u","a","acao"], "gama3.panel.PanelUpdater");
        gama3.panel.PanelUpdater.superclass.initComponent.apply(this, arguments);
    },

    onRender: function()
    {
        gama3.panel.PanelUpdater.superclass.onRender.apply(this, arguments);
        this.updatePanel();
    },

    updatePanel: function()
    {
        //Pega El
        var el = this.getEl();
        //Se frame == true, adiciona conteúdo ao elemento body interno
        if(el.child('.x-panel-body'))
            el = el.child('.x-panel-body');
        //Pega updater e realiza chamada do método update
        el.getUpdater().update({
                url: gama3.createUrl(this.m, this.u, this.a, this.acao),
                params: this.parms || {},
				callback: this.callback
        });
    }
})
