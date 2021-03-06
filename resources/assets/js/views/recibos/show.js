/**
* Class ShowRecibosView
* @author KOI || @dropecamargo
* @link http://koi-ti.com
*/

//Global App Backbone
app || (app = {});

(function ($, window, document, undefined) {

    app.ShowRecibosView = Backbone.View.extend({

        el: '#recibo-show',
        /**
        * Constructor Method
        */
        initialize : function() {
            // Model exist
            if( this.model.id != undefined ) {
            	this.detalleReciboList = new app.DetalleReciboList();
                this.detalleReciboMedioPagoList = new app.DetalleRecibo3List();
                
                // Reference views
                this.referenceViews();
            }
        },

        /**
        * reference to views
        */
        referenceViews: function () {
    		// Detalle asignaciones list
            this.detalleRecibosView = new app.DetalleRecibosView({
                collection: this.detalleReciboList,
                parameters: {
                    wrapper: this.el,
                    edit: false,
                    dataFilter: {
                    	recibo2: this.model.get('id')
                    }
                }
            });
            //DetalleRecibo3List
            this.detalleRecibos3View = new app.DetalleMedioPagoReciboView( {
                collection: this.detalleReciboMedioPagoList,
                parameters: {
                    edit: false,
                    dataFilter: {
                        'recibo3': this.model.get('id')
                    }
               }
            });
        }
    });
})(jQuery, this, this.document);
