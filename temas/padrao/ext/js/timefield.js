/**
 * @class Ext.ux.form.TimeField
 * @extends Ext.ux.form.FieldPanel
 * This class creates a time field using spinners.
 * @license: BSD
 * @author: Robert B. Williams (extjs id: vtswingkid)
 * @constructor
 * Creates a new FieldPanel
 * @param {Object} config Configuration options
 */
Ext.namespace("Ext.ux.form");
Ext.ux.form.TimeField = Ext.extend(Ext.ux.form.FieldPanel, {
	border: false,
	baseCls: null,
	layout: 'table',
	token: ':',
	value: '00:00:00',
	layoutConfig: {
		columns: 5
	},
	width: 134,
	defaults:{
		maskRe: /[0-9]/,
		maxLength: 2,
		listeners: {
			'focus': function(f){
				f.selectText();
			}
		}
	},
	setRawValue: function(v){
		if(!v.length)v='00:00:00';
		Ext.ux.form.TimeField.superclass.setRawValue.call(this, v);
	},
	// private
	initComponent: function()
	{
		this.items = [{
			xtype: 'uxspinner',
			width: 40,
			name: this.name + 'H',
			strategy: new Ext.ux.form.Spinner.TimeStrategy({
				format: 'H',
				incrementConstant: Date.HOUR,
				alternateIncrementValue: 3,
				alternateIncrementConstant: Date.HOUR
			})
		}, {
			html: ':',
			baseCls: null,
			bodyStyle: 'font-weight: bold;',
			border: false
		}, {
			xtype: 'uxspinner',
			width: 40,
			name: this.name + 'M',
			strategy: new Ext.ux.form.Spinner.TimeStrategy({
				format: 'i',
				incrementConstant: Date.MINUTE,
				alternateIncrementValue: 5,
				alternateIncrementConstant: Date.MINUTE
			})
		}, {
			html: ':',
			baseCls: null,
			bodyStyle: 'font-weight: bold;',
			border: false
		}, {
			xtype: 'uxspinner',
			width: 40,
			name: this.name + 'S',
			strategy: new Ext.ux.form.Spinner.TimeStrategy({
				format: 's',
				incrementConstant: Date.SECOND,
				alternateIncrementValue: 5,
				alternateIncrementConstant: Date.SECOND
			})
		}]
		Ext.ux.form.TimeField.superclass.initComponent.call(this);
	}
});
Ext.reg('uxtimefield', Ext.ux.form.TimeField);
