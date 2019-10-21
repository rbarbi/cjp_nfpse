/**
 * Store com funcionalidades adapatada ao gama3
 */

gama3.data.Store = function(config)
{

	//Atributos de configura��o padr�o
	this.config = {
      loaded: false
    }

    //Construtor

    //Sobrescreve atributos de configura��o
	Ext.apply(this.config, config);
	//Dispara construtor da superclasse
	gama3.data.Store.superclass.constructor.call(this, this.config );

	this.on("load", this.doLoaded, this);
}

/**
 * Extend Stroe principal do Ext
 */
Ext.extend(gama3.data.Store, Ext.data.Store, {

    /**
     * Marca como carregado
     * @void
     */
	doLoaded: function()
	{
		this.loaded = true;
	},

    /**
     * Verifica se um Store j� est� carregado
     * @return {boolean}
     */
	isLoaded: function()
	{
		return this.loaded;
	}
});