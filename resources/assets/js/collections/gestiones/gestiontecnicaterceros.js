/**
* Class GestionTecnicaCollection of Backbone Collection
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.GestionTecnicaCollection = Backbone.Collection.extend({

        url: function() {
            return window.Misc.urlFull( Route.route('gestionestecnico.index') );
        },
        model: app.GestionTecnicoModel,

        /**
        * Constructor Method
        */
        initialize : function(){
        }
   });

})(this, this.document);