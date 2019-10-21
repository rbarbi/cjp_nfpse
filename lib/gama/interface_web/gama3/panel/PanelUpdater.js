/**
 * gama3.panel.PanelUpdater
 * Cria um painel que � atualizado com HTML vindo de uma requisi��o ao servidor.
 */

gama3.panel.PanelUpdater = Ext.extend(Ext.Panel, {

    /**
     * @cfg m
     * @required
     * M�dulo da requisi��o AJAX
     */

    /**
     * @cfg u
     * @required
     * Sub-M�dulo da requisi��o AJAX
     */

    /**
     * @cfg a
     * @required
     * Action da requisi��o AJAX
     */

    /**
     * @cfg acao
     * @required
     * A��o da requisi��o AJAX
     */

    /**
     * @cfg params
     * Parâmetros da requisi��o AJAX
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
        //Se frame == true, adiciona conte�do ao elemento body interno
        if(el.child('.x-panel-body'))
            el = el.child('.x-panel-body');
        //Pega updater e realiza chamada do m�todo update
        el.getUpdater().update({
                url: gama3.createUrl(this.m, this.u, this.a, this.acao),
                params: this.parms || {},
				callback: this.callback
        });
    }
})
