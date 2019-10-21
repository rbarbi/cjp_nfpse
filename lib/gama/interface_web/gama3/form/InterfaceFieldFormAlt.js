/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.InterfaceFieldFormAlt
 * Cria uma interface que deve ser usada por todos os campos de formulário
   que precisam carregar listas em formulários de alteração como Select e TreeField.
 * @interface
 */
gama3.form.InterfaceFieldFormAlt = {

    /**
     * Função executada pelo método gama3.form.FormPanel.loadData()
     * responsável por carregar os dados de um formulário.
     * Todos os fields que exigem o carregamento de Listas deverão implementar esta interface
     * @param data {Mixed} contendo os dados da resposta json para este field
     * @void
     */
    showFormAltLoad: function(data){
        //code
    }
};