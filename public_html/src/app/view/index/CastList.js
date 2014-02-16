/**
 * CAST.view.index.CastList
 *
 * @author app2641
 **/
Ext.define('CAST.view.index.CastList', {

    extend: 'Ext.grid.Panel',

    alias: 'widget.index-CastList',

    requires: [
        'Ext.ux.form.SearchField'
    ],


    initComponent: function () {
        var me = this;
        me.buildStore();
        me.buildColumns();

        me.buildToolbar();

        Ext.apply(me, {
            multiSelect: true,
            height: 600
        });

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
            pageSize: 300,
            fields: ['id', 'cast_id', 'dmm_name', 'name', 'furigana', 'search_index', 'is_active'],
            proxy: {
                type: 'direct',
                directFn: Cast.getList,
                reader: {
                    root: 'results',
                    totalProperty: 'count'
                }
            }
        });
    },



    /**
     * カラムの構築
     *
     * @author app2641
     **/
    buildColumns: function () {
        var me = this;

        me.columns = [{
            text: 'id',
            dataIndex: 'id',
            flex: 0.5
        }, {
            text: 'cast_id',
            dataIndex: 'cast_id',
            flex: 0.5
        }, {
            text: 'dmm_name',
            dataIndex: 'dmm_name',
            flex: 1
        }, {
            text: 'name',
            dataIndex: 'name',
            flex: 1
        }, {
            text: 'furigana',
            dataIndex: 'furigana',
            flex: 1
        }, {
            text: 'search_index',
            dataIndex: 'search_index',
            flex: 0.5
        }, {
            text: 'is_active',
            dataIndex: 'is_active',
            flex: 0.5
        }];
    },



    /**
     * ツールバーの構築
     *
     * @author app2641
     **/
    buildToolbar: function () {
        var me = this;

        me.tbar = [{
            xtype: 'searchfield',
            store: me.store,
            width: 400
        }, '-', {
            text: 'テンプレート',
            action: 'template'
        }];


        me.bbar = Ext.create('Ext.PagingToolbar', {
            store: me.store
        });
    }

});
