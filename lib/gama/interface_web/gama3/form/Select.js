/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.Select
 * @extends Ext.form.ComboBox
 * Estende comboBox definindo os atributos necessário para que este se transforme em um Select
 * @param {Object} config Configuration options
 * @implements gama3.form.InterfaceFieldFormAlt
 */
gama3.form.Select = Ext.extend(Ext.form.ComboBox, gama3.useInterface(gama3.form.InterfaceFieldFormAlt, {

    //Largura default
    width: 140,

    /**
     * @override
     * @private
     */
    initComponent: function()
    {
        Ext.apply(this, {

            //Não pode ser editado diretamente
            editable: false,
            //Um elemento deve ser selecionado | não permite que o usuário digite diretamente
            forceSelection: this.forceSelection || true,
            //Elimina auto-completar, exibindo sempre todos os ítens do Store Associado
            triggerAction: "all",

            resizable: this.resizable || true,
            minListWidth: this.minListWidth || 240
        })
        gama3.form.Select.superclass.initComponent.apply(this, arguments);
    },

    /**
     * @public
     * @return {Ext.data.Store}
     */
    getStore: function()
    {
        return this.store;
    },

    showFormAltLoad: function(data)
    {
       if(this.getStore())
           this.getStore().removeAll();
       else
           return;

        var temp = [];
        //Para cada ítem, transforma-o em um Record e o adiciona ao Store
        for(var i = 0; i<data.length; i++)
            temp[i] = new Ext.data.Record(data[i]);

        this.getStore().add(temp);
    },

	getRecord: function(){
		if(this.getValue()){
			return this.getStore().getAt( this.getStore().find(this.valueField, this.getValue()) );
		} else {
			return false;
		}
	}

}, "gama3.form.Select"));