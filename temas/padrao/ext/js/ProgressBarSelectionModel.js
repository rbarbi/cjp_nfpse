/*
 * Ext JS Library 2.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

/**
 * @class Ext.grid.ProgressBarSelectionModel
 * @extends Ext.grid.RowSelectionModel
 * A custom selection model that renders a column of progress bar showing status
 * @constructor
 * @param {Object} config The configuration options
 * @autor Ximosoft
 */
Ext.grid.ProgressBarSelectionModel = Ext.extend(Ext.grid.RowSelectionModel, {
    /**
     * @cfg {String} header Any valid text or HTML fragment to display in the header cell for the row
     * number column (defaults to '').
     */
    header: "",
    sortable: true,
    fixed:true,
    dataIndex: '',
    id: 'progress-grid',
	text: '%',
	baseCls: 'x-progress',
	colored: true,
	initEvents : function(){
        Ext.grid.ProgressBarSelectionModel.superclass.initEvents.call(this);
	},
  
    // private
    renderer : function(v, p, record, w){
		var text_post = '%';
		
		if(this.text){
            text_post = this.text;
        }
		var text_front;
		var text_back;
		
		text_front = (v <55)?'':v+text_post;
		text_back = (v >=55)?'':v+text_post;		
		
		var style ='';
		this.colored = true;
		if (this.colored == true)
		{
			if (v <= 100 && v >66) style='-green';
			if (v < 67  && v >33) style='-orange';
			if (v < 34 ) style='-red';
		}
		
		return String.format('<div class="x-progress-wrap"><div class="x-progress-inner"><div class="x-progress-bar{0}" style="width:{1}%;"><div class="x-progress-text" style="width:100%;">{2}</div></div><div class="x-progress-text x-progress-text-back" style="width:100%;">{3}</div></div></div>',style,v,text_front,text_back);		

    }
});
Ext.reg('progress-grid', Ext.grid.ProgressBarSelectionModel);