<?php
class CB_WPFusion_Integration {
	private $is_wp_fusion_active = false;
	private $is_pro = false;
	private $tested_version = '3.45.2.2';

	public function __construct() {
		$this->is_wp_fusion_active = function_exists( 'wp_fusion' );
		
		add_filter( 'conditional_blocks_register_condition_categories', [ $this, 'register_categories' ], 10, 1 );
		add_filter( 'conditional_blocks_register_condition_types', [ $this, 'register_conditions' ], 10, 1 );
			}

	/**
	 * Register condition categories for the WP Fusion integration.
	 *
	 * @param array $categories The list of available categories.
	 * @return array The updated list of categories.
	 */
	public function register_categories( $categories ) {
		$categories[] = [ 
			'value' => 'wp_fusion',
			'label' => __( 'WP Fusion', 'conditional-blocks' ),
			'icon' => plugins_url( 'assets/images/mini-colored/wp-fusion.svg', __DIR__ ),
			'tag' => 'plugin',
		];
		return $categories;
	}

	/**
	 * Register condition types for the WP Fusion integration.
	 *
	 * @param array $conditions The list of available condition types.
	 * @return array The updated list of condition types.
	 */
	public function register_conditions( $conditions ) {

		$conditions[] = [ 
			'type' => 'wp_fusion_user_tag',
			'label' => __( 'User Tag', 'conditional-blocks' ),
			'is_pro' => true,
			'tag' => 'plugin',
			'is_disabled' => ! $this->is_wp_fusion_active || ! $this->is_pro,
			'description' => '',
			'category' => 'wp_fusion',
			'fields' => [ 

				[ 
					'key' => 'tag_relation',
					'type' => 'select',
					'attributes' => [ 
						'label' => __( 'User Relation to Tag', 'conditional-blocks' ),
						'help' => __( 'Check if the current user has or does not have specific WP Fusion tags, or any tag at all.', 'conditional-blocks' ),
						'searchable' => false,
					],
					'options' => [ 
						[ 'label' => __( 'User has tag(s)', 'conditional-blocks' ), 'value' => 'has_tag' ],
						[ 'label' => __( 'User does not have tag(s)', 'conditional-blocks' ), 'value' => 'does_not_have_tag' ], // Means user has zero tags
					],
				],
				[ 
					'key' => 'wp_fusion_tag',
					'type' => 'select',
					'attributes' => [ 
						'label' => __( 'WP Fusion Tag', 'conditional-blocks' ),
						'help' => __( 'Select the specific tag for the condition, or select \'(Any Tag)\'.', 'conditional-blocks' ),
						'placeholder' => __( 'Select a tag', 'conditional-blocks' ),
						'searchable' => true,
					],
					'options' => ( $this->is_wp_fusion_active && method_exists( $this, 'get_wp_fusion_tag_options' ) ) ? $this->get_wp_fusion_tag_options() : [],
				],
				[ 
					'key' => 'blockAction',
					'type' => 'blockAction',
				],
			],
		];

		return $conditions;
	}

	}

// Initialize the class to set up the hooks.
new CB_WPFusion_Integration();