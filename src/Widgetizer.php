<?php
/**
 * Class Widgetizer
 *
 * @package Widgetizer
 */

declare(strict_types=1);

namespace Nilambar\Widgetizer;

/**
 * Widgetizer class.
 *
 * @since 1.0.0
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
abstract class Widgetizer {

	/**
	 * Widget ID.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $widget_id;

	/**
	 * Widget name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $widget_name;

	/**
	 * Widget fields.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $fields;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $widget_id Widget ID.
	 * @param string $widget_name Widget name.
	 * @param array  $fields Widget fields.
	 */
	public function __construct( string $widget_id, string $widget_name, array $fields = [] ) {
		$this->widget_id   = $widget_id;
		$this->widget_name = $widget_name;
		$this->fields      = $fields;

		add_action( 'wp_dashboard_setup', [ $this, 'register' ] );
	}

	/**
	 * Render the content of the widget.
	 *
	 * Must be implemented by child classes.
	 *
	 * @since 1.0.0
	 */
	abstract public function widget();

	/**
	 * Registers the widget.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		if ( ! empty( $this->fields ) ) {
			wp_add_dashboard_widget( $this->widget_id, $this->widget_name, [ $this, 'widget' ], [ $this, 'settings' ] );
		} else {
			wp_add_dashboard_widget( $this->widget_id, $this->widget_name, [ $this, 'widget' ] );
		}
	}

	/**
	 * Render widget settings form.
	 *
	 * @since 1.0.0
	 */
	public function settings() {
		$this->update_form();
		$this->render_form();
	}

	/**
	 * Save widget option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The setting key.
	 * @param mixed  $value The value to set.
	 */
	protected function set_setting( $key, $value ) {
		$settings = get_option( $this->widget_id, [] );

		$settings[ $key ] = $value;

		update_option( $this->widget_id, $settings );
	}

	/**
	 * Returns widget options.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The setting key.
	 * @return mixed Settings value.
	 */
	protected function get_setting( string $key ) {
		$default = $this->fields[ $key ]['default'] ?? null;

		$settings = get_option( $this->widget_id, [] );

		return array_key_exists( $key, $settings ) ? $settings[ $key ] : $default;
	}

	/**
	 * Returns field name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Field key.
	 * @return string Field name.
	 */
	private function get_field_name( string $key ): string {
		return $this->widget_id . "[{$key}]";
	}

	/**
	 * Checks whether valid save action is triggered.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if valid, otherwise false.
	 */
	protected function is_valid_save_action() {
		return ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['action'] ) && 'save_' . $this->widget_id === $_POST['action'] );
	}

	/**
	 * Renders widget settings form.
	 *
	 * @since 1.0.0
	 */
	protected function render_form() {
		if ( empty( $this->fields ) ) {
			return;
		}

		echo '<div class="widgetizer-widget-settings-wrap">';

		foreach ( $this->fields as $field ) {
			$this->render_form_field( $field );
		}

		echo '<input type="hidden" name="action" value="' . esc_attr( 'save_' . $this->widget_id ) . '" />';

		echo '</div><!-- .widgetizer-widget-settings-wrap -->';
	}

	/**
	 * Renders single form field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field detail.
	 */
	private function render_form_field( $field ) {
		$field_key = $field['id'] ?? '';

		if ( empty( $field_key ) ) {
			return;
		}

		$callback_name = $field['type'];
		$callback_name = strtolower( str_replace( '-', '_', $callback_name ) );

		if ( is_callable( [ $this, 'callback_' . $callback_name ] ) ) {
			call_user_func_array( [ $this, 'callback_' . $callback_name ], [ $field ] );
		}
	}

	/**
	 * Render field label.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 */
	private function render_field_label( array $args ) {
		echo '<label class="widgetizer-field-label">' . esc_html( $args['title'] ) . '</label>';
	}

	/**
	 * Render field open markup.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 */
	private function render_field_open( array $args ) {
		$wf_data = [
			'submitter' => ( isset( $args['submitter'] ) && true === $args['submitter'] ) ? true : false,
		];

		$field_attrs = [
			'class'       => [
				'widgetizer-field',
				'widgetizer-field-type-' . $args['type'],
			],
			'data-wfdata' => wp_json_encode( $wf_data ),
		];

		echo '<div ' . $this->render_attr( $field_attrs, false ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<p style="border:1px red solid;">';
	}

	/**
	 * Render field close markup.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 */
	private function render_field_close( array $args ) {
		echo '</p>';
		echo '</div><!-- .widgetizer-field.widgetizer-field-type-' . esc_attr( $args['type'] ) . ' -->';
	}

	/**
	 * Render text.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 */
	private function callback_text( array $args ) {
		$field_key = $args['id'] ?? '';

		if ( empty( $field_key ) ) {
			return;
		}

		$attr = [
			'type'  => $args['type'],
			'name'  => $this->get_field_name( $field_key ),
			'value' => $this->get_setting( $field_key ),
			'class' => '',
		];

		$attributes = $this->render_attr( $attr, false );

		$html = sprintf( '<input %s />', $attributes );

		$this->render_field_open( $args );

		$this->render_field_label( $args );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$this->render_field_refs( $args );

		$this->render_field_close( $args );
	}

	/**
	 * Render number.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function callback_number( array $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Render select.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function callback_select( array $args ) {
		$field_key = $args['id'] ?? '';

		if ( empty( $field_key ) ) {
			return;
		}

		$value = $this->get_setting( $field_key );

		$this->render_field_open( $args );

		$this->render_field_label( $args );
		?>
			<select name="<?php echo esc_attr( $this->get_field_name( $field_key ) ); ?>">

			<?php foreach ( $args['choices'] as $choice_key => $choice_label ) : ?>

				<option value="<?php echo esc_attr( $choice_key ); ?>" <?php selected( $value, $choice_key ); ?>><?php echo esc_html( $choice_label ); ?></option>

			<?php endforeach; ?>

			</select>
		<?php
		$this->render_field_close( $args );
	}

	/**
	 * Render buttonset.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function callback_buttonset( array $args ) {
		$field_key = $args['id'] ?? '';

		if ( empty( $field_key ) ) {
			return;
		}

		$value = $this->get_setting( $field_key );

		$this->render_field_open( $args );

		$this->render_field_label( $args );
		?>
			<div class="buttonset">
				<?php
				foreach ( $args['choices'] as $key => $label ) {
					$clean_id = $field_key . '---' . $key;

					$attr = [
						'type'  => 'radio',
						'name'  => $this->get_field_name( $field_key ),
						'id'    => $clean_id,
						'value' => $key,
						'class' => [ 'switch-input' ],
					];

					$attributes = $this->render_attr( $attr, false );

					printf( '<input %s %s ><label class="switch-label" for="%s">%s</label></input>', $attributes, checked( $value, $key, false ), $clean_id, $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div><!-- .buttonset -->
		<?php
		$this->render_field_close( $args );
	}

	/**
	 * Render radio.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function callback_radio( array $args ) {
		$field_key = $args['id'] ?? '';

		if ( empty( $field_key ) ) {
			return;
		}

		$value = $this->get_setting( $field_key );

		$this->render_field_open( $args );

		$this->render_field_label( $args );

		$layout_class = 'layout-vertical';

		if ( isset( $args['layout'] ) && ! empty( $args['layout'] ) ) {
			$layout_class = 'layout-' . $args['layout'];
		}

		echo '<ul class="radio-list ' . esc_attr( $layout_class ) . '">';

		foreach ( $args['choices'] as $choice_key => $choice_label ) {
			$attr = [
				'type'  => 'radio',
				'name'  => $this->get_field_name( $field_key ),
				'value' => $choice_key,
			];

			$attributes = $this->render_attr( $attr, false );

			echo '<li>';

			printf( '<label><input %s %s />%s</label>', $attributes, checked( $value, $choice_key, false ), esc_html( $choice_label ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			echo '</li>';
		}

		echo '</ul>';

		$this->render_field_close( $args );
	}

	/**
	 * Render multicheck.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function callback_multicheck( array $args ) {
		$field_key = $args['id'] ?? '';

		if ( empty( $field_key ) ) {
			return;
		}

		$field_value = (array) $this->get_setting( $field_key );

		$html = '';

		if ( ! empty( $args['choices'] ) ) {
			$html .= '<ul>';

			foreach ( $args['choices'] as $key => $label ) {
				$attr = [
					'type'  => 'checkbox',
					'name'  => $this->get_field_name( $field_key ) . '[]',
					'value' => $key,
				];

				$attributes = $this->render_attr( $attr, false );

				$html .= '<li>';
				$html .= sprintf( '<input %s %s />', $attributes, checked( in_array( (string) $key, $field_value, true ), true, false ) );
				$html .= $label;
				$html .= '</li>';
			}

			$html .= '</ul>';
		}

		$this->render_field_open( $args );

		$this->render_field_label( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$this->render_field_close( $args );
	}

	/**
	 * Render sortable.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function callback_sortable( array $args ) {
		$field_key = $args['id'] ?? '';

		if ( empty( $field_key ) ) {
			return;
		}

		$field_value = (array) $this->get_setting( $field_key );

		$html = '';

		if ( ! empty( $args['choices'] ) ) {
			$active_choices = $field_value;

			$rem_choices = array_diff( array_keys( $args['choices'] ), $active_choices );

			$final_choices = array_merge( $active_choices, $rem_choices );

			$html .= '<ul class="sortable">';

			foreach ( $final_choices as $key ) {
				$li_class = in_array( (string) $key, $active_choices, true ) ? '' : 'invisible';

				$html .= '<li class="' . esc_attr( $li_class ) . '" data-value="' . esc_attr( $key ) . '">';
				$html .= "<i class='dashicons dashicons-menu'></i><i class='dashicons dashicons-visibility visibility'></i>";
				$html .= $args['choices'][ $key ];
				$html .= '</li>';
			}

			$html .= '<input type="hidden" name="' . esc_attr( $this->get_field_name( $field_key ) ) . '" value="' . esc_attr( implode( ',', $active_choices ) ) . '" />';

			$html .= '</ul>';
		}

		$this->render_field_open( $args );

		$this->render_field_label( $args );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$this->render_field_close( $args );
	}

	/**
	 * Update form.
	 *
	 * @since 1.0.0
	 */
	protected function update_form() {
		if ( empty( $this->fields ) ) {
			return;
		}

		if ( $this->is_valid_save_action() ) {
			$settings = [];

			$post_items = $_POST[ $this->widget_id ] ?? []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

			foreach ( $this->fields as $field_key => $field ) {
				switch ( $field['type'] ) {
					case 'url':
						$raw_value       = array_key_exists( $field_key, $post_items ) ? $post_items[ $field_key ] : '';
						$sanitized_value = eac_url_raw( wp_unslash( $raw_value ) );
						break;

					case 'number':
						$raw_value       = array_key_exists( $field_key, $post_items ) ? $post_items[ $field_key ] : '';
						$sanitized_value = intval( wp_unslash( $raw_value ) );
						break;

					case 'multicheck':
						$raw_value       = array_key_exists( $field_key, $post_items ) ? $post_items[ $field_key ] : [];
						$sanitized_value = array_map( 'sanitize_text_field', $raw_value );
						break;

					case 'sortable':
						$input_value     = array_key_exists( $field_key, $post_items ) ? (string) $post_items[ $field_key ] : '';
						$raw_value       = wp_parse_list( $input_value );
						$sanitized_value = array_map( 'sanitize_text_field', $raw_value );
						break;

					default:
						$raw_value       = array_key_exists( $field_key, $post_items ) ? $post_items[ $field_key ] : '';
						$sanitized_value = sanitize_text_field( wp_unslash( $raw_value ) );
						break;
				}

				$settings[ $field_key ] = $sanitized_value;
			}

			if ( ! empty( $settings ) ) {
				foreach ( $settings as $key => $value ) {
					$this->set_setting( $key, $value );
				}
			}
		}
	}

	/**
	 * Render attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Attributes.
	 * @param bool  $display    Whether to echo or not.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	private function render_attr( array $attributes, bool $display = true ) {
		if ( empty( $attributes ) ) {
			return;
		}

		$html = '';

		foreach ( $attributes as $name => $value ) {
			$esc_value = '';

			if ( 'class' === $name && is_array( $value ) ) {
				$value = join( ' ', array_unique( $value ) );
			}

			if ( false !== $value && 'href' === $name ) {
				$esc_value = esc_url( $value );
			} elseif ( false !== $value ) {
				$esc_value = esc_attr( $value );
			}

			if ( ! in_array( $name, [ 'class', 'id', 'title', 'style', 'name' ], true ) ) {
				$html .= false !== $value ? sprintf( ' %s="%s"', esc_html( $name ), $esc_value ) : esc_html( " {$name}" );
			} else {
				$html .= $value ? sprintf( ' %s="%s"', esc_html( $name ), $esc_value ) : '';
			}
		}

		if ( ! empty( $html ) && true === $display ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $html;
		}
	}

	/**
	 * Render field ref links.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Field arguments.
	 */
	private function render_field_refs( array $args ) {
		if ( ! array_key_exists( 'refs', $args ) ) {
			return;
		}

		if ( empty( $args['refs'] ) || ! is_array( $args['refs'] ) ) {
			return;
		}

		if ( empty( $args['refs']['choices'] ) || ! is_array( $args['refs']['choices'] ) ) {
			return;
		}

		$ref_data = [
			'submitter' => ( isset( $args['refs']['submitter'] ) && true === $args['refs']['submitter'] ) ? true : false,
		];

		$ref_attrs = [
			'class'    => [ 'widgetizer-field-refs' ],
			'data-ref' => wp_json_encode( $ref_data ),
		];

		echo '<div ' . $this->render_attr( $ref_attrs, false ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ul>';

		foreach ( $args['refs']['choices'] as $val => $label ) {
			echo '<li><a href="#" class="button" data-val="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</a></li>';
		}

		echo '</ul>';
		echo '</div>';
	}
}
