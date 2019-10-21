/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.CPFField
 * @extends Ext.ux.CPFField
 * Cria um campo de texto com máscara e validação para CPF
 * @param {Object} config Configuration options
 */
gama3.form.CPFField = Ext.extend(Ext.ux.CPFField, {

    /*
     * @override
     */
    initComponent : function(){

        this.initVType();

        //Propriedades estíticas - Não podem ser modificados pelo config;
        Ext.apply(this, {
            fieldLabel: this.fieldLabel || "CPF",
            width: this.width || 120,
            vtype: 'cpf'
        });

        gama3.form.CPFField.superclass.initComponent.call(this);
    },

    /*
     * @override
     */
    initEvents : function(){
        gama3.form.CPFField.superclass.initEvents.call(this);
        this.el.on("keyup", this.apagaCampo.createDelegate(this));
    },

    /**
     * Handler para apagar campo quando for pressionado "del" ou forem apagados todos os valores digitados
     * @eventHandler
     */
    apagaCampo: function(evt)
    {
        var key = evt.getKey();
        var field = evt.getTarget();

        //SE tecla for backspace e não houver mais nenhum dado digitado pelo usuário -> apaga campo
        if(key == evt.BACKSPACE && (field.value == "000.000.000-00" || field.value == "00000000000"))
            this.el.dom.value = "";

        //SE tecla for del -> apaga campo
        if(key == evt.DELETE)
            this.el.dom.value = "";

        //SE tecla for tab e não houver mais nenhum dado digitado pelo usuário -> apaga campo
        if(key == evt.TAB && (field.value == "000.000.000-00" || field.value == "00000000000"))
            this.el.dom.value = "";
    },

    /*
     * Inicializa vType CPF deste componente
     * @private
     */
    initVType: function(){
        Ext.apply(Ext.form.VTypes, {
			cpf : this.validaCPF.createDelegate(this),
			cpfMask : /^[0-9]$/,
			cpfText : 'Você deve digitar um CPF válido no formato \'xxx.xxx.xxx-xx\''
		});
    },

    /*
     * Valida CPF (função adquirida junto com o Ext.ux.CPFField)
     * @private
     */
    validaCPF: function(CPF){

        if(CPF == "")
            return true;

        var i;
        var s = CPF.replace(/\D/g, "");
        if(parseInt(s) == 0){return false;}
        var c = s.substr(0,9);
        var dv = s.substr(9,2);
        var d1 = 0;
        for (i = 0; i < 9; i++){
            d1 += c.charAt(i)*(10-i);
        }
        if (d1 == 0){
            return false;
        }
        d1 = 11 - (d1 % 11);
        if (d1 > 9) d1 = 0;
        if (dv.charAt(0) != d1){
            return false;
        }
        d1 *= 2;
        for (i = 0; i < 9; i++){
            d1 += c.charAt(i)*(11-i);
        }
        d1 = 11 - (d1 % 11);
        if (d1 > 9) d1 = 0;
        if (dv.charAt(1) != d1){
            return false;
        }
        return true;
    }
});
Ext.reg('gama3.form.cpffield', gama3.form.CPFField);