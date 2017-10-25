<?php

class Auto_CRED {
	
	/**
	*
	* The constructor
	*
	**/
	
	public function __construct() {
		
		$this->field_atts = get_option( 'wpcf-fields' );
		
		add_shortcode( 'auto_cred' , array( $this , 'generate_smart_cred' ) );
		
		add_filter( 'auto_cred_class' , array( $this , 'set_auto_cred_class' ) , 10 , 2 );
		
		add_filter( 'auto_cred_label' , array( $this , 'set_auto_cred_label' ) , 10 , 3 );
		
		add_filter( 'auto_cred_after_field' , array( $this , 'add_field_description_after' ) , 10 , 2 );
		
		add_filter( 'auto_cred_before_field' , array( $this , 'add_field_description_before' ) , 10 , 2 );
		
	}
	
	
	/**
	*
	* Generate the smart cred form
	*
	**/
	
	public function generate_smart_cred( $atts ) {
		
		$atts = shortcode_atts(
			array( 'read-only' => false ),
			$atts
		);
		
		$force_label = false;
		
		if( $atts['read-only'] == 'true' ) {
			$force_label = true;
		}
		
		ob_start(); ?>

			<?php foreach( $this->get_all_fields() as $group_id => $field_array ) { ?>

				<?php foreach( explode( ',' , $field_array ) as $field_name ) { ?>

					<?php if( $field_name === '' ) { continue; } ?>

					<div class="form-group">
						
						<?php echo apply_filters( 'auto_cred_label' , '' , $field_name , $force_label ); ?>
						
						<?php echo apply_filters( 'auto_cred_before_field' , '' , $field_name ); ?>
						
						<?php if( $atts['read-only'] != 'true' ) { ?>
						
						[cred_field 
							field='<?php echo $field_name; ?>' 
							post='contractor-agreement' 
							value='' 
							urlparam='' 
							class='<?php echo implode( ' ' , apply_filters( 'auto_cred_class' , array() , $field_name ) ); ?>'
							output='bootstrap'
						]	
						
						<?php } else { ?>

							[types field='<?php echo $field_name; ?>' separator=', '][/types]
						
						<?php } ?>
						
						<?php echo apply_filters( 'auto_cred_after_field' , '' , $field_name ); ?>
						
					</div>

				<?php } ?>

			<?php } ?>

		<?php
		$html = ob_get_contents();
		if( $html ) { ob_end_clean(); }
		return $html;
		
	}
	
	
	/**
	*
	* Get field attribures
	*
	**/
	
	private function get_field_atts( $field ) {
		
		return $this->field_atts[ $field ];
		
	}
	
	
	/**
	*
	* Get all the fields for this post
	*
	**/
	
	public function get_all_fields() {
		
		$field_groups = $this->get_all_field_groups();
		
		foreach( $field_groups as $field_group ) {
			
			$fields[ $field_group->ID ] = get_post_meta( $field_group->ID , '_wp_types_group_fields' , true );
			
		}
		
		return $fields;
		
	}
	
	
	/**
	*
	* Get all the field groups
	*
	**/
	
	public function get_all_field_groups() {
		
		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'wp-types-group',
			'meta_query' => array(
				'relation' => 'OR',
			),
		);
		
		foreach( $this->get_object_terms() as $term ) {
			
			$args['meta_query'][] = array(
				'key' => '_wp_types_group_terms',
				'value' => ',' . $term->term_id . ',',
				'compare' => 'LIKE',
			);
			
		}
		
		return get_posts( $args );
		
	}
	
	
	/**
	*
	* Get the current object's taxonomies
	*
	**/
	
	private function get_object_taxonomies() {
		
		global $post;
		return get_object_taxonomies( get_post_type( $post ) );
		
	}
	
	
	/**
	*
	* Get the current object's terms
	*
	**/
	
	private function get_object_terms() {
		
		$taxonomies = $this->get_object_taxonomies();
		return wp_get_object_terms( get_the_ID() , $taxonomies );
		
	}
	
	
	/**
	*
	* Set the cred shortcode class
	*
	**/
	
	public function set_auto_cred_class( $class , $field_name ) {
		
		$field_atts = $this->get_field_atts( $field_name );
		
		switch( $field_atts['type'] ) {
			
			case 'checkboxes':
			case 'checkbox':
				break;
				
			default:
				$class[] = 'form-control';
				break;
			
		}
		
		return $class;
		
	}
	
	
	/**
	*
	* Set the cred shortcode class
	*
	**/
	
	public function set_auto_cred_label( $label , $field_name , $force ) {
		
		$field_atts = $this->get_field_atts( $field_name );
		
		switch( $field_atts['type'] ) {
			
			case 'checkboxes':
			case 'checkbox':
				break;
				
			default:
				$label = '<label>' . $field_atts['name'] . '</label>';
				break;
			
		}
		
		if( $force ) {
			$label = '<label>' . $field_atts['name'] . ': </label>';
		}
		
		return $label;
		
	}
	
	
	/**
	*
	* Add field descriptions
	*
	**/
	
	public function add_field_description_before( $description , $field_name ) {
		
		$field_details = $this->field_atts[ $field_name ];
		
		if( $field_details['type'] === 'checkbox' || $field_details['type'] === 'checkbox' ) {
			return;
		}
		
		if( ! isset( $field_details['description'] ) || $field_details['description'] === '' ) {
			return $description;
		}
		
		return '<br /><small>' . $field_details['description'] . '</small>';
		
	}
	
	
	/**
	*
	* Add field descriptions
	*
	**/
	
	public function add_field_description_after( $description , $field_name ) {
		
		$field_details = $this->field_atts[ $field_name ];
		
		if( $field_details['type'] !== 'checkbox' && $field_details['type'] !== 'checkbox' ) {
			return;
		}
		
		if( ! isset( $field_details['description'] ) || $field_details['description'] === '' ) {
			return $description;
		}
		
		return '<small>' . $field_details['description'] . '</small>';
		
	}
	
	
}

new Auto_CRED();