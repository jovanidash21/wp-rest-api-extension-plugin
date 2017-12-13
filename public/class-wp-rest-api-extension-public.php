<?php

class WP_REST_API_Extension_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->wp_rest_api_extension_options = get_option($this->plugin_name);
	}

	/**
	 * Register public styleheets
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-rest-api-extension-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register public scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-rest-api-extension-public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add next and previous post field
	 *
	 * @since 1.0.0
	 */
	public function add_next_prev_post() {
		$args = array(
		  'public' => true,
		);
		$output     = 'objects';
		$post_types = get_post_types( $args, $output );

		$options = get_option( $this->plugin_name );

		foreach ( $post_types as $post_type ) {
			if ( !empty($this->wp_rest_api_extension_options['next-prev-post-' . $post_type->name]) ) {
				self::add_next_post( $post_type->name );
				self::add_prev_post( $post_type->name );
			}
		}
	}

	/**
	 * Add next post field
	 *
	 * @since 1.0.0
	 */
	private function add_next_post($post_type) {
		$add_next_link = function ( $object, $request ) use ( $post_type ) {
			global $post;

			$post_id = $object['id'];
			$post = get_post( $post_id );
			setup_postdata($post);

			if ( !empty(get_next_post()) ) {
				$next_post = get_next_post();
				$next_object = (object) [
					'id'    => $next_post->ID,
					'slug'  => $next_post->post_name,
					'title' => $next_post->post_title,
					'link'  => get_permalink($next_post)
				];
			} else {
				$next_query = new WP_Query( array(
					'post_type'      => $post_type,
					'posts_per_page' => 1,
					'order'          => 'ASC'
				) );
				$next_post = $next_query->the_post();
				$next_object = (object) [
					'id'    => get_the_ID($next_post),
					'slug'  => get_post_field('post_name', $next_post),
					'title' => get_the_title($next_post),
					'link'  => get_permalink($next_post)
				];
			}

			wp_reset_postdata();
			return $next_object;
		};

		register_rest_field( $post_type,
			'next_post',
			array(
				'get_callback'    => $add_next_link,
				'update_callback' => null,
				'schema'          => null,
			)
		);
	}

	/**
	 * Add next post field
	 *
	 * @since 1.0.0
	 */
	private function add_prev_post( $post_type ) {
		$add_prev_link = function ( $object, $request ) use ( $post_type ) {
			global $post;

			$post_id = $object['id'];
			$post = get_post( $post_id );
			setup_postdata($post);

			if ( !empty(get_previous_post()) ) {
				$prev_post = get_previous_post();
				$prev_object = (object) [
					'id'    => $prev_post->ID,
					'slug'  => $prev_post->post_name,
					'title' => $prev_post->post_title,
					'link'  => get_permalink($prev_post)
				];
			} else {
				$prev_query = new WP_Query( array(
					'post_type'      => $post_type,
					'posts_per_page' => 1,
					'order'          => 'DESC'
				) );
				$prev_post = $prev_query->the_post();
				$prev_object = (object) [
					'id'    => get_the_ID($prev_post),
					'slug'  => get_post_field('post_name', $prev_post),
					'title' => get_the_title($prev_post),
					'link'  => get_permalink($prev_post)
				];
			}

			wp_reset_postdata();
			return $prev_object;
		};

		register_rest_field( $post_type,
			'prev_post',
			array(
				'get_callback'    => $add_prev_link,
				'update_callback' => null,
				'schema'          => null,
			)
		);
	}
}
