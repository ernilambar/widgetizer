const getFieldType = ( element ) => {
	const classes = element.classList;

	const fieldType = Array.from( classes ).find( ( cls ) =>
		cls.startsWith( 'widgetizer-field-type-' )
	);

	return fieldType ? fieldType.replace( 'widgetizer-field-type-', '' ) : null;
};

const submitSettingsForm = ( element ) => {
	const form = element.closest( 'form' );
	if ( form ) {
		HTMLFormElement.prototype.submit.call( form );
	}
};

export { getFieldType, submitSettingsForm };
