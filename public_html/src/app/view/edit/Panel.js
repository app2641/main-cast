/**
 * CAST.view.edit.Panel
 *
 * @author app2641
 **/
Ext.define('CAST.view.edit.Panel', {

    extend: 'Ext.panel.Panel',

    alias: 'widget.edit-Panel',

    requires: [
        'CAST.view.edit.CastList',
        'CAST.view.edit.CastForm'
    ],


    layout: 'border',
    border: false,
    height: 600,


    initComponent: function () {
        var me = this;

        Ext.apply(me, {
            items: [{
                xtype: 'edit-CastList',
                region: 'west',
                split: true,
                cast_id: me.cast_id,
                width: 500
            }, {
                xtype: 'edit-CastForm',
                region: 'center',
                cast_id: me.cast_id,
                split: true,
                api: {
                    load: Cast.loadCastData
                }
            }]
        });

        me.callParent(arguments);
    }

});
