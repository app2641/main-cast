

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
    controllers: [
    ],
    launch: function () {
        var panel = Ext.create('CAST.view.container.Panel', {
            width: 550,
            height: 400,
            renderTo: 'render-component'
        });
    }
});

