/**
 * Listagem coim modelo de celeção em células de coluna que contenham "clickFucntion"
 * @extend {Ext.grid.GridPanel}
 */
gama3.list.CellSelectGrid = Ext.extend(Ext.grid.GridPanel, {
   
    initComponent: function()
    {
        this.createSelectionModel();

        Ext.apply(this, {
            sm: this.cellSelectionModel
        });

        gama3.list.CellSelectGrid.superclass.initComponent.apply(this, arguments);
    },

    /* ---- Modelo de Seleção ---- */

    /**
     * Cria modelo de Seleção do GRID
     * Este modelo é por célula e execua a função do atributo "clickFunction" da coluna
     * enviando o record da linha como parâmetro
     */
    createSelectionModel: function()
    {
        //Modelo de Seleção de Célula
        this.cellSelectionModel = new Ext.grid.CellSelectionModel({
            singleSelect: true,
            listeners: {
                beforecellselect:  { fn: this.beforeCellSelect.createDelegate(this) },
                cellselect:  { fn: this.cellSelect.createDelegate(this) }
            }
        })
    },

    /*
     * Quando uma célula for clicada, verifica se existe o atributo "clickFunction" para a respectiva coluna.
     * Este hanlder também evita clicks em células que não possuam nenhuma função cadastrada.
     * @param {Ext.RowSelectionModel} sm
     * @param {int} rowIndex índice da linha selecionada
     * @param {int} colIndex índice da coluna selecionada
     */
    beforeCellSelect: function(sm, rowIndex, colIndex)
    {
        //Pega objeto da coluna clicada.
        var coluna = this.getColumnModel().getColumnById(colIndex);

        //Se existe, continua evento de seleção, se não, o pausa.
        if(coluna.clickFunction)
            return true;
        else
            return false;
    },

    /*
     * executa função "clickFunction" de uma coluna.
     * "clickFunction" receberá como atributo o Record do objeto clicado.
     * @param {Ext.RowSelectionModel} sm
     * @param {int} rowIndex índice da linha selecionada
     * @param {int} colIndex índice da coluna selecionada
     */
    cellSelect: function(sm, rowIndex, colIndex)
    {
        //Pega objeto da coluna clicada.
        var coluna = this.getColumnModel().getColumnById(colIndex);
        //Pega Record
        var record = this.getStore().getAt(rowIndex);
        //Executa Função
        coluna.clickFunction(record);
    }

});