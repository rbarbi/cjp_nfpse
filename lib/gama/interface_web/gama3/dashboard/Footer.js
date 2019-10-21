//FOOTER
gama3.dashboard.Footer = Ext.extend(Ext.Panel, {
   initComponent: function()
   {
       Ext.apply(this, {
            region: "south",
            frame: true,
            margins: "0 5 5 5",
            split: true
       });
       gama3.dashboard.Footer.superclass.initComponent.apply(this, arguments);
   }
});
