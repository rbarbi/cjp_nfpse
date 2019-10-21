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
gama3.form.AutoCompleteLocal = Ext.extend(Ext.form.ComboBox, gama3.useInterface(gama3.form.InterfaceFieldFormAlt, {

    //Largura default
    width: 140,

    /**
     * @override
     * @private
     */
    initComponent: function()
    {
        Ext.apply(this, {
            resizable: this.resizable || true,
            minListWidth: this.minListWidth || 240,
            minHeight: this.minHeight || 80,
			triggerAction: 'all'
        })
        gama3.form.AutoCompleteLocal.superclass.initComponent.apply(this, arguments);

        //Ao sair do campo, caso o que estiver preenchido no displayField1
        this.on('blur', function(){
            /*var value = this.hiddenField.value;
            var displayValue = this.getRawValue().toUpperCase();

            if(displayValue.length <= 0 )
                return false;

            var rec = this.getStore().find(this.valueField, value);
            if(rec >= 0)
                return false;

            this.getStore().each(function(rec){
                if(rec.get(this.displayField).toUpperCase == displayValue){
                    this.setValue(rec.get(this.valueField))
                    this.fireEvent("select", this, rec)
                }
            }, this)*/                     
            if(this.getRawValue().length == 0) {
                this.setValue(null);
                this.fireEvent("clear", this);
            } else if(this.getValue()) {
                this.getStore().clearFilter();
                var index = this.getStore().find(this.valueField, this.getValue());
                var rec = this.getStore().getAt(index);

                if(!rec){                    
                    this.clearValue();
                    return false;
                }

                if(this.getRawValue() == rec.get(this.displayName)){
                    return false;
                }

                this.setValue( this.getValue() );
            } else {
                this.getStore().clearFilter();                
                var index = this.getStore().find(this.valueField, this.getValue());                
                if(index == -1)
                    this.markInvalid(this.errorMsgBlur)                
            }
        }, this);        
        this.addEvents("clear");
    },

    /**
     * @public
     * @return {Ext.data.Store}
     */
    getStore: function(){
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

    validate: function(){          
        var value = this.hiddenField.value;
        var displayValue = this.getRawValue();
        //Procura Record para o value existente
        var record = this.getStore().findBy(function(rec){
            if(rec.get(this.valueField) == value){
                return true;
            }
        }.createDelegate(this));

        //Se record existe,
        //verifica se o valor do display é o mesmo do record selecionado,
        //se não for, lança evento para limpar e marca como inválido
        if(record >= 0){
            record = this.getStore().getAt(record);
            if(record.get(this.displayField) == displayValue){
                this.clearInvalid();
                return true;
            }
            else{
                this.hiddenField.value = "";
                this.fireEvent('clear', this);
                if(this.errorMsg)
                    this.markInvalid(this.errorMsg);
                return false;
            }
        } else {
            //Caso não haja nenhum record selecionado e exista algum valor digitado, marca como inválido
            if(displayValue.length > 0){
                if(this.errorMsg)
                    this.markInvalid(this.errorMsg);
                return false;
            }
            else
                return gama3.form.AutoCompleteLocal.superclass.validate.apply(this, arguments);
        }
    }
}, "gama3.form.Select"));
