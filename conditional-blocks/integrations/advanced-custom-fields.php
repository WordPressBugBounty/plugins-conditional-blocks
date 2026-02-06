<?php
class CB_AFC_Integration {
	private $is_acf_active = false;
	private $is_pro = false;
	private $tested_version = '6.3.12';

	public function __construct() {
		$this->is_acf_active = class_exists( 'ACF' );
		
		add_filter( 'conditional_blocks_register_condition_categories', [ $this, 'register_categories' ], 10, 1 );
		add_filter( 'conditional_blocks_register_condition_types', [ $this, 'register_conditions' ], 10, 1 );
			}

	/**
	 * Register condition categories for the ACF integration.
	 *
	 * Adds the 'Advanced Custom Fields' category to the list of available categories.
	 *
	 * @param array $categories The list of available categories.
	 * @return array The updated list of categories.
	 */
	public function register_categories( $categories ) {
		$categories[] = [
			'value' => 'advanced_custom_fields',
			'label' => __( 'Advanced Custom Fields (ACF)', 'conditional-blocks' ),
			'icon' => plugins_url( 'assets/images/mini-colored/advanced-custom-fields.svg', __DIR__ ), // URL or path to your icon, or dashicon name.
			'tag' => 'plugin',
		];
		return $categories;
	}

	/**
	 * Register condition types for the ACF integration.
	 *
	 * Adds the 'ACF Field Value' condition type to the list of available types.
	 *
	 * @param array $conditions The list of available condition types.
	 * @return array The updated list of condition types.
	 */
	public function register_conditions( $conditions ) {

		$conditions[] = [
			'type' => 'acf_field_value',
			'label' => __( 'ACF Field Value', 'conditional-blocks' ),
			'is_pro' => true,
			'tag' => 'plugin',
			'is_disabled' => ! $this->is_acf_active || ! $this->is_pro || ! class_exists( 'ACF' ),
			'description' => '',
			'category' => 'advanced_custom_fields',
			'fields' => [
				[
					'key' => 'acf_field',
					'type' => 'select',
					'attributes' => [
						'label' => __( 'ACF Field', 'conditional-blocks' ),
						'help' => __( 'Select a ACF Field from a Field Group', 'conditional-blocks' ),
						'placeholder' => __( 'Select a field', 'conditional-blocks' ),
						'searchable' => true,
					],
					'options' => class_exists( 'ACF' ) ? $this->get_acf_field_options( false ) : [],
				],
				[
					'key' => 'operator',
					'type' => 'select',
					'attributes' => [
						'label' => __( 'Operator', 'conditional-blocks' ),
						'help' => __( 'Select a operator used to check the value', 'conditional-blocks' ),
						'searchable' => true,
					],
					'options' => $this->get_operator_options(),
				],
				[
					'key' => 'expected_value',
					'type' => 'text',
					'requires' => [
						'operator' => [ 'equal', 'not_equal', 'contains', 'not_contains', 'greater_than', 'less_than', 'greater_than_or_equal_to', 'less_than_or_equal_to' ],
					],
					'attributes' => [
						'label' => __( 'Field Value', 'conditional-blocks' ),
						'help' => __( 'Set the value to compare against - case sensitive.', 'conditional-blocks' ),
						'placeholder' => '',
					],
				],
			],
		];

		$conditions[] = [
			'type' => 'acf_user_field_value',
			'label' => __( 'ACF User Field Value', 'conditional-blocks' ),
			'is_pro' => true,
			'tag' => 'plugin',
			'is_disabled' => ! $this->is_acf_active || ! $this->is_pro || ! class_exists( 'ACF' ),
			'description' => __( 'Check ACF field values stored on WordPress user profiles', 'conditional-blocks' ),
			'category' => 'advanced_custom_fields',
			'fields' => [
				[
					'key' => 'user_source',
					'type' => 'select',
					'attributes' => [
						'label' => __( 'User Source', 'conditional-blocks' ),
						'help' => __( 'Which user to check the field value for', 'conditional-blocks' ),
						'default' => 'current_user',
					],
					'options' => [
						[ 'label' => __( 'Current Logged-in User', 'conditional-blocks' ), 'value' => 'current_user' ],
						[ 'label' => __( 'Post Author', 'conditional-blocks' ), 'value' => 'post_author' ],
					],
				],
				[
					'key' => 'acf_field',
					'type' => 'select',
					'attributes' => [
						'label' => __( 'ACF Field', 'conditional-blocks' ),
						'help' => __( 'Select an ACF Field from user profile field groups', 'conditional-blocks' ),
						'placeholder' => __( 'Select a field', 'conditional-blocks' ),
						'searchable' => true,
					],
					'options' => class_exists( 'ACF' ) ? $this->get_acf_field_options( true ) : [],
				],
				[
					'key' => 'operator',
					'type' => 'select',
					'attributes' => [
						'label' => __( 'Operator', 'conditional-blocks' ),
						'help' => __( 'Select an operator used to check the value', 'conditional-blocks' ),
						'searchable' => true,
					],
					'options' => $this->get_operator_options(),
				],
				[
					'key' => 'expected_value',
					'type' => 'text',
					'requires' => [
						'operator' => [ 'equal', 'not_equal', 'contains', 'not_contains', 'greater_than', 'less_than', 'greater_than_or_equal_to', 'less_than_or_equal_to' ],
					],
					'attributes' => [
						'label' => __( 'Field Value', 'conditional-blocks' ),
						'help' => __( 'Set the value to compare against - case sensitive.', 'conditional-blocks' ),
						'placeholder' => '',
					],
				],
			],
		];

		return $conditions;
	}

	/**
	 * Get the standard operator options for ACF field conditions
	 *
	 * @return array Array of operator options
	 */
	private function get_operator_options() {
		return [
			[ 'label' => __( 'Has any value', 'conditional-blocks' ), 'value' => 'not_empty' ],
			[ 'label' => __( 'No value', 'conditional-blocks' ), 'value' => 'empty' ],
			[ 'label' => __( 'Equal to', 'conditional-blocks' ), 'value' => 'equal' ],
			[ 'label' => __( 'Not equal to', 'conditional-blocks' ), 'value' => 'not_equal' ],
			[ 'label' => __( 'Contains', 'conditional-blocks' ), 'value' => 'contains' ],
			[ 'label' => __( 'Does not contain', 'conditional-blocks' ), 'value' => 'not_contains' ],
			[ 'label' => __( 'Greater than', 'conditional-blocks' ), 'value' => 'greater_than' ],
			[ 'label' => __( 'Less than', 'conditional-blocks' ), 'value' => 'less_than' ],
			[ 'label' => __( 'Greater than or equal to', 'conditional-blocks' ), 'value' => 'greater_than_or_equal_to' ],
			[ 'label' => __( 'Less than or equal to', 'conditional-blocks' ), 'value' => 'less_than_or_equal_to' ],
		];
	}

	/**
	 * Extract subfields from a repeater field
	 *
	 * @param array $repeater_field
	 * @return array
	 */
	private function get_repeater_subfields( $repeater_field ) {
		$subfields = [];

		if ( empty( $repeater_field['sub_fields'] ) || ! is_array( $repeater_field['sub_fields'] ) ) {
			return $subfields;
		}

		foreach ( $repeater_field['sub_fields'] as $subfield ) {
			if ( empty( $subfield['label'] ) || empty( $subfield['name'] ) ) {
				continue;
			}

			$subfields[] = [
				'label' => $subfield['label'],
				'value' => $subfield['name'],
				'type' => $subfield['type'],
			];
		}

		return $subfields;
	}

	/**
	 * Process a single ACF field for options array
	 *
	 * @param array $field The ACF field array
	 * @param bool $include_groups Whether to include group fields
	 * @return array Array of field options
	 */
	private function process_field_for_options( $field, $include_groups = true ) {
		if ( empty( $field['label'] ) || empty( $field['name'] ) ) {
			return [];
		}

		$field_options = [];

		// Process repeater fields
		if ( $field['type'] === 'repeater' ) {
			$subfields = $this->get_repeater_subfields( $field );
			if ( ! empty( $subfields ) ) {
				foreach ( $subfields as $subfield ) {
					$field_options[] = [
						'label' => $field['label'] . ' → ' . $subfield['label'],
						'value' => $field['name'] . '.' . $subfield['value']
					];
				}
			}
		}
		// Process group fields
		else if ( $field['type'] === 'group' && $include_groups ) {
			if ( ! empty( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
				foreach ( $field['sub_fields'] as $subfield ) {
					if ( empty( $subfield['label'] ) || empty( $subfield['name'] ) ) {
						continue;
					}
					$field_options[] = [
						'label' => $field['label'] . ' → ' . $subfield['label'],
						'value' => $field['name'] . '_' . $subfield['name']
					];
				}
			}
		}
		// Process regular fields
		else if ( $field['type'] !== 'group' ) {
			$field_options[] = [
				'label' => $field['label'],
				'value' => $field['name']
			];
		}

		return $field_options;
	}

	/**
	 * Check if a field group is assigned to users
	 *
	 * @param array $group The ACF field group array
	 * @return bool True if group is for users
	 */
	private function is_user_field_group( $group ) {
		if ( empty( $group['location'] ) || ! is_array( $group['location'] ) ) {
			return false;
		}

		foreach ( $group['location'] as $location_group ) {
			if ( ! is_array( $location_group ) ) {
				continue;
			}
			foreach ( $location_group as $location_rule ) {
				if ( isset( $location_rule['param'] ) && $location_rule['param'] === 'user_form' ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Unified method to get ACF field options
	 *
	 * @param bool $filter_for_users Whether to filter for user field groups only
	 * @return array Array of field options
	 */
	private function get_acf_field_options( $filter_for_users = false ) {
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return [];
		}

		$field_groups = acf_get_field_groups();
		$options = [];

		if ( ! $field_groups ) {
			return $options;
		}

		foreach ( $field_groups as $group ) {
			if ( empty( $group['key'] ) || empty( $group['title'] ) ) {
				continue;
			}

			// Filter for user fields if requested
			if ( $filter_for_users && ! $this->is_user_field_group( $group ) ) {
				continue;
			}

			$fields = acf_get_fields( $group['key'] );
			if ( ! $fields ) {
				continue;
			}

			$group_options = [];
			foreach ( $fields as $field ) {
				$field_options = $this->process_field_for_options( $field, ! $filter_for_users );
				$group_options = array_merge( $group_options, $field_options );
			}

			if ( ! empty( $group_options ) ) {
				$label_suffix = $filter_for_users ? ' (User Fields)' : '';
				$options[] = [
					'label' => $group['title'] . $label_suffix,
					'options' => $group_options
				];
			}
		}

		return $options;
	}

	}

// Initialize the class to set up the hooks.
new CB_AFC_Integration();
