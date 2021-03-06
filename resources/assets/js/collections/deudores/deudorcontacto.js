/**
* Class ContactoDeudorList of Backbone Collection
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.ContactoDeudorList = Backbone.Collection.extend({
        url: function() {
            return window.Misc.urlFull( Route.route('deudores.contactos.index') );
        },
        model: app.ContactoDeudorModel,

        /**
        * Constructor Method
        */
        initialize : function() {

        },
   });

})(this, this.document);
