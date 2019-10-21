/**
 * gama3.panel.PanelIcons
 * Cria um painel com uma lista de imagens clicáveis
 */

gama3.panel.PanelIcons = Ext.extend(Ext.Panel, {

    /**
     * @cfg icons
     * Array de icones para a listagem.
     * Deverão ser um JSON com:
     * {
     *      src: "img/xxx.png";
     *      handler: funcao;
     * }
     */

    /**
     * @cfg iconWidth
     * Largura dos ícones
     */

    /**
     * @cfg iconHeight
     * Altura dos ícones
     */

    initComponent: function()
    {
        gama3.hasRequiredParms(this, ["icons","iconHeight","iconWidth"], "gama3.panel.PanelIcons");
        gama3.panel.PanelIcons.superclass.initComponent.apply(this, arguments);
    },

    onRender: function()
    {
        gama3.panel.PanelIcons.superclass.onRender.apply(this, arguments);

        //Pega El
        var el = this.getEl().child(".x-panel-body");
        var img = null;
        var imgEl = null;
        for(var i = 0; i < this.icons.length; i++){
            img = this.icons[i];
            this.initAttImg(img);
            imgEl = el.createChild({
                tag: "a",
                href: img.href || "#",
                target: img.target,
                children: [
                    {
                        tag:'img',
                        title: img.title || "Ícone",
                        src: img.src || "",
                        style: "width: " + this.iconWidth + ";height: " + this.iconHeight+";"+img.style
                    }
                ]
            });
            el.appendChild(imgEl);
            if(img.handler)
                imgEl.on("click", img.handler);
       }
    },

    initAttImg: function(obj)
    {
        if(!obj.style)
            obj.style = "";
        if(obj.href)
        {
            if(obj.href.match("http://"))
                obj.target = "_blank"
            else
                obj.target = "";
        }

    }
})

/*
 *
 */