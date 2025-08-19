import './css/widgetizer.css';

import Sortable from 'sortablejs';
import { getFieldType, submitSettingsForm } from './js/utils';

const handleFormReset = () => {
	const submitParagraph = document.querySelector( '.dashboard-widget-control-form .submit' );

	if ( submitParagraph ) {
		const resetButton = document.createElement( 'input' );
		resetButton.type = 'submit';
		resetButton.value = 'Reset';
		resetButton.name = 'reset';
		resetButton.className = 'button button-secondary';

		const form = submitParagraph.closest( 'form' );

		submitParagraph.appendChild( resetButton );

		resetButton.addEventListener( 'click', function ( event ) {
			event.preventDefault();

			const hiddenInput = form.querySelector( 'input[name="submit_type"]' );

			if ( hiddenInput ) {
				hiddenInput.value = 'reset';
			}

			if ( form ) {
				HTMLFormElement.prototype.submit.call( form );
			}
		} );
	}
};

document.addEventListener( 'DOMContentLoaded', function () {
	// Reset button.
	handleFormReset();

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

					const upIcon = li.querySelector( 'i.up' );
					if ( upIcon ) {
						upIcon.addEventListener( 'click', function ( event ) {
							event.stopPropagation();
							const previousSibling = li.previousElementSibling;

							if ( previousSibling ) {
								sortableList.insertBefore( li, previousSibling );
								updateSortableValue( sortableField );
							}
						} );
					}

					const downIcon = li.querySelector( 'i.down' );
					if ( downIcon ) {
						downIcon.addEventListener( 'click', function ( event ) {
							event.stopPropagation();
							const nextSibling = li.nextElementSibling;

							if ( nextSibling ) {
								sortableList.insertBefore( nextSibling, li );
								updateSortableValue( sortableField );
							}
						} );
					}
				} );
			}
		} );
	}

	// Handle field presets.
	const presetLinks = document.querySelectorAll( '.widgetizer-field-presets a' );

	if ( presetLinks ) {
		presetLinks.forEach( ( link ) => {
			link.addEventListener( 'click', function ( event ) {
				event.preventDefault();

				const fieldContainer = link.closest( '.widgetizer-field' );
				const presetContainer = link.closest( '.widgetizer-field-presets' );

				const presetData = JSON.parse( presetContainer.getAttribute( 'data-preset' ) );

				if ( fieldContainer ) {
					const inputField = fieldContainer.querySelector( 'input' );

					if ( inputField ) {
						const value = link.getAttribute( 'data-val' );

						inputField.value = value;

						if ( true === presetData.submitter ) {
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
		const inputTypeFields = [ 'buttonset', 'checkbox', 'radio', 'toggle' ];

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
