/*
 *  Objeto de Visualiza��o de um Usu�rio
 */

gama3.object.Object = Ext.extend(Ext.Panel, {

    //Ext.data.Record contendo os dados de um objeto
    record: null,

    //Painel com a visualiza��o inical do objeto
    panelIndex: null,

    initComponent: function()
    {
        if(!this.record)
            throw new gama3.util.Exception({title: "record n�o foi instanciado", msg: "� necess�rio que toda visualiza��o de um objeto contenha um atributo record", fileName: './gama3/object/Object.js'})

        if(!this.panelIndex)
            throw new gama3.util.Exception({title: "panelIndex n�o foi instanciado", msg: "� necess�rio que toda visualiza��o de um objeto contenha um atributo panelIndex", fileName: './gama3/object/Object.js'})

        var arrItems = [this.panelIndex];
        if(this.tabPanel)
            arrItems.push(this.tabPanel);

        Ext.apply(this, {
            frame:true,
            border: true,
            closable: true,
            items:arrItems,
            autoScroll: true
        });

        gama3.object.Object.superclass.initComponent.apply(this, arguments);

        this.addEvents("editItem, deleteItem");
    },

    editItem: function()
    {
        this.fireEvent("editItem", this, this.record);
    },

    deleteItem: function()
    {
        this.fireEvent("deleteItem", this, this.record)
    }

})


