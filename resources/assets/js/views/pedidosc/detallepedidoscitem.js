/**
* Class DetallePedidoscItemView  of Backbone Router
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.DetallePedidoscItemView = Backbone.View.extend({

        tagName: 'tr',
        template: _.template( ($('#add-pedidoc-item-tpl').html() || '') ),
        parameters: {
            edit: false
        },

        /**
        * Constructor Method
        */
        initialize: function(opts){
	        // Extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({},this.parameters, opts.parameters);
            
            // Events Listener
            this.listenTo( this.model, 'change', this.render );
        },

        /*
        * Render View Element
        */
        render: function(){
            var attributes = this.model.toJSON();
            attributes.edit = this.parameters.edit;
            if ( attributes.pedidoc2_margen_porcentaje < 100 && !attributes.edit && attributes.pedidoc1_autorizacion_co == null) 
                this.$el.addClass('bg-menor360');
            
            this.$el.html( this.template(attributes) );
            return this;
        }
    });

})(jQuery, this, this.document);