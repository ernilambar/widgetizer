const getFieldType = ( element ) => {
	const classes = element.classList;

	const typeClass = Array.from( classes ).find( ( cls ) =>
		cls.startsWith( 'widgetizer-field-type-' )
	);

	return typeClass ? typeClass.replace( 'widgetizer-field-type-', '' ) : null;
};

export { getFieldType };
