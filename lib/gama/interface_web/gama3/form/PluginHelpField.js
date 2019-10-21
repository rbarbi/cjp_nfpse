/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.PluginHelpField
 * Plugin que adiciona um balão de ajuda ao lado de um campo de formulário tupo TextField, Select ou Trigger
 * @plugin
 */
gama3.form.PluginHelpField = function(){

    return {

        /**
         * Campo do formulário
         */
        field: null,

        /**
         * Inicializa Plugin
         */
        init: function(obj){
            this.field = obj;
            this.field.on('render', this.insertTip.createDelegate(this));
        },

        /**
         * Insere ToolTip de ajuda ao lado de um campo de formulário
         */
        insertTip: function()
        {
            //Pega elemento
            var el = this.field.getEl();

            //Se campo for Trigger, na hora em que este é redimensionado deve corrigir o posicionamento do help
            var trigger = el.next(".x-form-trigger");
            if(trigger)
                this.field.on('resize', this.resizeTip.createDelegate(this));

            var tip = el.insertSibling({tag:"span", "class":"gama3-form-icon-tip"}, "after");
            Ext.QuickTips.register({
                target: tip,
                title: 'Ajuda',
                text: this.field.help,
                width: 180
            });

            this.field.on("invalid", this.addMarginTip.createDelegate(this));
            this.field.on("valid", this.removeMarginTip.createDelegate(this));
        },

        /**
         * Ajusta posicionamento do ícone de ajuda caso campo seja do tipo Trigger
         * obj {Ext.form.Field}
         * newWidth {int} nova largura
         */
        resizeTip: function(obj, newWidth)
        {
            obj.getEl().next(".gama3-form-icon-tip").setLeft(newWidth);
        },

        /**
         * Adiciona Margem extra caso o campo esteja imválido, evitando sobrepor os ícones
         * @comp {Ext.form.Field} Campo do formulário
         */
        addMarginTip: function(comp)
        {
            comp.getEl().next(".gama3-form-icon-tip").addClass("gama3-form-icon-tip-margin");
        },

        /**
         * Remove Margem extra caso o campo esteja válido, evitando espaçamento desnecessário.
         * @comp {Ext.form.Field} Campo do formulário
         */
        removeMarginTip: function(comp)
        {
            comp.getEl().next(".gama3-form-icon-tip").removeClass("gama3-form-icon-tip-margin");
        }
    }
};

