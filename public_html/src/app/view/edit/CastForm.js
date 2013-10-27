/**
 * CAST.view.edit.CastForm
 *
 * @author app2641
 **/
Ext.define('CAST.view.edit.CastForm', {

    extend: 'Ext.form.Panel',

    alias: 'widget.edit-CastForm',


    paramsAsHash: true,
    bodyStyle: 'padding: 30px;',
    defaults: {
        xtype: 'textfield',
        width: 500,
        allowBlank: false
    },


    initComponent: function () {
        var me = this;

        me.items = [];
        me.buildHiddenFields();
        me.buildDmmName();
        me.buildName();
        me.buildFurigana();
        me.buildDMMButton();

        Ext.apply(me, {
            buttons: [{
                text: '新規作成',
                action: 'new',
                disabled: true
            }, {
                text: 'データ更新',
                action: 'update',
                disabled: true
            }, {
                text: 'データ削除',
                action: 'delete',
                disabled: true
            }]
        });

        me.callParent(arguments);
        
    },



    /**
     * hiddenフィールド
     *
     * @author app2641
     **/
    buildHiddenFields: function () {
        var me = this;

        me.items.push({
            xtype: 'hidden',
            name: 'id',
            allowBlank: true
        }, {
            xtype: 'hidden',
            name: 'cast_id',
            allowBlanks: true
        });
    },



    /**
     * DMM名称フィールド
     *
     * @author app2641
     **/
    buildDmmName: function () {
        var me = this;

        me.items.push({
            name: 'dmm_name',
            readOnly: true,
            fieldLabel: 'DmmName'
        });
    },




    /**
     * 名前フィールド
     *
     * @author app2641
     **/
    buildName: function () {
        var me = this;

        me.items.push({
            name: 'name',
            fieldLabel: 'Name',
            selectOnFocus: true
        });
    },



    /**
     * ふりがなフィールド 
     *
     * @author app2641
     **/
    buildFurigana: function () {
        var me = this;

        me.items.push({
            name: 'furigana',
            fieldLabel: 'furigana',
            selectOnFocus: true
        });
    },



    /**
     * DMMプロフィールページを開くボタンを構築する
     *
     * @author app2641
     **/
    buildDMMButton: function () {
        var me = this;

        me.items.push({
            xtype: 'button',
            text: 'DMM',
            style: 'margin-top: 20px;',
            width: 120,
            handler: function () {
                window.open('http://actress.dmm.co.jp/-/detail/=/actress_id='+me.cast_id+'/');
            }
        });
    }

});
