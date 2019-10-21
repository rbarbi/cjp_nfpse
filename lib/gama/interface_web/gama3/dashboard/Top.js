//TOP
gama3.dashboard.Top = Ext.extend(Ext.Panel, {
   initComponent: function()
   {
        Ext.apply(this, {
            region:"north",
            frame: true,
            margins: "5 5 0 5",
            split: true
        });

        gama3.dashboard.Top.superclass.initComponent.apply(this, arguments);
   }
});


