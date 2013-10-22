

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

        if (Ext.get('render-list-container')) {
            Ext.create('CAST.view.index.CastList', {
                renderTo: 'render-list-container'
            });

        } else if (Ext.get('render-edit-form-container')) {
            var el = Ext.get('render-edit-form-container');

            Ext.create('CAST.view.edit.Panel', {
                renderTo: 'render-edit-form-container',
                cast_id: el.getAttribute('cast_id')
            });
        }
    }
});


