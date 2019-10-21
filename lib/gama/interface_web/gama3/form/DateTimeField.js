/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.DateTimeField
 * @extends Ext.ux.form.DateTime
 * Cria um campo de formulário contendo um campo para seleção de data e um de hora.
 * @param {Object} config Configuration options
 */
gama3.form.DateTimeField = Ext.extend(Ext.ux.form.DateTime, {

    /**
     * @cfg {String} hiddenFormat Format of datetime used to store value in hidden field
     * and submitted to server (defaults to 'd/m/Y H:i:s' that is mysql format)
     */
    hiddenFormat:'d/m/Y H:i:s'
    /**
     * @cfg {String} dateFormat Format of DateField. Can be localized. (defaults to 'd/m/Y')
     */
    ,dateFormat:'d/m/Y'
    /**
     * @cfg {String} timeFormat Format of TimeField. Can be localized. (defaults to 'H:i')
     */
    ,timeFormat:'H:i'


    // {{{
    /**
     * @private
     * @override
     * creates DateField and TimeField and installs the necessary event handlers
     */
    ,initComponent:function() {
        // call parent initComponent
        gama3.form.DateTimeField.superclass.initComponent.call(this);

        // create DateField
        var dateConfig = Ext.apply({}, {
             allowBlank: this.allowBlank
            ,msgTarget: "qtip"
            ,blankText: "É necessário informar a data"
            ,id:this.id + '-date'
            ,format:this.dateFormat || Ext.form.DateField.prototype.format
            ,width:this.timeWidth
            ,selectOnFocus:this.selectOnFocus
            ,listeners:{
                  blur:{scope:this, fn:this.onBlur}
                 ,focus:{scope:this, fn:this.onFocus}
            }
        }, this.dateConfig);

        //Alterado do original para que o campo de data contivesse nossos atalhos. (antes: Ext.form.DateField )
        this.df = new gama3.form.DateField(dateConfig);
        this.df.ownerCt = this;
        delete(this.dateFormat);

        // create TimeField
        var timeConfig = Ext.apply({}, {

             allowBlank: this.allowBlank
            ,blankText: this.blankText || "Você deve informar a data e a hora."

            ,id:this.id + '-time'
            ,format:this.timeFormat || Ext.form.TimeField.prototype.format
            ,width:this.timeWidth
            ,selectOnFocus:this.selectOnFocus
            ,listeners:{
                  blur:{scope:this, fn:this.onBlur}
                 ,focus:{scope:this, fn:this.onFocus}
            }
        }, this.timeConfig);
        this.tf = new Ext.form.TimeField(timeConfig);
        this.tf.ownerCt = this;
        delete(this.timeFormat);

        // relay events
        this.relayEvents(this.df, ['focus', 'specialkey', 'invalid', 'valid']);
        this.relayEvents(this.tf, ['focus', 'specialkey', 'invalid', 'valid']);

    },

    updateTime: function()
    {
        var t = this.tf.getValue();
        if(t && !(t instanceof Date)) {
            t = Date.parseDate(t, this.tf.format);
        }
        if(t && !this.df.getValue()) {
            this.initDateValue();
            this.setDate(this.dateValue);
        }
        if(this.dateValue instanceof Date) {
            if(t) {
                this.dateValue.setHours(t.getHours());
                this.dateValue.setMinutes(t.getMinutes());
                this.dateValue.setSeconds(t.getSeconds());
            }
            else {
                this.dateValue.setHours(0);
                this.dateValue.setMinutes(0);
                this.dateValue.setSeconds(0);
            }
        }
    },

    updateDate:function()
    {
        var d = this.df.getValue();
        if(d) {
            if(!(this.dateValue instanceof Date)) {
                this.initDateValue();
                if(!this.tf.getValue()) {
                    this.dateValue.setHours(0);
                    this.dateValue.setMinutes(0);
                    this.dateValue.setSeconds(0);
                    this.setTime(this.dateValue);
                }
            }
            this.dateValue.setMonth(0); // because of leap years
            this.dateValue.setFullYear(d.getFullYear());
            this.dateValue.setMonth(d.getMonth(), d.getDate());
//            this.dateValue.setDate(d.getDate());
        }
        else {
            this.dateValue = '';
            this.setTime('');
        }
    } // eo function updateDate

}); // eo extend

// register xtype
Ext.reg('gama3.form.datetimefield', gama3.form.DateTimeField);
