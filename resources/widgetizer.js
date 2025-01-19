import './widgetizer.css';

( function ( $ ) {
	$( '.widgetizer-field-type-sortable' ).each( function ( index, element ) {
		const $this = $( this );

		$this.sortableContainer = $this.find( 'ul.sortable' ).first();

		$this.updateValue = () => {
			const $hiddenField = $this.sortableContainer.find( 'input[type="hidden"]' );

			const newValue = [];

			$this.sortableContainer.find( 'li' ).each( function () {
				if ( ! $( this ).is( '.invisible' ) ) {
					newValue.push( $( this ).data( 'value' ) );
				}
			} );

			$hiddenField.val( newValue.join( ',' ) );
		};

		$this.sortableContainer
			.sortable( {
				stop() {
					$this.updateValue();
				},
			} )
			.disableSelection()
			.find( 'li' )
			.each( function () {
				$( this )
					.find( 'i.visibility' )
					.on( 'click', function () {
						$( this )
							.toggleClass( 'dashicons-visibility-faint' )
							.parents( 'li:eq(0)' )
							.toggleClass( 'invisible' );
					} );
			} )
			.click( function () {
				$this.updateValue();
			} );
	} );

	// Handle field ref links.
	const refLinks = document.querySelectorAll( '.widgetizer-field-refs a' );

	if ( refLinks ) {
		refLinks.forEach( ( link ) => {
			link.addEventListener( 'click', function ( event ) {
				event.preventDefault();

				const fieldContainer = link.closest( '.widgetizer-field' );

				if ( fieldContainer ) {
					const inputField = fieldContainer.querySelector( 'input[type="number"]' );

					if ( inputField ) {
						const value = link.getAttribute( 'data-val' );

						inputField.value = value;

						const form = fieldContainer.closest( 'form' );

						if ( form ) {
							HTMLFormElement.prototype.submit.call( form );
						}
					}
				}
			} );
		} );
	}
} )( jQuery );
