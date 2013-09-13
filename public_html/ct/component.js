

Ext.Loader.setConfig({
    enabled: true,
    paths: {
        'Ext': '/ext/src',
        'Ext.ux': '/src/ux'
    }
});

Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.util.*',
    'Ext.state.*',
    'Ext.ux.form.SearchField'
]);

Ext.onReady(function () {
    var store = Ext.create('store.json', {
        storeId: 'ctstore',
        fields: [
            {name: 'category', type: 'string'},
            {name: 'screen_name', type: 'string'},
            {name: 'cls_name', type: 'string'},
            {name: 'explanation', type: 'string'},
            {name: 'url', type: 'string'}
        ],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: '/test/list',
            reader: {
                root: 'items'
            }
        }
    });


    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        columns: [
            {header: 'カテゴリ', dataIndex: 'category', width: 90},
            {header: '画面名', dataIndex: 'screen_name', width: 140},
            {header: 'クラス名', dataIndex: 'cls_name', width: 210, renderer: function (value, metaData, record) {
                var tpl = new Ext.XTemplate(
                    '<a href="/test/cmp/c/{category}/cls/{cls_name}" target="_blank">{cls_name}</a>'
                );
                return tpl.applyTemplate(record.data);
            }},
            {header: '説明', dataIndex: 'explanation', flex: 1}
        ],
        renderTo: 'ct-container',
        autoScroll: true,
        height: 390,
        tbar: [{
            xtype: 'searchfield',
            store: store,
            width: 300,
            listeners: {
                afterrender: {
                    fn: function (field) {
                        field.focus(true, 300);
                    }
                }
            }
        }, '-', {
            xtype: 'button',
            text: '結合テスト',
            handler: function () {
                window.open('/?debug=ct');
            }
        }]
    });
});
