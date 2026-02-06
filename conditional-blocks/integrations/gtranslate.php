<?php
class CB_GTranslate_Integration {
	private $is_gtranslate_active = false;
	private $is_pro = false;
	private $tested_version = '3.0.7';

	public function __construct() {
		$this->is_gtranslate_active = class_exists( 'GTranslate' );

		add_filter( 'conditional_blocks_register_condition_categories', [ $this, 'register_categories' ], 10, 1 );
		add_filter( 'conditional_blocks_register_condition_types', [ $this, 'register_conditions' ], 10, 1 );
			}

	public function register_categories( $categories ) {
		$categories[] = [
			'value' => 'gtranslate',
			'label' => __( 'GTranslate', 'conditional-blocks' ),
			'icon' => plugins_url( 'assets/images/mini-colored/gtranslate.svg', __DIR__ ),
			'tag' => 'plugin',
		];
		return $categories;
	}

	public function register_conditions( $conditions ) {

		$conditions[] = [
			'type' => 'gtranslate_language',
			'label' => __( 'Current Language', 'conditional-blocks' ),
			'is_pro' => true,
			'is_disabled' => ! $this->is_gtranslate_active || ! $this->is_pro,
			'description' => __( 'Check if the current language matches the selected language.', 'conditional-blocks' ),
			'category' => 'gtranslate',
			'fields' => [
				[
					'key' => 'language',
					'type' => 'select',
					'attributes' => [
						'label' => __( 'Language', 'conditional-blocks' ),
						'help' => __( 'Select the language to check against', 'conditional-blocks' ),
						'placeholder' => __( 'Select a language', 'conditional-blocks' ),
						'searchable' => true
					],
					'options' => function_exists( 'conditional_blocks_get_language_codes' ) ? $this->get_language_options() : []
				],
				[
					'key' => 'blockAction',
					'type' => 'blockAction',
				],
			],
		];

		return $conditions;
	}

	private function get_language_options() {
		$languages = conditional_blocks_get_language_codes();

		return array_map( function ( $code, $language ) {
			return [ 'label' => strtoupper( $code ) . ' - ' . $language, 'value' => $code ];
		}, array_keys( $languages ), $languages );
	}

	

}

new CB_GTranslate_Integration();