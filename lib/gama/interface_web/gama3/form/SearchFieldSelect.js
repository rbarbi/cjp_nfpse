gama3.form.SearchFieldSelect = Ext.extend(gama3.form.Select, {

    initComponent: function()
    {
        Ext.apply(this, {
            //Dados sempre locais
            mode: 'local',
            //Store contendo um array com os campos que podem ser usados na busca
            store: new Ext.data.Store({
                data: this.searchFields,
                reader: new Ext.data.ArrayReader({id: "id"}, ["id", "campo_exibicao","campo"])
            }),
            //Exibe o rótulo dos ítens
            displayField: "campo_exibicao",
            name: 'campo_exibicao',
            //Envia a coluna dos ítens
            valueField: "campo",
            hiddenName: "campo"

        })
        gama3.form.SearchFieldSelect.superclass.initComponent.apply(this, arguments);
    }

});