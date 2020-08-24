/* global jQuery */
( function( $ ) {
	/**
	 * AddToWishlistHandler class.
	 */
	const AddToWishlistHandler = function() {
		$( document.body )
			.on( 'click', '.f9wishlist-add-to-wishlist a', { addToWishlistHandler: this }, this.onAddToWishlist );
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
	};

	/**
	 * Init AddToWishlistHandler.
	 */
	new AddToWishlistHandler();
}( jQuery ) );
