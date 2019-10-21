gama3.form.CEPField = Ext.extend(Ext.form.TextField, {

	width: 80,	
	//default field label
	fieldLabel:'CEP',

	//permite deixar ou não, em branco.
	allowBlank:true,
	blankText: "Por favor, digite CEP.",

	initComponent: function(){

		Ext.apply(this, {
			vtype: "cep",
			enableKeyEvents:true
		});

		this.on('keyup',function(field, e){		
			
			var tecla = e.getKey();
			if ( e.getKey()!= 46 && e.getKey() != 8 && e.getKey() != 37 && e.getKey() != 38 && e.getKey() != 39 && e.getKey() != 40 )
			{
				var vr = new String(this.getValue());
				v = this.getValue();
				var max = 9;
	
				v = v.substring (0,max);
				v=v.replace(/\D/g,"");
				v=v.replace(/(\d{5})(\d)/,"$1-$2");
				this.setValue(v);
			}
			return true;
				
		});

		gama3.form.CEPField.superclass.initComponent.apply(this, arguments);
	}

});

Ext.apply(Ext.form.VTypes, {
	cep : function(v) {
		if(v.length == 0){
			return true;
		}
		
		return /^[0-9]{5}-[0-9]{3}$/.test(v);
	},
	cepMask : /^[0-9]$/ ,
	cepText : 'Você deve informar um CEP válido. Ex.: 00000-000.'
});