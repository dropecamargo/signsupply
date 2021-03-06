/**
* Class CreateCajaMenorView  of Backbone Router
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.CreateCajaMenorView = Backbone.View.extend({

        el: '#cajamenor-create',
        template: _.template( ($('#add-cajamenor-tpl').html() || '') ),
        templateDetailt: _.template( ($('#add-cajamenor-detail-tpl').html() || '') ),
        events: {
            'submit #form-cajamenor': 'onStore',
        },

        /**
        * Constructor Method
        */
        initialize : function(opts) {
            // Initialize
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            // Spinner
            this.$spinner = this.$('#spinner-box');

            // Events
            this.listenTo( this.model, 'change', this.render );
            this.listenTo( this.model, 'sync', this.responseServer );
            this.listenTo( this.model, 'request', this.loadSpinner );
        },

        /*
        * Render View Element
        */
        render: function() {
            var attributes = this.model.toJSON();
            attributes.edit = false;
            this.$el.html( this.template(attributes) );

            this.$wraperDetail = this.$('#render-form-detail');
            this.$wraperDetail.html( this.templateDetailt({edit: attributes.edit}) );

            // Reference Fields
            this.$form = this.$('#form-cajamenor');
            this.$('#cajamenor2_cuenta').prop('disabled', true);

            // Reference views
            this.ready();
        },

        /**
        * Event Create Caja Menor
        */
        onStore: function (e) {
            if (!e.isDefaultPrevented()) {
                e.preventDefault();

                var data = window.Misc.formToJson( e.target );
                this.model.save( data, {patch: true, silent: true} );
            }
        },

        /**
        * fires libraries js
        */
        ready: function () {
            // to fire plugins
            if( typeof window.initComponent.initToUpper == 'function' )
                window.initComponent.initToUpper();

            if( typeof window.initComponent.initSelect2 == 'function' )
                window.initComponent.initSelect2();

            if( typeof window.initComponent.initValidator == 'function' )
                window.initComponent.initValidator();
        },

        /**
        * Load spinner on the request
        */
        loadSpinner: function (model, xhr, opts) {
            window.Misc.setSpinner( this.$spinner );
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) {
            window.Misc.removeSpinner( this.$spinner );

            if(!_.isUndefined(resp.success)) {
                // response success or error
                var text = resp.success ? '' : resp.errors;
                if( _.isObject( resp.errors ) ) {
                    text = window.Misc.parseErrors(resp.errors);
                }

                if( !resp.success ) {
                    alertify.error(text);
                    return;
                }
                Backbone.history.navigate(Route.route('cajasmenores.edit', { cajasmenores: resp.id}), { trigger:true });
            }
        }
    });

})(jQuery, this, this.document);
