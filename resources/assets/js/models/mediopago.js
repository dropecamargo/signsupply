/**
* Class MedioPagoModel extend of Backbone Model
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.MedioPagoModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('mediopagos.index') );
        },
        idAttribute: 'id',
        defaults: {
        	'mediopago_nombre': '',
            'mediopago_activo': 1,
            'mediopago_ch': 0,
        	'mediopago_ef': 1,
        }
    });

})(this, this.document);
