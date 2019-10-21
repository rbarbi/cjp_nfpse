/*
 * gama3 Interface Web 0.2
 */

/**
 * @class gama3.form.DateField
 * @extends Ext.form.DateField
 * Cria um campo para seleção de data com máscara, atalhos e validação
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
     * Aplica máscara de data e uso de atalhos.
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
            //Data de amanhã
            else if (v == "+")
                    v = this.formata( this.avancaDias(1, dia, mes, ano) );
            //Data do início do mês
            else if (v == ".")
                    v = this.formata({"dia":1, "mes":mes, "ano":ano});
            //Data do início do ano;
            else if (v == "*")
                    v = this.formata({"dia":1, "mes":1, "ano":ano})
            else
                    v = v.replace(/\D/g,"");
        }
        else
        {
                //Remove tudo o que não é dígito
                v=v.replace(/\D/g,"");
                //Remove dígitos a partir do 8°
                v=v.replace(/^(\d{8})(\d{1,})/,"$1");
                //Coloca barra entre o segundo e o terceiro dígitos
                v=v.replace(/^(\d{2})(\d)/,"$1/$2");
                //Coloca barra entre o quarto e o quinto dígitos
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
     * Retorna número de dias de um determinado mês.
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
     * Avança número de dias em uma data
     * @private
     * @param lnDias {int} com o número de dias que deseja avançar. Pode ser negativo.
     * @param ldDia {int}
     * @param ldMes {int}
     * @param idAno {int}
     * @return {Object} json com dia, mes e ano.
     */
    avancaDias: function(lnDias, ldDia, ldMes, ldAno)
    {
        //Número de dias no mês
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
                        //Número de Dias em cada mês pelo qual for necessário pegar o número de dias
                        var diasDoMes = this.retornaDiasMes(i, ldAno);
                        if(diasDoMes < totalDias)
                        {
                                /*
                                 * Caso o número de dias do mês seja menor do que o total de dias
                                 * então decrementa-se do total os dias do mês.
                                 */
                                totalDias -= diasDoMes;
                                /*
                                 * Se mês igual a doze significa que a soma de dias passará de um ano,
                                 * deve-se então incrementar o valor do ano e reiniciar o valor do mês
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
                                 * Quando o número de dias do mês for maior do que o total de dias para incremento
                                 * Significa que já temos o mês atual
                                 * Por fim basta dizer o dia atual
                                 */
                                ltDia = totalDias;
                                //preenche o mês atual com o novo valor.
                                ltMes = i;
                                //E sair do loop
                                i = 99;
                        }
                }
        }
        return {"dia":ltDia, "mes":ltMes, "ano":ltAno};
    }

});