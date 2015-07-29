( function( $, plugin, _, Backbone, uploader ) {
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

} )( jQuery, logoUploader, _, Backbone, uploader );