Ext.ux.CNPJField = function(config){
    var defConfig = {
        autocomplete: 'off',
        width: 140,
        soNumero: false,
        maxLength: (this.soNumero)? 15 : 19
    };
    Ext.applyIf(config,defConfig);
    Ext.ux.CNPJField.superclass.constructor.call(this, config);
};

Ext.extend(Ext.ux.CNPJField, Ext.form.TextField,{
    initEvents : function(){
        Ext.ux.CNPJField.superclass.initEvents.call(this);
        this.el.on("keydown",this.stopEventFunction,this);
        this.el.on("keyup", this.formatCNPJ,this);
        this.el.on("keypress", this.stopEventFunction,this);
        //this.el.on("focus", this.startCNPJ,this);
    },

    KEY_RANGES : {
        numeric: [48, 57],
        padnum: [96, 105]
    },

    isInRange : function(charCode, range) {
        return charCode >= range[0] && charCode <= range[1];
    },

    stopEventFunction : function(evt) {
        var key = evt.getKey();

        if (this.isInRange(key, this.KEY_RANGES["padnum"])) {
            key -= 48;
        }

        if ( (( key>=41 && key<=122 ) || key==32 || key==8 || key>186) && (!evt.altKey && !evt.ctrlKey) ) {
            evt.stopEvent();
        }
    },

    startCNPJ : function(){
        var field = this.el.dom;
        if(field.value == ''){
            field.value = '';
            if(this.soNumero){
                field.value = '000000000000000';
            }else{
                field.value = '000.000.000/0000-00';
            }
        }
    },

    formatCNPJ : function(evt){
        var key = evt.getKey();

        if (this.isInRange(key, this.KEY_RANGES["padnum"])) {
            key -= 48;
        }

        var character = (this.isInRange(key, this.KEY_RANGES["numeric"]) ? String.fromCharCode(key) : "");
        var field = this.el.dom;
        var value = (field.value.replace(/\D/g, "").substr(1) + character).replace(/\D/g, "");
        var length = value.length;

        if ( character == "" && length > 0 && key == 8) {
            length--;
            value = value.substr(0,length);
            evt.stopEvent();
        }

        if(field.maxLength + 1 && length >= field.maxLength) return false;

        if (length < 15) {
            var qtn = '';
            for(var i = 0; i < 15 - length; i++){
                qtn = qtn + '0';
            }
            value = qtn+value;
            length = 15;
        }

        if(this.soNumero){
            field.value = value;
        }else{
            var result = '';
            result = value.substr(0,3)+'.'+value.substr(3,3)+'.'+value.substr(6,3)+'/'+value.substr(9,4)+'-'+value.substr(13);
            field.value = result;
        }
    }
});

Ext.ComponentMgr.registerType('cnpjfield', Ext.ux.CNPJField);