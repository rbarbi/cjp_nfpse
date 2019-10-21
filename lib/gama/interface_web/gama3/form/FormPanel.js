gama3.form.FormPanel = Ext.extend(Ext.FormPanel, {

    initComponent: function()
    {
        this.url = this.url || "index.php?m=" + this.m + "&u=" + this.u + "&a=" + this.a + "&acao=" + this.acao;

		this.trataCamposFormulario();
		
        Ext.apply(this, {
            frame: this.hasOwnProperty('frame') ? this.frame : true,
            buttonAlign: "left",
            autoScroll: this.hasOwnProperty('autoScroll') ? this.autoScroll : true
        });

        gama3.form.FormPanel.superclass.initComponent.apply(this, arguments);

        this.addEvents("save", "cancel",
            /**
             * Executado após load dos fields
             * @event fieldsLoaded
             * @param {Ext.form.FormPanel} este form
             */
            "fieldsLoaded",
            /**
             * Executado após load dos dados
             * @event dataLoaded
             * @param {Ext.form.FormPanel} este form
             */
            "dataLoaded"
        );

        //Necessário para que o form funcione
        this.form.url = this.url;

    },

    /*
     * @fireEvent save(FormPanel this)
     */
    eventSave: function()
    {
        this.fireEvent("save", this);
    },

    /*
     * @fireEvent cancel(FormPanel this)
     */
    eventCancel: function()
    {
        this.fireEvent("cancel", this);
    },

    /**
     * Carrega dados do formulário dinamicamente.
     * Caso seja especificado na resposta o método também carrega os ítens de comboBox's
     * @param record {Record} record do ítem que será carregado no formulário devendo conter obrigatoriamente apenas o ID
     * @param config {Object} objeto de configuração contendo:
                    config.m,
                    config.u,
                    config.a,
                    config.acao,
                    config.waitMessage {String} Mensagem exibida enquanto form está sendo carregado [opcional],
                    config.params {Object} objeto contendo os parâmetros da requisição AJAX, se nÃ£o for definido apenas o ID é enviado como parâmetro [opcional],
                    config.constructorRecord {Function} Caso o parâmetro record seja de um construtor diferente do que o usado pelo formulário, este parâmetro deve apontar para o construtor correto. [opcional]
     */
    loadData: function(config)
    {
        //Cria variável de parâmetros
        var parms = config.params || {};
        //Se Record existe
        if(config.record){
            var id = config.record;
            if(isNaN(config.record))
                id = config.record.get("id");

            parms.id = id;
        }

        var mascara = new Ext.LoadMask(this.getEl(), {msg:config.waitMessage || "Carregando"});
        mascara.show();

        gama3.Ajax.request({
            m: config.m,
            u: config.u,
            a: config.a,
            acao: config.acao,
            params: parms,
            method: 'POST',
            success: function ( result, request) {
                    var resultJson = Ext.util.JSON.decode(result.responseText);

                    //Pega valores dos fields
                    if(resultJson.fields)
                        this._loadFields(resultJson.fields);

                    //Carrega Data
                    if(resultJson.data)
                        this._loadData(resultJson.data, config.record);

                    mascara.hide();
                    delete mascara;
            }.createDelegate(this),
            failure: function ( result, request) {
                mascara.hide();
                delete mascara;
                Ext.MessageBox.alert(G3.text.error, 'Erro na requisição, por favor, tente novamente.');
            }
        })
    },

    _loadData: function(data, record)
    {
        //Inicializa Variáveis; "id" identificará o campo e "field" a referência ao objeto
        var field, id;
        for(id in data)
        {
            //SE id é atributo do formulário, executa setValue
            if(field = this.field[id])
            {
                field.setValue(data[id]);
            }
            //Se não...
            else
            {
                //Procura id no name do formulário e executa setValue()
                if(field = this.getForm().findField(id))
                    field.setValue(data[id]);
            }
        }

        this.fireEvent("dataLoaded", this, data);
    },

    _loadFields: function(fields)
    {
        //Para cada atributo de fields, preenche um campo do formulário com os valores passados
        for(var x in fields)
        {
            if(typeof fields[x] == "function")
                continue;

            //Pega field
            var field = this.field[x];

            //Executa função que carrega os dados para dentro do field
            field.showFormAltLoad(fields[x]);
        }

        this.fireEvent("fieldsLoaded", this, fields);
    },

	/**
	 * Para cada campo do formulário,
	 * verifica se ele é obrigatório e o marca se for.
	 * E adiciona toolTip de Informação caso o campo possua o atributo info.
	 */
	trataCamposFormulario: function(){
		var field;
		for(var nameField in this.field){
			field = this.field[nameField];
			field.on('render', function(field){
				var label = this.findLabel(field);
				if(label){
					if(!field.allowBlank){
						this.marcaComoObrigatorio(field, label);
					}
					if(field.info){
						this.insertTip(field, label);
					}
				}
			}, this);
			
		}
	},

	/** ---- FIELDs ---- **/

	marcaComoObrigatorio: function(field, label){		
		label.update(label.dom.innerHTML+"*");
	},

	/**
	 * Insere ToolTip de ajuda ao lado de um campo de formulário
	 */
	insertTip: function(field, label){
		var helpImage = label.createChild({
			tag: 'img'
			,src: './lib/gama/interface_web/gama3/resources/img/icones/info.png'
			,style: 'position: absolute; right: 0;'
		});

		Ext.QuickTips.register({
			target:  helpImage
			,text: field.info
			,title: field.fieldLabel
			,enabled: true
		});
	},

	findLabel: function(field) {

		var wrapDiv = null;
		var label = null

		//find form-item and label
		wrapDiv = field.getEl().up('div.x-form-item');
		if(wrapDiv){
			label = wrapDiv.child('label');
		}
		if(label) {
			return label;
		}
	}

});
