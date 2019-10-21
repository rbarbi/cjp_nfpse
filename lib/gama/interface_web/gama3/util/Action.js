/**
 * Classe Auxiliar para os Actions
 */
gama3.util.Action = function(namespace)
{

    return {

        /**
         * Namespace do projeto
         */
         namespace: namespace,

        //Mascara exibida no ACTION
        mascara: null,

         /**
         * Trata erros occoridos nas requisi��es AJAX de Formul�rios
         * @param form {Ext.form.BasicForm} Formul�rio que submeteu a requisi��o
         * @param action {Object} Objeto de resposta da requisi��o
         */
        showFailureForm: function(form, action)
        {
            this.hideMask();
            var msg = "";
            switch(action.failureType)
            {
                case Ext.form.Action.CLIENT_INVALID:
                    //Failure type returned when client side validation of the Form fails thus aborting a submit action.
                    msg = "Ocorreu uma falha na valida��o do formul�rio. Por favor, contate o suporte.";
                    break;
                case Ext.form.Action.CONNECT_FAILURE:
                    //Failure type returned when a communication error happens when attempting to send a request to the remote server.
                    msg = "Erro de comunica��o com o servidor. Tente novamente por favor. <br /><br />Caso o erro persista, contate o suporte.";
                    break;
                case Ext.form.Action.LOAD_FAILURE:
                    //Failure type returned when no field values are returned in the response's data property.
                    msg = "Nenhum valor foi retornado para esta requisi��o. Por favor, contate o suporte.";
                    break;
                case Ext.form.Action.SERVER_INVALID:
                    //Failure type returned when server side validation of the Form fails indicating that field-specific error messages have been returned in the response's errors property.
                    msg = "Ocorreram erros na valida��o do formul�rio no servidor. Por favor, verifique os dados digitados e tente novamente, Caso o erro persista, contate o suporte.";
                    break;
                default:
                    msg = 'Ol�, ocorreu um erro durante o envio dessa mensagem. Por favor, tente novamente e caso o erro persista contate o suporte';
            }

            Ext.Msg.alert('Erro na submiss�o do formul�rio', msg);
            //gama3.Debug.json(action);
        },

        /**
         * Trata erros occoridos nas requisi��es AJAX
         * @param result {Object} Objeto de resposta
         * @param request {Object} Objeto usado na configura��o do m�todo Ajax.request
         */
        showFailure: function(result, request)
        {
            this.hideMask();
            Ext.Msg.alert('Erro no envio da mensagem', result.responseText);
        },

        showMask: function(msg)
        {
            if(!msg)
                msg = "Carregando...";

            this.mascara = new Ext.LoadMask(window[this.namespace].Ds.getMain().getActiveTab().getEl(), {msg:msg});
            this.mascara.show();
        },

        hideMask: function()
        {
            this.mascara.hide();
            delete this.mascara;
        }
    }
};
