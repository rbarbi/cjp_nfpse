/**
 * Classe para encapsulamento do objeto de Excessão
 */
gama3.util.Exception = Ext.extend(Ext.util.Observable, {

    constructor: function(config)
    {
        Ext.apply(this, {
            code: config.code || 0,
            //Nome do Erro
            name: config.name || config.msg || "Excessão",
            //Mensagem do Erro
            msg: config.message || "Excessão Lançada",
            //Linha do erro
            lineNumber: config.lineNumber || -1,
            //Nome do arquivo aonde se encontra a excessão
            fileName: config.fileName || "undefined fileName",
            //Escopo em que a excessão foi lançada (nome do mÃ©todo, classe, etc)
            scope: config.scope || "undefined scope"
        });

        //Inicializa Construtor da superclasse.
        gama3.util.Exception.superclass.constructor.apply(this, arguments);

        //Adiciona Evento de Ecessão lançada
        this.addEvents("fireException");

        //Lança excessão fireException
        this.fireException();
    },

    /**
     * Lança excessão "fireExcepetion" que Ã© disparada sempre que uma excessão Ã© lançada.
     * Como parÃ¢metro envia apenas o prÃ³prio objeto da exceção.
     */
    fireException: function()
    {
        this.fireEvent("fireException", this);
    },

    toString: function()
    {
        Ext.Msg.alert(this.name, "Name: "+this.name+
                                 "<br />Code: "+this.code+
                                 "<br />Line: "+this.lineNumber+
                                 "<br />File Name: "+this.fileName+
                                 "<br />Scope: "+this.scope+
                                 "<br />Msg: "+this.msg
        );
    }

});

