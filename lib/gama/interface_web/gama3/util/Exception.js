/**
 * Classe para encapsulamento do objeto de Excess�o
 */
gama3.util.Exception = Ext.extend(Ext.util.Observable, {

    constructor: function(config)
    {
        Ext.apply(this, {
            code: config.code || 0,
            //Nome do Erro
            name: config.name || config.msg || "Excess�o",
            //Mensagem do Erro
            msg: config.message || "Excess�o Lan�ada",
            //Linha do erro
            lineNumber: config.lineNumber || -1,
            //Nome do arquivo aonde se encontra a excess�o
            fileName: config.fileName || "undefined fileName",
            //Escopo em que a excess�o foi lan�ada (nome do método, classe, etc)
            scope: config.scope || "undefined scope"
        });

        //Inicializa Construtor da superclasse.
        gama3.util.Exception.superclass.constructor.apply(this, arguments);

        //Adiciona Evento de Ecess�o lan�ada
        this.addEvents("fireException");

        //Lan�a excess�o fireException
        this.fireException();
    },

    /**
     * Lan�a excess�o "fireExcepetion" que é disparada sempre que uma excess�o é lan�ada.
     * Como parâmetro envia apenas o próprio objeto da exce��o.
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

