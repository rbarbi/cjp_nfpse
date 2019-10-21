/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.CNPJField
 * @extends Ext.ux.CNPJField
 * Cria um campo de texto com máscara e validação para CNPJ
 * @param {Object} config Configuration options
 */
gama3.form.CNPJField = Ext.extend(Ext.ux.CNPJField, {

    /**
     * Inicializa Componente
     * @override
     */
    initComponent : function(){

        this.initVType();

        //Propriedades estíticas - Não podem ser modificados pelo config;
        Ext.apply(this, {
            fieldLabel: this.fieldLabel || "CNPJ",
            vtype: 'cnpj',
            width: this.width || 140
        });

        gama3.form.CNPJField.superclass.initComponent.call(this);
    },

    /**
     * Inicializa Eventos do Componente
     * @override
     */
    initEvents : function(){
        gama3.form.CNPJField.superclass.initEvents.call(this);
        this.el.on("keyup", this.apagaCampo.createDelegate(this));
    },

    /**
     * Handler para apagar campo quando for pressionado "del" ou forem apagados todos os valores digitados
     * @eventHandler
     */
    apagaCampo: function(evt)
    {
        //Pega tecla digitada
        var key = evt.getKey();
        //pega field
        var field = evt.getTarget();

        //SE tecla for backspace e não houver mais nenhum dado digitado pelo usuário -> apaga campo
        if(key == evt.BACKSPACE && (field.value == "000.000.000/0000-00" || field.value == "000000000000000"))
            this.el.dom.value = "";

        //SE tecla for del -> apaga campo
        if(key == evt.DELETE)
            this.el.dom.value = "";

        //SE tecla for tab e não houver mais nenhum dado digitado pelo usuário -> apaga campo
        if(key == evt.TAB && (field.value == "000.000.000/0000-00" || field.value == "000000000000000"))
            this.el.dom.value = "";
    },

    /**
     * Inicializa vType CNPJ deste componente
     * @private
     */
    initVType: function(){
        Ext.apply(Ext.form.VTypes, {
			cnpj : this.validaCNPJ.createDelegate(this),
			cnpjMask : /^[0-9]$/,
			cnpjText : 'Você deve digitar um CNPJ válido no formato \'xx.xxx.xxx/xxxx-xx\''
		});
    },

    /**
     * Valida CNPJ (função adquirida junto com o Ext.ux.CNPJField)
     * @eventHandler
     */
    validaCNPJ: function(CNPJ){
        CNPJ = CNPJ.replace(/\D/g, "");
        CNPJ = CNPJ.replace(/^0+/, "");
        if(parseInt(CNPJ) == 0){
            return false;
        }else{
            var g=CNPJ.length-2;
            if(this.realTestaCNPJ(CNPJ,g) == 1){
                g=CNPJ.length-1;
                if(this.realTestaCNPJ(CNPJ,g) == 1){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    },

    /**
     * Valida CNPJ (função adquirida junto com o Ext.ux.CNPJField)
     * @private
     * @return boolean
     */
    realTestaCNPJ: function(CNPJ, g)
    {
        var VerCNPJ=0;
        var ind=2;
        var tam;
        for(var f=g;f>0;f--){
            VerCNPJ+=parseInt(CNPJ.charAt(f-1))*ind;
            if(ind>8){
                ind=2;
            }else{
                ind++;
            }
        }
        VerCNPJ%=11;
        if(VerCNPJ==0 || VerCNPJ==1){
            VerCNPJ=0;
        }else{
            VerCNPJ=11-VerCNPJ;
        }
        if(VerCNPJ!=parseInt(CNPJ.charAt(g))){
            return(0);
        }else{
            return(1);
        }
    }
});

Ext.reg('gama3.form.cnpjfield', gama3.form.CNPJField);