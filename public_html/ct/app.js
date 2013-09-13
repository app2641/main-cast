

Ext.Loader.setConfig({
    enabled: true,
    paths: {
        'Ext': '/ext/src',
        'Ext.ux': '/src/ux',
        'CAST': '/src/app'
    }
});


Ext.direct.Manager.addProvider(CAST.REMOTING_API);
Ext.application({
    controllers: CAST.Controllers,
    launch: function () {
    }
});


