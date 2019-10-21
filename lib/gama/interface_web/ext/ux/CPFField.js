Ext.ux.CPFField = function(config){
    var defConfig = {
        autocomplete: 'off',
        width: 100,
        soNumero: false,
        maxLength: (this.soNumero)? 11 : 14
    };
    Ext.applyIf(config,defConfig);
    Ext.ux.CPFField.superclass.constructor.call(this, config);
};

Ext.extend(Ext.ux.CPFField, Ext.form.TextField,{
    initEvents : function(){
        Ext.ux.CPFField.superclass.initEvents.call(this);
        this.el.on("keydown",this.stopEventFunction,this);
        this.el.on("keyup", this.formatCPF,this);
        this.el.on("keypress", this.stopEventFunction,this);
        //this.el.on("focus", this.startCPF,this);
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

    startCPF : function(){
        var field = this.el.dom;
        if(field.value == ''){
            field.value = '';
            if(this.soNumero){
                field.value = '00000000000';
            }else{
                field.value = '000.000.000-00';
            }
        }
    },

    formatCPF : function(evt){
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

        if (length < 11) {
            var qtn = '';
            for(var i = 0; i < 11 - length; i++){
                qtn = qtn + '0';
            }
            value = qtn+value;
            length = 11;
        }

        if(this.soNumero){
            field.value = value;
        }else{
            var result = '';
            result = value.substr(0,3)+'.'+value.substr(3,3)+'.'+value.substr(6,3)+'-'+value.substr(9);
            field.value = result;
        }
    }
});

Ext.ComponentMgr.registerType('cpffield', Ext.ux.CPFField);