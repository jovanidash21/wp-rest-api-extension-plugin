<?php

class WP_REST_API_Extension_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name                   = $plugin_name;
		$this->version                       = $version;
		$this->wp_rest_api_extension_options = get_option($this->plugin_name);
		$this->api_namespace                 = 'wp/v2';
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
	 * Add registered nav menu REST API route
	 *
	 * @since 1.0.0
	 */
	public function add_registered_nav_menu_api() {
		$registered_nav_menu_api = function ( $request ) {
			$params    = $request->get_params();
			$location  = $params['location'];
			$locations = get_nav_menu_locations();

			if ( !isset( $locations[ $location ] ) ) {
				return array();
			}

			$menu               = wp_get_nav_menu_object( $locations[ $location ] );
			$menu_items         = wp_get_nav_menu_items( $menu->term_id );
			$reverse_menu_items = array_reverse( $menu_items );
			$reverse_menu       = array();
			$children           = array();

			foreach ( $reverse_menu_items as $item ) {
				$parse_url = parse_url($item->url);

				if ( $parse_url['path'] !== '/' ) {
					$path = rtrim($parse_url['path'], '/');
				} else {
					$path = $parse_url['path'];
				}

				$menu_item = array(
					'ID'          => abs( $item->ID ),
					'guid'        => $item->guid,
					'order'       => (int)$item->menu_order,
					'parent'      => abs($item->menu_item_parent),
					'object_id'   => abs($item->object_id),
					'object'      => $item->object,
					'type'        => $item->type,
					'type_label'  => $item->type_label,
					'url'         => $item->url,
					'path'        => $path,
					'title'       => $item->title,
					'attr_title'  => $item->attr_title,
					'target'      => $item->target,
					'description' => $item->description,
					'classes'     => implode( ' ', $item->classes ),
					'xfn'         => $item->xfn,
					'children'    => array(),
				);

				if ( array_key_exists($item->ID , $children) ) {
					$menu_item['children'] = array_reverse($children[$item->ID]);
				}

				if ( $item->menu_item_parent != 0 ) {
					if ( array_key_exists($item->menu_item_parent , $children) ) {
						array_push( $children[$item->menu_item_parent], $menu_item );
					} else {
						$children[$item->menu_item_parent] = array($menu_item, );
					}
				} else {
					array_push($reverse_menu, $menu_item);
				}

				$menu_item = apply_filters( 'rest_menus_format_menu_item', $menu_item );
			}

			return array_reverse($reverse_menu);
		};

		if ( !empty($this->wp_rest_api_extension_options['enable-registered-nav-menu-api-route']) ) {
			register_rest_route( $this->api_namespace, '/menus/(?P<location>[a-zA-Z0-9_-]+)', array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => $registered_nav_menu_api
				)
			) );
		}
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
				$parse_url = parse_url(get_permalink($next_post));

				if ( $parse_url['path'] !== '/' ) {
					$path = rtrim($parse_url['path'], '/');
				} else {
					$path = $parse_url['path'];
				}

				$next_object = (object) [
					'id'    => $next_post->ID,
					'slug'  => $next_post->post_name,
					'title' => $next_post->post_title,
					'url'   => get_permalink($next_post),
					'path'  => $path
				];
			} else {
				$next_query = new WP_Query( array(
					'post_type'      => $post_type,
					'posts_per_page' => 1,
					'order'          => 'ASC'
				) );
				$next_post = $next_query->the_post();
				$parse_url = parse_url(get_permalink($next_post));

				if ( $parse_url['path'] !== '/' ) {
					$path = rtrim($parse_url['path'], '/');
				} else {
					$path = $parse_url['path'];
				}

				$next_object = (object) [
					'id'    => get_the_ID($next_post),
					'slug'  => get_post_field('post_name', $next_post),
					'title' => get_the_title($next_post),
					'url'   => get_permalink($next_post),
					'path'  => $path
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
				$parse_url = parse_url(get_permalink($prev_post));

				if ( $parse_url['path'] !== '/' ) {
					$path = rtrim($parse_url['path'], '/');
				} else {
					$path = $parse_url['path'];
				}

				$prev_object = (object) [
					'id'    => $prev_post->ID,
					'slug'  => $prev_post->post_name,
					'title' => $prev_post->post_title,
					'url'   => get_permalink($prev_post),
					'path'  => $path
				];
			} else {
				$prev_query = new WP_Query( array(
					'post_type'      => $post_type,
					'posts_per_page' => 1,
					'order'          => 'DESC'
				) );
				$prev_post = $prev_query->the_post();
				$parse_url = parse_url(get_permalink($prev_post));

				if ( $parse_url['path'] !== '/' ) {
					$path = rtrim($parse_url['path'], '/');
				} else {
					$path = $parse_url['path'];
				}

				$prev_object = (object) [
					'id'    => get_the_ID($prev_post),
					'slug'  => get_post_field('post_name', $prev_post),
					'title' => get_the_title($prev_post),
					'url'   => get_permalink($prev_post),
					'path'  => $path
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
