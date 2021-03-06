/**
* Class RemisionCollection of Backbone Collection
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.RemisionCollection = Backbone.Collection.extend({

        url: function() {
            return window.Misc.urlFull( Route.route('ordenes.remrepuestos.index') );
        },
        model: app.RemRepuModel,

        /**
        * Constructor Method
        */
        initialize : function(){
        },

        comparator: function( model ){
            return model.get('remrepu1_numero');
        },

   });
})(this, this.document);

