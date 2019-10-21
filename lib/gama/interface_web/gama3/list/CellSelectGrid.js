/**
 * Listagem coim modelo de cele��o em c�lulas de coluna que contenham "clickFucntion"
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

    /* ---- Modelo de Sele��o ---- */

    /**
     * Cria modelo de Sele��o do GRID
     * Este modelo � por c�lula e execua a fun��o do atributo "clickFunction" da coluna
     * enviando o record da linha como par�metro
     */
    createSelectionModel: function()
    {
        //Modelo de Sele��o de C�lula
        this.cellSelectionModel = new Ext.grid.CellSelectionModel({
            singleSelect: true,
            listeners: {
                beforecellselect:  { fn: this.beforeCellSelect.createDelegate(this) },
                cellselect:  { fn: this.cellSelect.createDelegate(this) }
            }
        })
    },

    /*
     * Quando uma c�lula for clicada, verifica se existe o atributo "clickFunction" para a respectiva coluna.
     * Este hanlder tamb�m evita clicks em c�lulas que n�o possuam nenhuma fun��o cadastrada.
     * @param {Ext.RowSelectionModel} sm
     * @param {int} rowIndex �ndice da linha selecionada
     * @param {int} colIndex �ndice da coluna selecionada
     */
    beforeCellSelect: function(sm, rowIndex, colIndex)
    {
        //Pega objeto da coluna clicada.
        var coluna = this.getColumnModel().getColumnById(colIndex);

        //Se existe, continua evento de sele��o, se n�o, o pausa.
        if(coluna.clickFunction)
            return true;
        else
            return false;
    },

    /*
     * executa fun��o "clickFunction" de uma coluna.
     * "clickFunction" receber� como atributo o Record do objeto clicado.
     * @param {Ext.RowSelectionModel} sm
     * @param {int} rowIndex �ndice da linha selecionada
     * @param {int} colIndex �ndice da coluna selecionada
     */
    cellSelect: function(sm, rowIndex, colIndex)
    {
        //Pega objeto da coluna clicada.
        var coluna = this.getColumnModel().getColumnById(colIndex);
        //Pega Record
        var record = this.getStore().getAt(rowIndex);
        //Executa Fun��o
        coluna.clickFunction(record);
    }

});