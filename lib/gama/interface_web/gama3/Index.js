/*
 * Inicia Funcionalidades Globais do gama3
 */

Ext.namespace("gama3",
              "gama3.form",
              "gama3.data",
              "gama3.util",
              "gama3.list",
              "gama3.report",
              "gama3.dashboard",
              "gama3.view",
			  "gama3.object",
              "gama3.panel",
              "gama3.ux");

gama3.Index = function()
{
    Ext.onReady(function(){

       try
       {
           ///Inicializa ToolTips
           Ext.QuickTips.init();

           //Exibe mensagens de erro de formul�rio em um �cone a direita do campo
           Ext.form.Field.prototype.msgTarget = 'side';

           //Atalhos
           Ext.ns("G3");
           G3 = gama3;
       }
       catch(e)
       {
           gama3.Debug.json(e);
       }

    });

}(); //Par�nteses acrescentados para execu��o do construtor;





