import './css/widgetizer.css';

import { getFieldType, submitSettingsForm } from './js/utils';

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
			.on( 'click', function () {
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
				const refContainer = link.closest( '.widgetizer-field-refs' );

				const refData = JSON.parse( refContainer.getAttribute( 'data-ref' ) );

				if ( fieldContainer ) {
					const inputField = fieldContainer.querySelector( 'input[type="number"]' );

					if ( inputField ) {
						const value = link.getAttribute( 'data-val' );

						inputField.value = value;

						if ( true === refData.submitter ) {
							submitSettingsForm( fieldContainer );
						}
					}
				}
			} );
		} );
	}

	// Submitter fields.
	const submitterFields = document.querySelectorAll( '.widgetizer-field-mode-submitter' );

	if ( submitterFields ) {
		const inputTypeFields = [ 'buttonset', 'radio', 'radioimage' ];

		submitterFields.forEach( ( submitterField ) => {
			const fieldType = getFieldType( submitterField );

			if ( inputTypeFields.includes( fieldType ) ) {
				const childInputFields = submitterField.querySelectorAll( 'input' );

				childInputFields.forEach( ( childInputField ) => {
					childInputField.addEventListener( 'click', function ( event ) {
						submitSettingsForm( childInputField );
					} );
				} );
			} else if ( 'select' === fieldType ) {
				submitterField.addEventListener( 'change', function () {
					submitSettingsForm( submitterField );
				} );
			}
		} );
	}
} )( jQuery );
