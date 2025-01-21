import './css/widgetizer.css';

import Sortable from 'sortablejs';
import { getFieldType, submitSettingsForm } from './js/utils';

window.addEventListener( 'DOMContentLoaded', function () {
	const sortableFields = document.querySelectorAll( '.widgetizer-field-type-sortable' );

	const updateSortableValue = ( field ) => {
		const hiddenField = field.querySelector( 'input[type="hidden"]' );

		const newValue = [];

		field.querySelectorAll( 'li' ).forEach( ( li ) => {
			if ( ! li.classList.contains( 'invisible' ) ) {
				newValue.push( li.dataset.value );
			}
		} );

		hiddenField.value = newValue.join( ',' );
	};

	if ( sortableFields ) {
		sortableFields.forEach( ( sortableField ) => {
			const sortableList = sortableField.querySelector( 'ul.sortable' );

			if ( sortableList ) {
				Sortable.create( sortableList, {
					animation: 100,
					onEnd: function ( evt ) {
						updateSortableValue( sortableField );
					},
				} );

				const listItems = sortableList.querySelectorAll( 'li' );

				listItems.forEach( ( li ) => {
					const visibilityIcon = li.querySelector( 'i.visibility' );
					if ( visibilityIcon ) {
						visibilityIcon.addEventListener( 'click', function ( event ) {
							event.stopPropagation();
							this.classList.toggle( 'dashicons-visibility-faint' );
							li.classList.toggle( 'invisible' );
							updateSortableValue( sortableField );
						} );
					}
				} );
			}
		} );
	}

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
} );
