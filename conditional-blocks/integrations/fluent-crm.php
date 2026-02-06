<?php

class CB_FluentCRM_Integration {
	private $is_fluentcrm_active = false;
	private $is_pro = false;
	private $tested_version = ' 2.9.50';

	public function __construct() {
		$this->is_fluentcrm_active = defined( 'FLUENTCRM' );

		
		add_filter( 'conditional_blocks_register_condition_categories', [ $this, 'register_categories' ], 10, 1 );
		add_filter( 'conditional_blocks_register_condition_types', [ $this, 'register_conditions' ], 10, 1 );
			}

	/**
	 * Register condition categories for the Fluent CRM integration.
	 *
	 * @param array $categories The current list of categories.
	 * @return array The updated list of categories.
	 */
	public function register_categories( $categories ) {
		$categories[] = [ 
			'value' => 'fluentcrm',
			'label' => __( 'Fluent CRM', 'conditional-blocks' ),
			'icon' => plugins_url( 'assets/images/mini-colored/fluentcrm.svg', __DIR__ ),
			'tag' => 'plugin',
		];
		return $categories;
	}

	public function register_conditions( $conditions ) {

		$conditions[] = [ 
			'type' => 'fluentcrm_user_tag',
			'label' => __( 'User Tag', 'conditional-blocks' ),
			'is_pro' => true,
			'tag' => 'plugin',
			'is_disabled' => ! $this->is_fluentcrm_active || ! $this->is_pro,
			'description' => __( 'Check if the current user has specific Fluent CRM tags.', 'conditional-blocks' ),
			'category' => 'fluentcrm',
			'fields' => [ 
				[ 
					'key' => 'tag_relation',
					'type' => 'select',
					'attributes' => [ 
						'label' => __( 'User Relation to Tag', 'conditional-blocks' ),
						'help' => __( 'Check if the current user has or does not have specific Fluent CRM tags, or any tag at all.', 'conditional-blocks' ),
						'searchable' => false,
					],
					'options' => [ 
						[ 'label' => __( 'User has tag(s)', 'conditional-blocks' ), 'value' => 'has_tag' ],
						[ 'label' => __( 'User does not have tag(s)', 'conditional-blocks' ), 'value' => 'does_not_have_tag' ],
					],
				],
				[ 
					'key' => 'fluentcrm_tag',
					'type' => 'select',
					'attributes' => [ 
						'label' => __( 'Fluent CRM Tag', 'conditional-blocks' ),
						'help' => __( 'Select the specific tag for the condition, or select \'(Any Tag)\'.', 'conditional-blocks' ),
						'placeholder' => __( 'Select a tag', 'conditional-blocks' ),
						'searchable' => true,
					],
					'options' => ( $this->is_fluentcrm_active && method_exists( $this, 'get_fluentcrm_tag_options' ) ) ? $this->get_fluentcrm_tag_options() : [],
				],
				[ 
					'key' => 'blockAction',
					'type' => 'blockAction',
				],
			],
		];

		$conditions[] = [ 
			'type' => 'fluentcrm_user_list',
			'label' => __( 'User List', 'conditional-blocks' ),
			'is_pro' => true,
			'tag' => 'plugin',
			'is_disabled' => ! $this->is_fluentcrm_active || ! $this->is_pro,
			'description' => __( 'Check if the current user is on specific Fluent CRM lists.', 'conditional-blocks' ),
			'category' => 'fluentcrm',
			'fields' => [ 
				[ 
					'key' => 'list_relation',
					'type' => 'select',
					'attributes' => [ 
						'label' => __( 'User Relation to List', 'conditional-blocks' ),
						'help' => __( 'Check if the current user is or is not on specific Fluent CRM lists, or any list at all.', 'conditional-blocks' ),
						'searchable' => false,
					],
					'options' => [ 
						[ 'label' => __( 'User is on list(s)', 'conditional-blocks' ), 'value' => 'is_on_list' ],
						[ 'label' => __( 'User is not on list(s)', 'conditional-blocks' ), 'value' => 'is_not_on_list' ],
					],
				],
				[ 
					'key' => 'fluentcrm_list',
					'type' => 'select',
					'attributes' => [ 
						'label' => __( 'Fluent CRM Contact List', 'conditional-blocks' ),
						'help' => __( 'Select the specific list for the condition, or select \'(Any List)\'.', 'conditional-blocks' ),
						'placeholder' => __( 'Select a list', 'conditional-blocks' ),
						'searchable' => true,
					],
					'options' => ( $this->is_fluentcrm_active && method_exists( $this, 'get_fluentcrm_list_options' ) ) ? $this->get_fluentcrm_list_options() : [],
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


new CB_FluentCRM_Integration();