/**
 * Possibilita o uso de interfaces simples em Javascript.
 * @param interfaces {Mixed Object/Array} Objeto ou Array de objetos de interface
 * @param obj {Object} Objeto JSON que implementa a Interface
 * @param nomeClasse {String} Nome da classe na qual est� sendo aplicada a interface. Importante para Debug.
 */
gama3.useInterface = function(interfaces, obj, nomeClasse)
{
    //Pega nome da classe
    nomeClasse = nomeClasse || "Classe desconhecida";
    //Instancia vari�vel que cont�m os atributos obrigat�rios indicados pela interface
    var objInterface = {};

    //Se objeto passado for um array, concatena todos em um �nico objeto
    if(Ext.type(interfaces) == "array")
    {
        for(var i = 0; i<interfaces.length; i++)
            Ext.apply(objInterface, interfaces[i]);
    }
    else
        objInterface = interfaces;

    //Verifica se objeto da classe cont�m os atributos das classes Interface
    for(var x in objInterface)
    {
        if(!obj[x])
            throw("A Classe '"+nomeClasse+"' n�o possui o atributo '"+x+"' obrigat�rio pela classe Interface e n�o poder� ser instanciada");
    }
    return obj;
}

/**
 * Verifica se um Objeto cont�m os parametros Indicados, caso n�o possua lan�a uma exce��o
 */
gama3.hasRequiredParms = function(obj, parms, className)
{
    for(var i = 0; i< parms.length; i++)
        if(!obj[parms[i]])
            throw new gama3.util.Exception({msg: "Par�metro ' " + parms[i] + " ' n�o encontrado em ' " + className + " '"})
}

/**
 * Cria URL a partir de par�metros indicados
 * @return {String}
 */
gama3.createUrl = function(m, u, a, acao)
{
    return "index.php?m="+m+"&u="+u+"&a="+a+"&acao="+acao;
}

