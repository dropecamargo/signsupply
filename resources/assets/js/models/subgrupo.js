/**
* Class SubGrupoModel extend of Backbone Model
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function (window, document, undefined) {

    app.SubGrupoModel = Backbone.Model.extend({

        urlRoot: function () {
            return window.Misc.urlFull( Route.route('subgrupos.index') );
        },
        idAttribute: 'id',
        defaults: {
            'subgrupo_codigo': '',
            'subgrupo_nombre': '',
            'subgrupo_activo': 1,
            'subgrupo_retefuente': ''
        }
    });

})(this, this.document);
