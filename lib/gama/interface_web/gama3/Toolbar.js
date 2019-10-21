Ext.ns("gama3.Toolbar");
gama3.Toolbar.IconItem = function(t){
    var s = document.createElement("img");
    s.src = t.src;
    gama3.Toolbar.IconItem.superclass.constructor.call(this, s);
};
Ext.extend(gama3.Toolbar.IconItem, Ext.Toolbar.Item, {
    enable:Ext.emptyFn,
    disable:Ext.emptyFn,
    focus:Ext.emptyFn
});