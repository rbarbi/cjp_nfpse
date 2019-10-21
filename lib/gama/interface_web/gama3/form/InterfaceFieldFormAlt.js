/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.InterfaceFieldFormAlt
 * Cria uma interface que deve ser usada por todos os campos de formul�rio
   que precisam carregar listas em formul�rios de altera��o como Select e TreeField.
 * @interface
 */
gama3.form.InterfaceFieldFormAlt = {

    /**
     * Fun��o executada pelo m�todo gama3.form.FormPanel.loadData()
     * respons�vel por carregar os dados de um formul�rio.
     * Todos os fields que exigem o carregamento de Listas dever�o implementar esta interface
     * @param data {Mixed} contendo os dados da resposta json para este field
     * @void
     */
    showFormAltLoad: function(data){
        //code
    }
};