/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.MoneyField
 * @extends Ext.ux.MoneyField
 * Cria um campo de texto com máscara de valor Monetário
 * @param {Object} config Configuration options
 */
gama3.form.MoneyField = Ext.extend(Ext.ux.MoneyField, {

    /*
     * @override
     */
    initComponent : function(){

        //Propriedades estíticas - Não podem ser modificados pelo config;
        Ext.apply(this, {
            fieldLabel: this.fieldLabel || "Valor",
            width: this.width || 120
        });

        gama3.form.MoneyField.superclass.initComponent.call(this);

		this.addEvents('afterkeyup');
    },

    /*
     * @override
     */
    initEvents : function(){
        gama3.form.MoneyField.superclass.initEvents.call(this);
        this.el.on("keyup", this.apagaCampo.createDelegate(this));
    },

    /**
     * Handler para apagar campo quando for pressionado "del" ou forem apagados todos os valores digitados
     * @eventHandler
     */
    apagaCampo: function(evt)
    {
        var key = evt.getKey();
        var field = evt.getTarget();

        //SE tecla for backspace e não houver nenhum valor digitado  -> apaga campo
        if(key == evt.BACKSPACE && (field.value == "0,00"))
            this.el.dom.value = "";

        //SE tecla for del -> apaga campo
        if(key == evt.DELETE)
            this.el.dom.value = "";

        //SE tecla for tab e não houver nenhum valor digitado -> apaga campo
        if(key == evt.TAB && (field.value == "0,00"))
            this.el.dom.value = "";
    },

	mapCurrency : function(evt) {
        switch (this.format) {
            case 'BRL':
                this.currency = 'R$';
                this.currencyPosition = 'left';
                this.formatCurrency(evt, 2,',','.');
                break;

            case 'EUR':
                this.currency = ' ?';
                this.currencyPosition = 'right';
                this.formatCurrency(evt, 2,',','.');
                break;

            case 'USD':
                this.currencyPosition = 'left';
                this.currency = '$';
                this.formatCurrency(evt, 2);
                break;

            default:
                this.formatCurrency(evt, 2);
        }

		this.fireEvent('afterKeyUp', this, evt);
    }

});
Ext.reg('gama3.form.moneyfield', gama3.form.MoneyField);