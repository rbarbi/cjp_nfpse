//NAV
gama3.dashboard.Nav = Ext.extend(Ext.Panel, {
   initComponent: function()
   {
       //Painel de Navegação
        this.panelMenu = new Ext.Panel({
            layout: "accordion",
            defaults: {frame: true},
            items: this.itemsMenu,
            region: "center",
            autoScroll: false,
            split: true,
            margins: "0 0 0 0"
        });

        Ext.apply(this, {
           region: "west",
           layout: "border",
           frame: true,		   
           title: G3.text.navigation,
           split: true,
           width: 205,
           items: [this.panelMenu ].concat(this.itemsNav),
           margins: "0 0 0 5",
           cmargins: "0 0 0 0"
        })

       gama3.dashboard.Nav.superclass.initComponent.apply(this, arguments);
   },

   /*
    * Retorna Painel de menu
    * @return Ext.Panel
    */
   getMenu: function()
   {
        return this.panelMenu;
   },

   /*
    * Adiciona painel ao Menu
    */
   addMenu: function(menu)
   {
       this.panelMenu.add(menu);
       this.doLayout();
   },

   /*
     * Seleciona um ítem de menu que possua o mesmo id
     * @param treeNodeRoot {TreeNode}
     * @param nodeId {String}
     */
    selectItem: function(treeNodeRoot, nodeId)
    {
        //Separa as palavras do hash name
        var nodeIdArr = nodeId.split(".");
        //para todo não filho do não principal, executa função
        treeNodeRoot.cascade(function(obj){
            //Aqui salvarão nome completo do hash
            var idTemp = "";
            //Para todas as palavras de nodeHashName verifica se algum ítem existe na árvore
            //E se não for folha, o expande
            for(var x in nodeIdArr)
            {
                if(typeof(nodeIdArr[x]) == "function")
                    continue;

                if(x > 0)
                    idTemp += ".";
                idTemp += nodeIdArr[x];

                if(obj.id == idTemp)
                    if(!obj.isExpanded() && !obj.isLeaf())
                        obj.expand();
            }

            //Se id do objeto igual ao nodeId
            if(obj.id == nodeId)
            {
                if(!obj.isSelected())
                    obj.select();

                return false;
            }
            return true;
        });
    }

})

