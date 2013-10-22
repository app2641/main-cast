/**
 * CAST.view.edit.CastList
 *
 * @author app2641
 **/
Ext.define('CAST.view.edit.CastList', {

    extend: 'Ext.grid.Panel',

    alias: 'widget.edit-CastList',


    initComponent: function () {
        var me = this;

        me.buildStore();
        me.buildColumns();

        me.callParent(arguments);
    },



    /**
     * ストアの構築
     *
     * @author app2641
     **/
    buildStore: function () {
        var me = this;

        me.store = Ext.create('Ext.data.Store', {
            autoLoad: true,
            fields: ['id', 'dmm_name', 'name', 'is_active'],
            proxy: {
                type: 'direct',
                directFn: Cast.getDuplicateCastList,
                extraParams: {
                    cast_id: me.cast_id
                }
            }
        });
    },



    /**
     * カラムの構築
     *
     * @author suguru
     **/
    buildColumns: function () {
        var me = this;

        me.columns = [{
            text: 'id',
            dataIndex: 'id',
            flex: 1
        }, {
            text: 'dmm_name',
            dataIndex: 'dmm_name',
            flex: 1
        }, {
            text: 'name',
            dataIndex: 'name',
            flex: 1
        }, {
            text: 'is_active',
            dataIndex: 'is_active',
            flex: 1
        }];
    }

});
