/**
 * CAST.controller.index.CastList
 *
 * @author app2641
 **/
Ext.define('CAST.controller.index.CastList', {

    extend: 'Ext.app.Controller',

    refs: [{
        ref: 'List', selector: 'index-CastList'
    }],


    init: function () {
        var me = this;

        me.control({
            'index-CastList': {
                itemcontextmenu: function (view, record, item, index, e) {
                    var menu = Ext.create('Ext.menu.Menu', {
                        items: [{
                            text: 'is_activeの更新',
                            handler: function () {
                                me.updateIsActive(record);
                            }
                        }]
                    });

                    e.stopEvent();
                    menu.showAt(e.getXY());
                },


                itemdblclick: function (view, record, item, index, e) {
                    window.open('/index/edit/cast_id/'+ record.raw.cast_id);
                }
            }
        });
    },



    /**
     * is_active値を更新する
     *
     * @author app2641
     **/
    updateIsActive: function (record) {
        var me = this;

        Cast.updateIsActive({
            id: record.raw.id
        }, function (response) {
            me.getList().getStore().reload();
        });
    }

});
