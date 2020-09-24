/* global jQuery,f9wishlist_params */
( function( $ ) {
	/**
	 * AddToWishlistHandler class.
	 */
	const AddToWishlistHandler = function() {
		this.requests = [];

		$( document.body )
			.on( 'click', '.f9wishlist-add-to-wishlist a', { addToWishlistHandler: this }, this.onAddToWishlist )
			.on( 'f9wishlist_show_login', { addToWishlistHandler: this }, this.showLogin );
	};

	/**
	 * Add add to wishlist event.
	 *
	 * @param {Object} request
	 */
	AddToWishlistHandler.prototype.addRequest = function( request ) {
		this.requests.push( request );

		if ( 1 === this.requests.length ) {
			this.run();
		}
	};

	/**
	 * Run add to wishlist events.
	 */
	AddToWishlistHandler.prototype.run = function() {
		const requestManager = this,
			originalCallback = requestManager.requests[ 0 ].complete;

		requestManager.requests[ 0 ].complete = function() {
			if ( typeof originalCallback === 'function' ) {
				originalCallback();
			}

			requestManager.requests.shift();

			if ( requestManager.requests.length > 0 ) {
				requestManager.run();
			}
		};

		$.ajax( this.requests[ 0 ] );
	};

	/**
	 * Handle the add to wishlist event.
	 *
	 * @param {event} e
	 */
	AddToWishlistHandler.prototype.onAddToWishlist = function( e ) {
		const $thisbutton = $( this );

		if ( ! $thisbutton.attr( 'data-product_id' ) ) {
			return true;
		}

		e.preventDefault();

		$thisbutton.removeClass( 'added' );
		$thisbutton.addClass( 'loading' );

		const data = {};

		// Fetch changes that are directly added by calling $thisbutton.data( key, value )
		$.each( $thisbutton.data(), function( key, value ) {
			data[ key ] = value;
		} );

		// Fetch data attributes in $thisbutton. Give preference to data-attributes because they can be directly modified by javascript
		// while `.data` are jquery specific memory stores.
		$.each( $thisbutton[ 0 ].dataset, function( key, value ) {
			data[ key ] = value;
		} );

		e.data.addToWishlistHandler.addRequest( {
			type: 'POST',
			url: f9wishlist_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_wishlist' ),
			data,
			error( response ) {
				console.log(response.statusCode);
				if ( 401 === response.status ) {
					e.data.addToWishlistHandler.showLogin();
				}
			},
			success( response ) {
				if ( response && 'unauthenticated' === response.data ) {
					$( document.body ).trigger( 'f9wishlist_show_login' );
				} else if ( response ) {
					console.log( 'add to wishlist' );
				}
			},
			dataType: 'json',
		} );
	};

	AddToWishlistHandler.prototype.modal = function() {
		let $modal = $( '#f9wishlistModal' ),
			$modalDialog = $( '<div/>' ),
			$modalContentBody = $( '<div/>' );
		if ( $modal.length > 0 ) {
			$modal.remove();
		}
		$modal = $( '<div/>' );
		$modal.attr(
			{
				id: 'f9wishlistModal',
				class: 'f9wishlist-modal',
			}
		);
		$modalContentBody.append(
			$( '<input/>' ).attr(
				{
					type: 'text',
					name: 'username',
					id: 'username',
					autocomplete: 'username',
					placeholder: f9wishlist_params.i18n_username_placeholder,
				}
			)
		);
		$modalContentBody.append(
			$( '<input/>' ).attr(
				{
					type: 'password',
					name: 'password',
					id: 'password',
					autocomplete: 'current-password',
					placeholder: f9wishlist_params.i18n_password_placeholder,
				}
			)
		);
		$modalContentBody.append(
			$( '<button/>' )
				.attr(
					{
						type: 'submit',
						name: 'login',
					}
				)
				.text( f9wishlist_params.i18n_login_button )
		);
		$modalDialog.append( $modalContentBody );
		$modal.append( $modalDialog );
		$( document.body ).append( $modal );
		$modal.f9wishlistmodal( 'show' );
	};

	AddToWishlistHandler.prototype.showLogin = function( e ) {
		e.data.addToWishlistHandler.modal();
	};

	/**
	 * Init AddToWishlistHandler.
	 */
	new AddToWishlistHandler();
}( jQuery ) );
