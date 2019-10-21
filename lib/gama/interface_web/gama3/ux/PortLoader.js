/**
* esse componente carrega automaticamente uma URL, dentro de um portLet
*/

gama3.ux.PortLoader = Ext.extend(Ext.ux.Portlet, {

        /**
         * @cfg url {String} URL que retornará o template para o Portlet
         */

        /**
         * novo Construtor
         * @override
         */
        initComponent: function() {

            this.tools = [
                {
                    id:'refresh',
                    handler: this.recarregar.createDelegate(this)
                }
            ];

            Ext.apply(this, {
                    tools: this.tools,
                    bodyStyle: "padding: 5px"
            });

            gama3.ux.PortLoader.superclass.initComponent.apply(this, arguments);
        },

       /**
        * Realiza Requisição AJAX para buscar template do Portlet
        */
        requisicao: function () {

            if(!this.mascara)
                this.mascara = new Ext.LoadMask( this.getEl(), {msg:"Carregando..."});

            this.mascara.show();

            var html = Ext.Ajax.request({
                url: this.url,
                method: 'POST',
                success: function (response)
                {
                    this.getEl().child('.x-panel-body').update(response.responseText);
                    this.mascara.hide();
                }.createDelegate(this),
                    failure: function() {
                }
            });
        },

        /**
         * @override
         */
        onRender: function() {
        	gama3.ux.PortLoader.superclass.onRender.apply(this, arguments);
        	this.requisicao();

        	this.findParentByType("portal").on('show',function(){  this.recarregar()}.createDelegate(this));
                /*function(container, thisComponent){

                        alert(container.getId());

                });*/
        },

        /**
         * Recarrega Template
         */
        recarregar:function() {
            this.requisicao()
        }
});

