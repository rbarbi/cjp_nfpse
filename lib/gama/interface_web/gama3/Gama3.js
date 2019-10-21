/**
 * Possibilita o uso de interfaces simples em Javascript.
 * @param interfaces {Mixed Object/Array} Objeto ou Array de objetos de interface
 * @param obj {Object} Objeto JSON que implementa a Interface
 * @param nomeClasse {String} Nome da classe na qual está sendo aplicada a interface. Importante para Debug.
 */
gama3.useInterface = function(interfaces, obj, nomeClasse)
{
    //Pega nome da classe
    nomeClasse = nomeClasse || "Classe desconhecida";
    //Instancia variável que contém os atributos obrigatórios indicados pela interface
    var objInterface = {};

    //Se objeto passado for um array, concatena todos em um único objeto
    if(Ext.type(interfaces) == "array")
    {
        for(var i = 0; i<interfaces.length; i++)
            Ext.apply(objInterface, interfaces[i]);
    }
    else
        objInterface = interfaces;

    //Verifica se objeto da classe contém os atributos das classes Interface
    for(var x in objInterface)
    {
        if(!obj[x])
            throw("A Classe '"+nomeClasse+"' não possui o atributo '"+x+"' obrigatório pela classe Interface e não poderá ser instanciada");
    }
    return obj;
}

/**
 * Verifica se um Objeto contém os parametros Indicados, caso não possua lança uma exceção
 */
gama3.hasRequiredParms = function(obj, parms, className)
{
    for(var i = 0; i< parms.length; i++)
        if(!obj[parms[i]])
            throw new gama3.util.Exception({msg: "Parâmetro ' " + parms[i] + " ' não encontrado em ' " + className + " '"})
}

/**
 * Cria URL a partir de parâmetros indicados
 * @return {String}
 */
gama3.createUrl = function(m, u, a, acao)
{
    return "index.php?m="+m+"&u="+u+"&a="+a+"&acao="+acao;
}

