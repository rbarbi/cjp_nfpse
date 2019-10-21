/*
 * gama3 Interface Web 0.2
 */


/**
 * @class gama3.form.Association
 * @extends Ext.form.Association
 * Cria um Association que para associa��o de �tens n x 1 e n x n
 * @param {Object} config Configuration options
 */

gama3.form.Association = Ext.extend(Ext.Panel, {

    /**
     * @cfg {Object} linked
     * Configura��o do grid dos �tens associados.
     * <pre><code>
     * {
           store: aStore,
           title: "Titulo",
           waitMessage: "Loading Itens",
           columns: arrayListColumns
       }
     * </code></pre>
     */

    /**
     * @cfg {Object} unlinked
     * Configura��o do grid dos �tens n�o associados.
     * <pre><code>
     * {
           store: aStore,
           title: "Titulo",
           waitMessage: "Loading Itens",
           columns: arrayListColumns
       }
     * </code></pre>
     */

    /**
     * @cfg {Record}
     * Record do Dono da associa��o
     */
    recordMaster: null,

    /**
     * GRID esquerdo dos n�o associados
     * @public
     */
    gridUnlinked: null,

    /**
     * GRID direito dos associados
     * @public
     */
    gridLinked: null,

    /**
     * Painel Central
     * @private
     */
    centerPanel: null,

    /**
     * @override
     */
    initComponent: function()
    {
        this.createGridUnlinked();
        this.createGridLinked();
        this.createCenterPanel();

        Ext.apply(this, {
            title: this.title || "Association",
            height: 340,
            frame: true,
            layout: "border",
            items: [this.gridUnlinked, this.centerPanel, this.gridLinked]
        })

        gama3.form.Association.superclass.initComponent.apply(this, arguments);

        this.addEvents(
            /**
             * @event add
             * Lan�ado quando usu�rio clica no bot�o "Adicionar Selecionados"
             * @param {gama3.form.Association}
             * @param {Ext.data.Record} recordMaster indicado no objeto de configura��o
             * @param {Ext.data.Record[]} array dos �tens selecionados para associa��o
             */
            "add",
            /**
             * @event remove
             * Lan�ado quando usu�rio clica no bot�o "Remover Selecionados"
             * @param {gama3.form.Association}
             * @param {Ext.data.Record} recordMaster indicado no objeto de configura��o
             * @param {Ext.data.Record[]} array dos �tens selecionados para desassocia��o
             */
            "remove"
        );
    },

    /**
     * @override
     */
    onRender: function()
    {
        gama3.form.Association.superclass.onRender.apply(this, arguments);
        //Redimensiona GRID's
        this.resizeGrids();
        //Evento ao redimensionar componente
        this.on("resize", this.resizeGrids.createDelegate(this));
        //Evento no "dono" do componente
        this.ownerCt.on("show", this.loadStores.createDelegate(this));
    },

    /**
     * @override
     */
    onShow: function()
    {
        gama3.form.Association.superclass.onShow.apply(this, arguments);
        this.loadStores();
    },

    /**
     * Carrega Store's
     * @public
     */
    loadStores: function()
    {
        if(this.gridUnlinked)
            this.gridUnlinked.getStore().reload();
        if(this.gridLinked)
            this.gridLinked.getStore().reload();
    },

    /**
     * Redimensiona GRID's
     * @public
     */
    resizeGrids: function()
    {
        //Pega largura do container
        var width = this.ownerCt.getBox().width;
        //Calcula largura do grid (um pouco menos da metade para ter espa�o entre eles)
        var wGrid = (width - 30) / 2;
        //adiciona nova largura aos componentes
        this.gridUnlinked.setWidth(wGrid);
        this.gridLinked.setWidth(wGrid);
    },

    /* ---- Create GRIDS ---- */

    /**
     * Cria GRID de dados n�o associados
     * @private
     */
    createGridUnlinked: function()
    {
        Ext.apply(this.unlinked, {
            height: 300,
            width: 200,
            region: "west",
            tbar: [
                new Ext.Button({
                    icon: "./lib/gama/interface_web/gama3/resources/img/icones/add.png",
                    cls:"x-btn-text-icon",
                    text: "Adicionar Selecionados",
                    handler: this._addItems.createDelegate(this)})
            ]
        });
        this.gridUnlinked = new gama3.list.Association(this.unlinked);
        
    },

    /**
     * Cria GRID de dados associados
     * @private
     */
    createGridLinked: function()
    {
        Ext.apply(this.linked, {
            region: "east",
            height: 300,
            width: 200,
            tbar: [
                new Ext.Button({
                    icon: "./lib/gama/interface_web/gama3/resources/img/icones/remove.png",
                    cls:"x-btn-text-icon",
                    text: "Remover Selecionados",
                    handler: this._removeItems.createDelegate(this)})
            ]
        });
        this.gridLinked = new gama3.list.Association(this.linked);
    },

    /**
     * Cria p�inel central
     * @private
     */
    createCenterPanel: function()
    {
        this.centerPanel = new Ext.Panel({
            region: 'center',
            width: 20
        })

    },

    /* ---- Action Methods ---- */

    /**
     * Lan�a evento 'add' com �tens adicionados
     * @private
     * @launchEvent {add}
     */
    _addItems: function()
    {
        //Pega �tens n�o associados selecionados
        var items = this.gridUnlinked.getSelectionModel().getSelections();
        //lan�a evento enviando este componente, o record do dono da associa��o e os �tens selecionados
        if(!this.fireEvent("add", this, this.recordMaster, items)){
            return false;
        }
    },

    /**
     * Adiciona �tens "n�o associados" para o grid dos �tens "associados"
     * @public
     */
    addItems: function()
    {
        //Pega �tens selecionados
        var items = this.gridUnlinked.getSelectionModel().getSelections();
        //pega Store's
        var unlinked = this.gridUnlinked.getStore();
        var linked = this.gridLinked.getStore();
        //Para cada �tem, remove dos n�o associados e insere nos associados
        for(var i = 0; i<items.length; i++){
            unlinked.remove(items[i]);
            linked.add(items[i]);
        }
    },

    /**
     * Adiciona �tens "n�o associados" para o grid dos �tens "associados" a partir do ID dos mesmos
     * @param ids {array} ids para associa��o
     * @public
     */
    addItemsById: function(ids)
    {
        //pega Store's
        var unlinked = this.gridUnlinked.getStore();
        var linked = this.gridLinked.getStore();

        //Para cada �tem, remove dos n�o associados e insere nos associados
        for(var i = 0; i<ids.length; i++){
            //procura Record no store unlinked
            var record = unlinked.getAt(unlinked.find("id", ids[i]));

            if(record)
            {
                unlinked.remove(record);
                linked.add(record);
            }
            else
            {
                throw("Association n�o encontrou id '"+ids[i]+ "' para associa��o");
            }
        }
    },

    /**
     * Lan�a evento 'remove' com �tens removidos
     * @private
     * @launchEvent {add}
     */
    _removeItems: function()
    {
        //Pega �tens associados selecionados
        var items = this.gridLinked.getSelectionModel().getSelections();
        //lan�a evento enviando este componente, o record do dono da associa��o e os �tens selecionados
        if(!this.fireEvent("remove", this, this.recordMaster, items)){
            return false;
        }
    },

    /**
     * Remove �tens "associados" e os coloca no grid dos �tens "n�o associados"
     * @public
     */
    removeItems: function()
    {
        var items = this.gridLinked.getSelectionModel().getSelections();
        var store1 = this.gridUnlinked.getStore();
        var store2 = this.gridLinked.getStore();
        for(var i = 0; i<items.length; i++){
            store2.remove(items[i]);
            store1.add(items[i]);
        }
    },

    /**
     * Remove �tens "associados" para o grid dos �tens "n�o associados" a partir do ID dos mesmos
     * @param ids {array} ids para desassocia��o
     * @public
     */
    removeItemsById: function(ids)
    {
        //pega Store's
        var unlinked = this.gridUnlinked.getStore();
        var linked = this.gridLinked.getStore();

        //Para cada �tem, remove dos n�o associados e insere nos associados
        for(var i = 0; i<ids.length; i++){
            //procura Record no store unlinked
            var record = linked.getAt(linked.find("id", ids[i]));

            if(record)
            {
                linked.remove(record);
                unlinked.add(record);
            }
            else
            {
                throw("Association n�o encontrou id '"+ids[i]+ "' para desassocia��o");
            }
        }
    }
});