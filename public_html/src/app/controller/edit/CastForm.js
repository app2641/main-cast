/**
 * CAST.controller.edit.CastForm
 *
 * @author app2641
 **/
Ext.define('CAST.controller.edit.CastForm', {

    extend: 'Ext.app.Controller',

    refs: [{
        ref: 'Form', selector: 'edit-CastForm'
    }, {
        ref: 'List', selector: 'edit-CastList'
    }],



    init: function () {
        var me = this;
        
        me.control({
            'edit-CastList': {
                select: function (model, record, index) {
                    var form = me.getForm();
                    form.getForm().load({
                        params: {id: record.raw.id},
                        success: function () {
                            // ボタンの有効化
                            form.down('button[action="new"]').enable();
                            form.down('button[action="update"]').enable();
                            form.down('button[action="delete"]').enable();
                        }
                    });
                }
            },


            'edit-CastForm button[action="new"]': {
                click: me.createSubmit
            },



            'edit-CastForm button[action="update"]': {
                click: me.updateSubmit
            },



            'edit-CastForm button[action="delete"]': {
                click: me.deleteSubmit
            }
        });
    },



    /**
     * 新規作成サブミット
     *
     * @author app2641
     **/
    createSubmit: function (btn) {
        var me = this,
            form = me.getForm();

        if (form.getForm().isValid()) {
            btn.disable();
            var values = form.getValues();

            Cast.createCastData({
                values: values
            }, function (response) {
                btn.enable();
                me.callback(response);
            });
        }
    },



    /**
     * データ更新サブミット
     *
     * @author app2641
     **/
    updateSubmit: function (btn) {
        var me = this,
            form = me.getForm();

        if (form.getForm().isValid()) {
            btn.disable();
            var values = form.getValues();

            Cast.updateCastData({
                values: values
            }, function (response) {
                btn.enable();
                me.callback(response);
            });
        }
    },



    /**
     * データ削除サブミット
     *
     * @author app2641
     **/
    deleteSubmit: function (btn) {
        var me = this,
            form = me.getForm();

        Ext.Msg.confirm('Confirm', 'データを削除しますか？', function (b) {
            if (b == 'yes') {
                btn.disable();

                Cast.deleteCastData({
                    id: form.getValues().id
                }, function (response) {
                    btn.enable();
                    me.callback(response);
                });
            }
        });
    },



    /**
     * フォームサブミット後のコールバック
     *
     * @author app2641
     **/
    callback: function (response) {
        var me = this;

        if (response.success) {
            me.getList().getStore().reload();
        
        } else {
            Ext.Msg.show({
                title: 'Caution!',
                msg: response.msg,
                icon: Ext.Msg.ERROR,
                buttons: Ext.Msg.OK
            });
        }
    }

});
