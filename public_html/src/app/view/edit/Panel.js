/**
 * CAST.view.edit.Panel
 *
 * @author app2641
 **/
Ext.define('CAST.view.edit.Panel', {

    extend: 'Ext.panel.Panel',

    alias: 'widget.edit-Panel',

    requires: [
        'CAST.view.edit.CastList'
    ],


    initComponent: function () {
        var me = this;

        Ext.apply(me, {
            items: [{
                xtype: 'edit-CastList',
                cast_id: me.cast_id
            }]
        });

        me.callParent(arguments);
    }

});
