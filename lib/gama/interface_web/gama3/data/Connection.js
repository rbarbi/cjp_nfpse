 /**
  * Classe que sobrescreve Connection para construção de URL a partir dos atributos padrÃ£o do gama "m", "a", "u" e "acao".
  * @constructor
  * @param {Object} config a configuration object.
  */
gama3.data.Connection = function(config){

    if(config.acao && !config.url)
        config.url = "index.php?m=" + config.m + "&u=" + config.u + "&a=" + config.a + "&acao=" + config.acao;

    Ext.apply(this, config);
    Ext.data.Connection.superclass.constructor.call(this);
};

Ext.extend(gama3.data.Connection, Ext.data.Connection, {

    /**
     * Cria URL a partir dos atributos "m", "u", "a" e "acao".
     * @override
     */
    request: function(o)
    {
        if(o.acao && !o.url)
            o.url = "index.php?m=" + o.m + "&u=" + o.u + "&a=" + o.a + "&acao=" + o.acao;

        //Se url é um objeto formata a String conforme m,u,a,acao        
        if(typeof(o.url) == "object")
            o.url = "index.php?m=" + o.url.m + "&u=" + o.url.u + "&a=" + o.url.a + "&acao=" + o.url.acao;

        //Se for informada uma máscara, a utiliza
        if(o.mask)
            o.mask.show();
        
        gama3.data.Connection.superclass.request.apply(this, arguments);
    }
});

/**
 * Extend Ext.Ajax (Singleton) com anova classe
 * @singleton
 */
gama3.Ajax = Ext.apply(Ext.Ajax, new gama3.data.Connection({}));