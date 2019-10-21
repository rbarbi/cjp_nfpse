/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.DateField
 * @extends Ext.form.DateField
 * Cria um campo para sele��o de data com m�scara, atalhos e valida��o
 * @param {Object} config Configuration options
 */
gama3.form.DateField = Ext.extend(Ext.form.DateField, {

    /*
     * @override
     * @private
     */
    initComponent: function()
    {
        Ext.apply(this, {
            enableKeyEvents: true,
            format: "d/m/Y"
        })
        gama3.form.DateField.superclass.initComponent.apply(this, arguments);
    },

    /*
     * @override
     * @eventHandler
     */
    onRender: function()
    {
        gama3.form.DateField.superclass.onRender.apply(this, arguments);

        this.on("keyup", this.onKeyup.createDelegate(this));
    },

    /*
     * Aplica m�scara de data e uso de atalhos.
     * @eventHandler
     */
    onKeyup: function(textfield, event)
    {
        var v = this.getRawValue();
        if(v.length == 1)
        {
            var date = new Date();
            var dia = date.getDate();
            var mes = date.getMonth()+1;
            var ano = date.getFullYear();

            //Data Atual
            if(v == "=")
                    v = this.formata({"dia":dia, "mes":mes, "ano":ano});
            //Data de ontem
            else if (v == "-")
                    v = this.formata( this.avancaDias(-1, dia, mes, ano) );
            //Data de amanh�
            else if (v == "+")
                    v = this.formata( this.avancaDias(1, dia, mes, ano) );
            //Data do in�cio do m�s
            else if (v == ".")
                    v = this.formata({"dia":1, "mes":mes, "ano":ano});
            //Data do in�cio do ano;
            else if (v == "*")
                    v = this.formata({"dia":1, "mes":1, "ano":ano})
            else
                    v = v.replace(/\D/g,"");
        }
        else
        {
                //Remove tudo o que n�o � d�gito
                v=v.replace(/\D/g,"");
                //Remove d�gitos a partir do 8�
                v=v.replace(/^(\d{8})(\d{1,})/,"$1");
                //Coloca barra entre o segundo e o terceiro d�gitos
                v=v.replace(/^(\d{2})(\d)/,"$1/$2");
                //Coloca barra entre o quarto e o quinto d�gitos
                v=v.replace(/^(\d{2})\/(\d{2})(\d)/,"$1/$2/$3");
        }
        this.setRawValue(v);
    },

    /**
     * Formata data a partir de um JSON
     * @private
     * @param jsonData {Object}
     */
    formata: function(jsonData)
    {
            var dia = jsonData.dia;
            var mes = jsonData.mes;
            var ano = jsonData.ano;

            if(dia < 10) dia = "0"+dia;
            if(mes < 10)  mes = "0"+mes;

            return dia+"/"+mes+"/"+ano;
    },

    /*
     * Retorna n�mero de dias de um determinado m�s.
     * @private
     * @param mes
     * @param ano
     * @return {int}
     */
    retornaDiasMes: function(mes, ano)
    {
            var numeroDiasMes;

            //31 dias
            if ( (mes==01) || (mes==03) || (mes==05) || (mes==07) || (mes==08) || (mes==10) || (mes==12))
                    numeroDiasMes = 31;
            //30 dias
            else if ( (mes==04) || (mes==06) || (mes==09) || (mes==11))
                    numeroDiasMes = 30;
            //fevereiro
            else
            {
                    //Calcula ano bissexto
                    if (((ano % 4) == 0) && ((ano % 100) == 0))
                            numeroDiasMes=29;
                    else if ((ano % 400) == 0)
                            numeroDiasMes=29;
                    else
                            numeroDiasMes=28;
            }
            return numeroDiasMes;
    },

    /**
     * Avan�a n�mero de dias em uma data
     * @private
     * @param lnDias {int} com o n�mero de dias que deseja avan�ar. Pode ser negativo.
     * @param ldDia {int}
     * @param ldMes {int}
     * @param idAno {int}
     * @return {Object} json com dia, mes e ano.
     */
    avancaDias: function(lnDias, ldDia, ldMes, ldAno)
    {
        //N�mero de dias no m�s
        var ndiasmes = this.retornaDiasMes(ldMes, ldAno);
        //temp -> dia, mes, ano
        var ltDia = ldDia;
        var ltMes = ldMes;
        var ltAno = ldAno;

        //incrementa dias
        if ((ldDia + lnDias) <= ndiasmes)
            ltDia = ldDia + lnDias
        else
        {
                //Soma do dia atual com o incremento
                var totalDias = ldDia + lnDias;
                for(var i = ldMes; i <= 12; i++ )
                {
                        //N�mero de Dias em cada m�s pelo qual for necess�rio pegar o n�mero de dias
                        var diasDoMes = this.retornaDiasMes(i, ldAno);
                        if(diasDoMes < totalDias)
                        {
                                /*
                                 * Caso o n�mero de dias do m�s seja menor do que o total de dias
                                 * ent�o decrementa-se do total os dias do m�s.
                                 */
                                totalDias -= diasDoMes;
                                /*
                                 * Se m�s igual a doze significa que a soma de dias passar� de um ano,
                                 * deve-se ent�o incrementar o valor do ano e reiniciar o valor do m�s
                                 */
                                if(i == 12)
                                {
                                        i = 0;
                                        ltAno += 1;
                                }
                        }
                        else
                        {
                                /*
                                 * Quando o n�mero de dias do m�s for maior do que o total de dias para incremento
                                 * Significa que j� temos o m�s atual
                                 * Por fim basta dizer o dia atual
                                 */
                                ltDia = totalDias;
                                //preenche o m�s atual com o novo valor.
                                ltMes = i;
                                //E sair do loop
                                i = 99;
                        }
                }
        }
        return {"dia":ltDia, "mes":ltMes, "ano":ltAno};
    }

});