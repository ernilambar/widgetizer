<?php
/**
 * Basic Widgetizer Example
 */

// Include the Widgetizer class.
require_once __DIR__ . '/../src/Widgetizer.php';

use Nilambar\Widgetizer\Widgetizer;

/**
 * Basic Widgetizer Example Class.
 */
class Basic_Widgetizer_Example extends Widgetizer {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$fields = [
			// Text field example.
			'text_field' => [
				'id'          => 'text_field',
				'type'        => 'text',
				'title'       => esc_html__( 'Text Field:' ),
				'default'     => 'Default text value',
				'placeholder' => esc_html__( 'Enter text here' ),
			],

			// Number field example.
			'number_field' => [
				'id'          => 'number_field',
				'type'        => 'number',
				'title'       => esc_html__( 'Number Field:' ),
				'default'     => 10,
			],

			// URL field example.
			'url_field' => [
				'id'          => 'url_field',
				'type'        => 'url',
				'title'       => esc_html__( 'URL Field:' ),
				'default'     => 'https://example.com',
				'placeholder' => esc_html__( 'https://example.com' ),
			],

			// Email field example.
			'email_field' => [
				'id'          => 'email_field',
				'type'        => 'email',
				'title'       => esc_html__( 'Email Field:' ),
				'default'     => 'admin@example.com',
				'placeholder' => esc_html__( 'admin@example.com' ),
			],

			// Textarea field example.
			'textarea_field' => [
				'id'          => 'textarea_field',
				'type'        => 'textarea',
				'title'       => esc_html__( 'Textarea Field:' ),
				'default'     => 'Default textarea content.',
				'placeholder' => esc_html__( 'Enter your content here...' ),
			],

			// Select field example.
			'select_field' => [
				'id'      => 'select_field',
				'type'    => 'select',
				'title'   => esc_html__( 'Select Field:' ),
				'default' => 'option2',
				'choices' => [
					'option1' => esc_html__( 'First Option' ),
					'option2' => esc_html__( 'Second Option' ),
					'option3' => esc_html__( 'Third Option' ),
					'option4' => esc_html__( 'Fourth Option' ),
				],
			],

			// Buttonset field example.
			'buttonset_field' => [
				'id'      => 'buttonset_field',
				'type'    => 'buttonset',
				'title'   => esc_html__( 'Buttonset Field:' ),
				'default' => 'center',
				'choices' => [
					'left'   => esc_html__( 'Left' ),
					'center' => esc_html__( 'Center' ),
					'right'  => esc_html__( 'Right' ),
				],
			],

			// Radio field example.
			'radio_field' => [
				'id'      => 'radio_field',
				'type'    => 'radio',
				'title'   => esc_html__( 'Radio Field:' ),
				'default' => 'option1',
				'choices' => [
					'option1' => esc_html__( 'Radio Option 1' ),
					'option2' => esc_html__( 'Radio Option 2' ),
					'option3' => esc_html__( 'Radio Option 3' ),
				],
			],

			// Radio image field example.
			'radioimage_field' => [
				'id'      => 'radioimage_field',
				'type'    => 'radioimage',
				'title'   => esc_html__( 'Radio Image Field:' ),
				'default' => 'layout1',
				'choices' => [
					'layout1' => 'https://picsum.photos/id/1/150/100',
					'layout2' => 'https://picsum.photos/id/2/150/100',
					'layout3' => 'https://picsum.photos/id/3/150/100',
				],
			],

				// Multicheckbox field example.
	'multicheckbox_field' => [
		'id'      => 'multicheckbox_field',
		'type'    => 'multicheckbox',
		'title'   => esc_html__( 'Multicheckbox Field:' ),
				'default' => [ 'option1', 'option3' ],
				'choices' => [
					'option1' => esc_html__( 'Check Option 1' ),
					'option2' => esc_html__( 'Check Option 2' ),
					'option3' => esc_html__( 'Check Option 3' ),
					'option4' => esc_html__( 'Check Option 4' ),
				],
			],

			// Sortable field example.
			'sortable_field' => [
				'id'      => 'sortable_field',
				'type'    => 'sortable',
				'title'   => esc_html__( 'Sortable Field:' ),
				'default' => [ 'item1', 'item2', 'item3' ],
				'arrows'  => true,
				'choices' => [
					'item1' => esc_html__( 'Sortable Item 1' ),
					'item2' => esc_html__( 'Sortable Item 2' ),
					'item3' => esc_html__( 'Sortable Item 3' ),
					'item4' => esc_html__( 'Sortable Item 4' ),
					'item5' => esc_html__( 'Sortable Item 5' ),
				],
			],

			// Checkbox field example.
			'checkbox_field' => [
				'id'        => 'checkbox_field',
				'type'      => 'checkbox',
				'title'     => esc_html__( 'Checkbox Field:' ),
				'default'   => true,
				'side_text' => esc_html__( 'Enable this feature' ),
			],

			// Toggle field example.
			'toggle_field' => [
				'id'        => 'toggle_field',
				'type'      => 'toggle',
				'title'     => esc_html__( 'Toggle Field:' ),
				'default'   => false,
				'side_text' => esc_html__( 'Enable advanced mode' ),
			],
		];

		parent::__construct( 'basic_widgetizer_example', esc_html__( 'Basic Widgetizer Example' ), $fields );
	}

	/**
	 * Render the widget content.
	 */
	public function widget() {
		$settings = $this->get_settings();

		echo '<div class="widgetizer-example-content">';
		echo '<h3>' . esc_html__( 'Widget Settings Display' ) . '</h3>';
		echo '<p>' . esc_html__( 'This widget demonstrates all field types. Current settings:' ) . '</p>';

		echo '<ul>';
		foreach ( $settings as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}

			if ( is_bool( $value ) ) {
				$value = $value ? 'Yes' : 'No';
			}
			echo '<li><strong>' . esc_html( $key ) . ':</strong> ' . esc_html( $value ) . '</li>';
		}
		echo '</ul>';

		echo '</div>';
	}
}

// Initialize the widget.
new Basic_Widgetizer_Example();
