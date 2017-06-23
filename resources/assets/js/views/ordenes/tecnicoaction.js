/**
* Class TecnicoActionView  of Backbone Router
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.TecnicoActionView = Backbone.View.extend({

        el: '#orden-content-section',

        // Add remision
        templateAdd: _.template(($('#add-remision-tpl').html() || '')),

        events: {
            'click .click-store-remsion': 'onStoreRemision',
            'click .click-add-item': 'submitForm',
            'submit #form-remrepu': 'onStoreItem',
        },
        parameters: {
            data: {},
            action: {}
        },

        /**
        * Constructor Method
        */
        initialize : function( opts ) {
            // extends parameters
            if( opts !== undefined && _.isObject(opts.parameters) )
                this.parameters = $.extend({}, this.parameters, opts.parameters);

            if (this.parameters.action == 'add') {
                // Instancio modelo 
                this.remrepuModel = new app.RemRepuModel();
            }
            // Prepare collection
            this.remrepu = new app.RemRepuCollection();

            this.$modalCreate =  this.$('#modal-create-remision');
            this.$form =  this.$('#form-remrepu');

            this.listenTo( this.remrepuModel, 'sync', this.responseServer );
        },

        /*
        * Render View Element
        */
        render: function() {
            if (this.parameters.action == 'add') {
                this.$modalCreate.modal('show');
                this.$modalCreate.find('.content-modal').empty().html( this.templateAdd() );
            }else if(this.parameters.action == 'consult'){

            }

            this.referenceView();
		},
        /**
        * Collection remrepu View
        */
        referenceView: function(){
            this.remRepuView = new app.RemRepuView( {
                collection: this.remrepu,
                parameters: {
                    edit: true,
                    dataFilter: {
                        'orden_id': this.model.get('id'),
                    }
                }
            });
        },
        /**
        * Sumbit form
        */
        submitForm: function(e){
            this.$form.submit();
        },
        /**
        * On store in collection
        */
        onStoreItem: function(e){
            if (!e.isDefaultPrevented()) {
                e.preventDefault();
                var data = window.Misc.formToJson( e.target );
                this.remrepu.trigger( 'store', data );
            }
        },
        /**
        * Store Remision (RemRepu1)
        */
        onStoreRemision: function(e){
            e.preventDefault();

            // Prepare data
            var data = [];
                data.detalle = this.remrepu.toJSON();
                data.remrepu_orden = this.model.get('id'); 
                console.log(this.remrepu);
            this.remrepuModel.save( data, {patch: true, silent: true} );
        },
    	/**
        * fires libraries js
        */
        ready: function () {
            // to fire plugins
            if( typeof window.initComponent.initToUpper == 'function' )
                window.initComponent.initToUpper();

            if( typeof window.initComponent.initValidator == 'function' )
                window.initComponent.initValidator();
        },

        /**
        * response of the server
        */
        responseServer: function ( model, resp, opts ) { 
            // response success or error
            var text = resp.success ? '' : resp.errors;
            if( _.isObject( resp.errors ) ) {
                text = window.Misc.parseErrors(resp.errors);
            }
            if( !resp.success ) {
                alertify.error(text);
                return;
            }
            if (resp.success && this.parameters.action == 'add') {
                this.$modalCreate.modal('hide');
            }
        }
    });

})(jQuery, this, this.document);