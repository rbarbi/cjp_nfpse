/**
 * Cria componente que une um formulário simples e uma listagem para inclusão rápida de ítens
 */
gama3.ux.AgileInclude = Ext.extend(Ext.Panel, {

    /**
     * @cfg {String} URL para inclusão dos ítens
     */
    urlInsert: null,

    /**
     * @cfg {String} URL para alteração dos ítens
     */
    urlUpdate: null,

    /**
     * @cfg
     */
    configForm: null,

    /**
     * @cfg
     */
    configList: null,

    /**
     * @public
     */
    form: null,

    /**
     * @public
     */
    list: null,

    /**
     * Record salvo quando houver algum ítem em edição
     * @type {Ext.Data.Record}
     * @private
     */
    recordEdit: null,

    /**
     * @override
     */
    initComponent: function()
    {

        //initForm
        this.form = new gama3.ux.AgileIncludeForm(this.configForm);
        //initList
        this.list = new gama3.ux.AgileIncludeList(this.configList);

        Ext.apply(this, {
            layout: "border",
            split: true,
            frame: true,
            closable: true,
            items: [
                this.form,
                this.list
            ]
        });

        gama3.ux.AgileInclude.superclass.initComponent.apply(this, arguments);
    },

    /**
     * @override
     */
    initEvents: function()
    {
        gama3.ux.AgileInclude.superclass.initEvents.apply(this, arguments);

        //Evento ao clicar em salvar do formulário
        this.form.on("save", this.saveForm, this);

        //Ao incluir um ítem na lista deve ser enviada uma requisição com estes dados
        this.list.getStore().on("add", this.saveItem, this);
    },

    /**
     * Salva campos do formulário
     * Inclui ítem na listagem e executa requisição AJAX para alteração no BD
     * @handler
     * @param form {gama3.ux.AgileInclude}
     */
    saveForm: function(form)
    {
        //Cria record com os novos valores do formulário
        var record = new this.list.Record(form.getForm().getValues());

        //Verifica se Existe Record de edição
        if(this.recordEdit)
        {
            //Altera os valores do Record antigo com os novos campos do formulário
            for(var field in record.data)
                this.recordEdit.set(field, record.get(field));

            //Define Status como Loading
            this.recordEdit.set("_status", "L");

            //Realiza requisição de atualização
            Ext.Ajax.request({
                url: record.get("id") ? this.urlUpdate : this.urlInsert,
                params: record.data,
                method: 'POST',
                success: this.successSaveItem.createDelegate(this, [this.recordEdit], 2),
                scope: this
            });
        }
        //Se não então inclui novo ítem
        else
        {
            //Define Status como Loading
            record.set("_status", "L");
            //Insere Record no topo da Lista
            this.list.getStore().insert(0, record);
        }

        //Reseta formulário
        form.getForm().reset();
        //Define foco no formulário
        form.fields[0].focus();
        //Zera record de edição
        this.recordEdit = null;
    },

    /**
     * Adiciona ítem no servidor enviando o Index correspondente da listagem
     * @param store {Ext.data.Store}
     * @param records {Ext.data.Record[]}
     * @param index {int}
     */
    saveItem: function(store, records, index)
    {
        //Pega record
        var record = store.getAt(index);

        //Realiza requisição AJAX
        Ext.Ajax.request({
            url: this.urlInsert,
            params: record.data,
            method: 'POST',
            success: this.successSaveItem.createDelegate(this, [record], 2)
        });
    },

    /**
     * Pega retorno da requisição AJAX para inserção e/ou atualização dos dados
     * @param result
     * @param request
     * @param record {Ext.data.Record} Record do ítem correspondente na listagem
     */
    successSaveItem: function(result, request, record)
    {
        var result = Ext.util.JSON.decode(result.responseText);
        if(result.success)
        {
            record.set("_status", "S");
            record.set("id", result.data.id);
            record.set("_error", false);
        }
        else
        {
            record.set("_status", "E");
            record.set("_error", result.data.msg);
        }
    }

});




/**
 * Classe representando o formulário do componente gama3.ux.AgileInclude
 * @extend {Ext.form.FormPanel}
 */
gama3.ux.AgileIncludeForm = Ext.extend(Ext.form.FormPanel, {

    //Campos do formulário
    fields: null,

    /**
     * @override
     */
    initComponent: function()
    {
        for(var i = 0; i<this.fields.length; i++)
            this.fields[i].on("specialkey", this.enterEvent, this);

        Ext.apply(this, {
            region: this.region,
            width: this.width,
            height: this.height,
            margins: "0 10 0 0",
            split: true,
            items: this.fields,
            buttonAlign: "left",
            title: this.title || "Formulário",
            frame:true,
            buttons: [
                {
                    text: "Salvar",
                    handler: this.eventSave.createDelegate(this),
                    icon: "./lib/gama/interface_web/gama3/resources/img/icones/apply.png",
                    cls:"x-btn-text-icon"
                },{
                    text: "Limpar",
                    handler: this.eventClear.createDelegate(this),
                    icon: "./lib/gama/interface_web/gama3/resources/img/icones/excluir.png",
                    cls:"x-btn-text-icon"
                }
            ]
        });

        gama3.ux.AgileIncludeForm.superclass.initComponent.apply(this, arguments);

        this.addEvents("save", "clear");
    },

    /* ---- Hendler Events ---- */

    /**
     * Lança evento de save caso o usuário use o botão "Enter"
     * @handler
     */
    enterEvent: function(field, event)
    {
        if(event.getKey() != Ext.EventObject.ENTER )
            return false;

        if(this.getForm().isValid())
            this.eventSave();
    },

    /*
     * @fireEvent save(FormPanel this)
     */
    eventSave: function()
    {
        this.fireEvent("save", this);
    },

    /*
     * Limpa formulário
     * @fireEvent clear(FormPanel this)
     */
    eventClear: function()
    {
        this.getForm().reset();
        this.fields[0].focus();
        this.fireEvent("clear", this);
    }

});




/**
 * Listagfem do componente gama3.ux.AgileInclude
 * @extend {Ext.grid.GridPanel}
 */
gama3.ux.AgileIncludeList = Ext.extend(Ext.grid.GridPanel, {

    Record: null,

    initComponent: function()
    {
        this.addControlFields();
        //Cria Record que será usado pelos ítens inseridos na listagem
        this.Record = Ext.data.Record.create(this.fields);

        this.addControlColumns();

        this.createSelectionModel();

        Ext.apply(this, {
            region: "center",
            title: this.title || "Listagem",
            frame: true,
            autoScroll: true,
            columns: this.columns,
            store: new Ext.data.Store({
                data: [],
                reader: new Ext.data.JsonReader({}, this.Record)
            }),
            sm: this.cellSelectionModel
        });

        gama3.ux.AgileIncludeList.superclass.initComponent.apply(this, arguments);
    },

    /**
     * Adiciona Campos de Controle
     */
    addControlFields: function()
    {
        /*
         * Campos de Controle inseridos no Record
         */
        this.fields.push(
            //Status do Ítem {L -> Loading; P -> Pendente; E -> Erro; S -> sucesso }
            {name: "_status"},
            //Mensagem de Erro
            {name: "_error"}
        );
    },

    /**
     * Adiciona Colunas de Controle
     */
    addControlColumns: function()
    {
        var columnsTemp = new Array();

        /*
         * Insere Colunas com ícone de Status e Edição à Listagem
         */
        columnsTemp.push(
            //Status
            {
                header: "",
                width: 30,
                dataIndex: "_status",
                clickFunction: this.clickFunctionStatus.createDelegate(this),
                renderer: this.rendererStatus.createDelegate(this)
            },
            //Edição
            {
                header: "",
                dataIndex: "_edit",
                width: 30,
                clickFunction: this.clickFunctionEdit.createDelegate(this),
                renderer: this.rendererEdit.createDelegate(this)
            }
        );

        for(var i = 0; i< this.columns.length; i++)
            columnsTemp.push(this.columns[i]);

        this.columns = columnsTemp;
    },

    /* ---- clickFunction's ---- */

    /**
     * Caso Exista erro, o exibe em um Alert
     * @param record {Ext.data.Record}
     */
    clickFunctionStatus: function(record)
    {
        if(record.get("_error"))
            Ext.Msg.alert("Mensagem de Erro",record.get("_error"));
        else
            Ext.Msg.alert("Sucesso", "Ítem incluído com sucesso");
    },

    /**
     * Executa Bind com o formulário para edição
     * @param record {Ext.data.Record}
     */
    clickFunctionEdit: function(record)
    {
        //Carrega dados do Record para o form
        this.ownerCt.form.getForm().loadRecord(record);
        //Salva Record em edição
        this.ownerCt.recordEdit = record;
        //Coloca Foco no primeiro campo do formulário
        this.ownerCt.form.fields[0].focus();
    },

    /* ---- Renderer's ---- */

    /**
     * Renderiza Imagem correspondente ao Status
     */
    rendererStatus: function(val)
    {
        switch(val)
        {
            case "S": return "<a href='#' ><img src='./lib/gama/interface_web/gama3/resources/img/icones/apply.png' alt='Sucesso' /></a>";
            case "E": return "<a href='#' ><img src='./lib/gama/interface_web/gama3/resources/img/icones/cancel.png' alt='Erro' /></a>";
            case "L": return "<a href='#' ><img style='width: 16px' src='./lib/gama/interface_web/gama3/resources/img/icones/loading.gif' alt='Carregando' /></a>";
            default: return "Pendente";
        }
    },

    /**
     * Renderiza Coluna com imagem de edição
     */
    rendererEdit: function(val)
    {
        return "<a href='#' ><img src='./mod/" + gama3.m + "/interface_web/gama3/resources/img/icones/editar.png' alt='Editar' /></a>";
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