

Ext.ns('CAST');

Ext.Loader.setConfig({
    enabled: true,
    paths: {
        'Ext': '/ext/src',
        'Ext.ux': '/src/ux',
        'CAST': '/src/auth'
    }
});


// Ext.direct.Providerの設定
Ext.direct.Manager.addProvider(CAST.REMOTING_API);

Ext.application({
    controllers: CAST.Controllers,
    launch: function () {
        // Elがあるかどうか
        if (Ext.get('auth-login-container')) {
        }
    }
});

