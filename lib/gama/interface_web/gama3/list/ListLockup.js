/**
 * Classe de Grid Padrão para listagem de objetos
 */

gama3.list.ListLockup = Ext.extend(Ext.grid.GridPanel, {

    /**
     * @cfg searchFields {array} Array dos campos de busca.
     * Será usado apenas se o atributo de configuração "tbar" não existir.
     * Os ítens do array devem estar no formado [id, nomeExibicao, nomeServidor]. Ex: ["0", "Nome de Usuário", "username]
     */

    /**
     * @cfg waitMessage {string} Mensagem exibida enquanto os dados da listagem são carregados
     */

    /**
     * @cfg pageSize {int} Número de ítens por página
     */

    _findField:null,
    comboCampos: null,
    //Campo de Busca {gama3.form.SearchField}
    _SearchField: null,
    searchField:null,
    _valueDigitado:null,

    loadStore: true,
  
    initComponent: function()
    {
        //Modelo de Seleção por coluna.
        this.rowSelectionModel = new Ext.grid.RowSelectionModel({
               singleSelect: true,
               listeners: {
                   rowselect: this.select.createDelegate(this)
               }
        });

        //Sobrescreve atributos - Não podem ser modificados pelo config;
        Ext.apply(this, {
            sm: this.rowSelectionModel,
            tbar: this.tbar || this.createBarSearch(),
            ctCls: "gama3-list-lockup"
        })

        gama3.list.ListLockup.superclass.initComponent.apply(this, arguments);

   
        this.addEvents("select");
    },

    onRender: function()
    {
        gama3.list.ListLockup.superclass.onRender.apply(this, arguments);

        this.loadMascara = new Ext.LoadMask(this.getId(), {store: this.store, msg:this.waitMessage || "Carregando..."});
        
        if (!this._findField){
            if(this.loadStore){                
                this.store.load();
            }
        }
            
    },

    /**
     * Lança evento após seleção de um ítem
     * @param sm {Ext.grid.RowSelectionModel}
     * @param index {int} Index da linha selecionada
     * @param record {Ext.data.Record} ítem adicionado
     */
    select: function(sm, index, record)
    {
        this.fireEvent("select", record);
    },

    /**
     * Caso não seja informado um array de ítems para a toolBar insere os campos de busca.
     * Nesse caso o atributo "searchFields" é obrigatório.
     */
    createBarSearch: function()
    {
        //Cria Combo dos Campos
        this.comboCampos = new gama3.form.SearchFieldSelect({
                //SearchFields é obrigatório
                value:this._findField,
                searchFields: this.searchFields
            });

       
        this._SearchField =  new gama3.form.SearchField({            	
                width: 180,
                store: this.store,
                value: this._valueDigitado,
                comboCampos: this.comboCampos
            });    
            
        var barSearch = [
            new Ext.form.Label({html: " Busca: "}),
            {xtype: 'tbspacer'},
              this.comboCampos,
            {xtype: 'tbspacer'},
            this._SearchField
           
        ];

        return barSearch;
    },

    /**
     * Recarrega Store com os dados preenchidos nos campos de busca
     * @public
     */
    reloadStore: function()
    {
        this._SearchField.onTrigger2Click();
    },

    /* ---- SearchField ---- */
    
    /**
     * Coloca Valor no campo de busca
     * @public
     */
    setSearchValue:function(value){
    	this._SearchField.setRawValue(value);
        this._SearchField.setValue(value);
        this._SearchField.hasSearch = true;
        this._SearchField.triggers[0].show();
    },

    /**
     * Retorna Valor do Campo de Busca
     * @public
     */
    getSearchValue:function(){
    	return this._SearchField.getRawValue();
    },

    /**
     * Limpa Valor do campo de busca
     * @public
     */
    clearSearchValue: function()
    {
        this._SearchField.setValue("");
        this._SearchField.setRawValue("");
        this._SearchField.triggers[0].hide();
    }

});