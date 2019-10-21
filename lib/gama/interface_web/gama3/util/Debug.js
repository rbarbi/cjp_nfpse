gama3.Debug = function()
{
    return {

        /*
         * Exibe Atributos de primeiro nível de um Json
         * @param json
         */
        json: function(json)
        {
            var s = '<blockquote>';
            for(x in json)
            {
                if(typeof json[x] != "function")
                    s += "<br />"+x+" = "+json[x];
            }
            s += '</blockquote>';
            var w = new Ext.Window({title: "Debug", html: s, width: 400,height: 400, autoScroll:true});
            w.show();
        },

        /*
         * Exibe Debug de uma Excessão
         * @param e gama3.util.Exception || Error
         */
        exception: function(e)
        {
            if(typeof e != "gama3.util.Exception")
                e = new gama3.util.Exception(e);

            e.toString();
        }
    }
}();


