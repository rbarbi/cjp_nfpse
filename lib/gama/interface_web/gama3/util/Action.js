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
         * Trata erros occoridos nas requisições AJAX de Formulários
         * @param form {Ext.form.BasicForm} Formulário que submeteu a requisição
         * @param action {Object} Objeto de resposta da requisição
         */
        showFailureForm: function(form, action)
        {
            this.hideMask();
            var msg = "";
            switch(action.failureType)
            {
                case Ext.form.Action.CLIENT_INVALID:
                    //Failure type returned when client side validation of the Form fails thus aborting a submit action.
                    msg = "Ocorreu uma falha na validação do formulário. Por favor, contate o suporte.";
                    break;
                case Ext.form.Action.CONNECT_FAILURE:
                    //Failure type returned when a communication error happens when attempting to send a request to the remote server.
                    msg = "Erro de comunicação com o servidor. Tente novamente por favor. <br /><br />Caso o erro persista, contate o suporte.";
                    break;
                case Ext.form.Action.LOAD_FAILURE:
                    //Failure type returned when no field values are returned in the response's data property.
                    msg = "Nenhum valor foi retornado para esta requisição. Por favor, contate o suporte.";
                    break;
                case Ext.form.Action.SERVER_INVALID:
                    //Failure type returned when server side validation of the Form fails indicating that field-specific error messages have been returned in the response's errors property.
                    msg = "Ocorreram erros na validação do formulário no servidor. Por favor, verifique os dados digitados e tente novamente, Caso o erro persista, contate o suporte.";
                    break;
                default:
                    msg = 'Olá, ocorreu um erro durante o envio dessa mensagem. Por favor, tente novamente e caso o erro persista contate o suporte';
            }

            Ext.Msg.alert('Erro na submissão do formulário', msg);
            //gama3.Debug.json(action);
        },

        /**
         * Trata erros occoridos nas requisições AJAX
         * @param result {Object} Objeto de resposta
         * @param request {Object} Objeto usado na configuração do método Ajax.request
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
