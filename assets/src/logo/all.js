//noinspection ThisExpressionReferencesGlobalObjectJS,JSUnresolvedVariable
( function( window, $, Backbone, plugin ) {
	'use strict';

	var models = {};

	models.Logo = Backbone.Model.extend( {

		urlRoot : ajaxurl,

		defaults : {
			container : {},
			templates : {},
			l10n      : {},
			att_id    : 0,
			width     : 0,
			height    : 0,
			thumb     : '',
			name      : '',
			link      : ''
		},

		initialize : function() {
			// this.queue = $.Deferred();
			// this.queue.resolve();
		},

		fetch : function() {

			var self = this;

			$.when(
				$.post( this.urlRoot, {
					action      : plugin.action,
					_ajax_nonce : plugin._ajax_nonce,
					att_id      : self.get('att_id'),
					task        : 'fetch'
				} )
			)
				.then(
					// success
					function( response ) {
						//console.log( 'RESPONSE', self.attributes );
					},
					// error
					function( reason ) {
						//console.log( 'Model:fetch Error',reason );
					}
				);

			/*wp.ajax.post( plugin.action, {
				_ajax_nonce : plugin._ajax_nonce,
				att_id      : self.get('att_id'),
				task        : 'fetch'
			} )
				.done( function( response ) {
					 self.set( {
						 att_id : self.get('att_id'),
						 width  : response.data.width,
						 height : response.data.height,
						 thumb  : response.data.thumb,
						 name   : response.data.name,
						 link   : response.data.link
					 } );
				} );*/
		},

		save : function( att_id ) {},

		// Triggered by: views.Delete
		destroy : function( att_id ) {
			var self = this;
			$.when(
				$.post( this.urlRoot, {
					action      : plugin.action,
					_ajax_nonce : plugin._ajax_nonce,
					att_id      : self.get('att_id'),
					task        : 'destroy'
				} )
			)
				.then(
					// success
					function( response ) {
						//console.log( 'Model:destroy Success', response );
					},
					// error
					function( reason ) {
						//console.log( 'Model:destroy Error', reason );
					}
				);
			return {};
		}
	} );

	window.wcm = window.wcm || {};
	window.wcm.logo = window.wcm.logo || {};
	window.wcm.logo.models = models;

} )( this, jQuery, Backbone, logoUploader );( function( $, plugin, _, Backbone, uploader ) {
	"use strict";

	var views = {};

	//noinspection JSUnresolvedVariable
	views.Preview = Backbone.View.extend( {

		// @TODO Consider using WP JS API: ~/wp-includes/js/wp-util.js
		// Needs wp_enqueue_script( 'wp-util' ); - maybe 'shortcode' as well?
		//template : wp.template( plugin.templates.logo ),
		template : _.template( $( plugin.templates.logo ).html(), null, { variable : 'data' } ),

		initialize : function() {
			var att_id = this.model.get('att_id');
			this.render();
			this.listenTo( this.model, 'change', function( data ) {
				$( '#' + plugin.container.main ).find('.wrap').removeClass('hidden');
				$( '#' + plugin.container.uploader ).addClass('hidden');
			} );
		},

		hide : function() {
			var $el = $( this.$el ).find('.wrap');
			$el.hasClass('hidden') && $el.removeClass('hidden');
		},

		show : function() {
			var $el = $( this.$el ).find('.wrap');
			! $el.hasClass('hidden') && $el.addClass('hidden');
		},

		render : function() {
			// this is the only way that <%= namespace.attribute %> works in .tmpl
			this.$el.html( this.template( this.model.attributes ) );
			return this;
		}
	} );

	//noinspection JSUnresolvedVariable
	views.Thumb = Backbone.View.extend( {

		template : _.template( $( plugin.templates.thumb ).html(), null, { variable : 'data' } ),

		events : {},

		initialize : function() {
			this.listenTo( this.model, 'change:thumb', this.render );
			this.render();
		},

		render : function() {
			this.$el.html( this.template( this.model.attributes ) );
			return this;
		}
	} );

	//noinspection JSUnresolvedVariable
	views.Delete = Backbone.View.extend( {

		template : _.template( $( plugin.templates.delete ).html(), null, { variable : 'data' } ),

		events : {
			'click .icon'      : 'deleteAttachment',
			'mouseover .icon'  : 'onHover',
			'mouseleave .icon' : 'onOut'
		},

		initialize : function() {
			this.render();
		},

		render : function() {
			this.$el.html( this.template( this.model.attributes ) );
			$( this.$el ).find('.icon').css( 'background-color', this.model.get( 'color1' ) );
			return this;
		},

		deleteAttachment : function( e ) {
			// $( this.$el ).unbind();
			$( '#' + plugin.container.main ).find('.wrap').addClass('hidden');
			$( '#' + plugin.container.uploader ).removeClass('hidden');
			$('#media-items').empty();

			var destroyed = this.model.destroy( this.model.get('att_id') );
			return false;
		},

		onHover : function( e ) {
			$( this.$el ).find('.icon').css( 'color', this.model.get('color2') );
		},

		onOut : function( e ) {
			$( this.$el ).find('.icon').css( 'color', '#fff' );
		}
	} );

	//noinspection JSUnresolvedVariable
	views.Caption = Backbone.View.extend( {

	    template : _.template( $( plugin.templates.caption ).html(), null, { variable : 'data' } ),

		initialize : function() {
			this.render();
			this.listenTo( this.model, 'change', this.render );
		},

		render : function() {
			this.$el.html( this.template( this.model.attributes ) );
			return this;
		}
	} );

	window.wcm = window.wcm || {};
	window.wcm.logo = window.wcm.logo || {};
	window.wcm.logo.views = views;

} )( jQuery, logoUploader, _, Backbone, uploader );/** @namespace logoUploader */
( function( window, $, _, plugin, models, views ) {
	'use strict';

	var logo = new models.Logo(),
		controller = {};

	// Flatten 'data', remove sub array
	logo.set( _.extend( plugin.data, {
		container : plugin.container,
		templates : plugin.templates,
		l10n      : plugin.l10n
	} ) );
	// Update model
	logo.on( 'change', logo.fetch );

	/** @namespace plugin.container.main */
	controller.Preview = new views.Preview( {
		el    : $( '#' + plugin.container.main ),
		model : logo
	} );

	/** @namespace plugin.container.thumb */
	controller.Thumb = new views.Thumb( {
		el    : $( '#' + plugin.container.thumb ),
		model : logo
	} );

	/** @namespace plugin.container.delete */
	controller.Delete = new views.Delete( {
		el    : $( '#' + plugin.container.delete ),
		model : logo
	} );

	/** @namespace plugin.container.caption */
	controller.Caption = new views.Caption( {
		el    : $( '#' + plugin.container.caption ),
		model : logo
	} );

	// Attach event handler
	$( document ).ready( function() {
		'use strict';

		// Bind event handler to file upload / drag&drop
		uploader.bind( 'FileUploaded', function( up, file, response ) {

			if ( 200 !== response.status ) {
				return;
			}

			// Triggers Model:fetch( att_id ) & event listeners in the views
			logo.set( { 'att_id' : response.response } );
		} );
	} );
} )( window, jQuery, _, logoUploader, window.wcm.logo.models, window.wcm.logo.views );