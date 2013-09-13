/**
 * CAST.Events
 *
 * @author app2641
 **/
Ext.define('CAST.Events', {

    extend: 'Ext.util.Observable',

    singleton: true,


    /**
     * Infoウィンドウを表示
     *
     * @author app2641
     **/
    showInfoWindow: function (msg) {
        Ext.Msg.show({
            title: 'Success!',
            msg: msg,
            icon: Ext.Msg.INFO,
            buttons: Ext.Msg.OK
        });
    },



    /**
     * Cautionウィンドウを表示する
     *
     * @author app2641
     **/
    showCautionWindow: function (msg) {
        Ext.Msg.show({
            title: 'Caution!',
            msg: msg,
            icon: Ext.Msg.ERROR,
            buttons: Ext.Msg.OK
        });
    }

});
