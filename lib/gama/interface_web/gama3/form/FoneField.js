gama3.form.FoneField = Ext.extend(Ext.form.TextField, {

	width: 120,	
	//default field label
	fieldLabel:'Telefone',

	//permite deixar ou não, em branco.
	allowBlank:true,
	blankText: "Por favor, digite CEP.",

	initComponent: function(){

		Ext.apply(this, {
			vtype: "fone",
			enableKeyEvents:true
		});

		this.on('keyup',function(field, e){		
			
			var tecla = e.getKey();
			if ( e.getKey()!= 46 && e.getKey() != 8 && e.getKey() != 37 && e.getKey() != 38 && e.getKey() != 39 && e.getKey() != 40 )
			{
				var vr = new String(this.getValue());
				v = this.getValue();
				var max = 14;
	
				v = v.substring (0,max);
				v=v.replace(/\D/g,"");
				v=v.replace(/(\d{2})(\d)/,"($1) $2");
				v=v.replace(/(\d{4})(\d)/,"$1.$2");
				this.setValue(v);
			}
			return true;
				
		});

		gama3.form.FoneField.superclass.initComponent.apply(this, arguments);
	}

});

Ext.apply(Ext.form.VTypes, {
	fone : function(v) {
		if (v.length==14 || v.length == 0)
			return true;
	},
	foneMask : /^[0-9]$/ ,
	foneText : 'Você deve informar um número de telefone válido. Ex.: (xx) xxxx.xxxx .'
});