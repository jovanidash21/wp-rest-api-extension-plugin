<?php
$args = array(
  'public' => true,
);
$output     = 'objects';
$post_types = get_post_types( $args, $output );

$options = get_option($this->plugin_name);
?>

<div class="wrap">
  <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-1">
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox">
          <form method="post" name="wp-rest-api-extension_options" action="options.php">
            <?php
              settings_fields($this->plugin_name);
              do_settings_sections($this->plugin_name);
            ?>

            <h2 class="hndle"><?php echo 'Add Registered Nav Menu API'; ?></h2>
            <div class="inside">
              <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Enable Registered Nav Menu API Route', $this->plugin_name);?></span></legend>
                <label for="<?php echo $this->plugin_name; ?>-enable-registered-nav-menu-api-route">
                  <input type="checkbox" id="<?php echo $this->plugin_name; ?>-enable-registered-nav-menu-api-route" name="<?php echo $this->plugin_name; ?>[enable-registered-nav-menu-api-route]" value="1"
                    <?php
                      $registered_nav_menu_option = $options['enable-registered-nav-menu-api-route'];
                      isset($registered_nav_menu_option) ? checked($registered_nav_menu_option, 1) : '';
                    ?>
                  />
                  <span><?php _e('Enable Registered Nav Menu API Route', $this->plugin_name);?></span>
                </label>
              </fieldset>
            </div>

            <h2 class="hndle"><?php echo 'Add Next And Previous Post'; ?></h2>
            <div class="inside">
              <h4><?php echo 'Post types'; ?></h4>

              <?php if ( ! empty( $post_types ) ) : ?>
                <?php foreach ( $post_types as $post_type ) : ?>
                  <?php
                    if (
                      ($post_type->name != 'page') &&
                      ($post_type->name != 'attachment')
                    ) :
                  ?>
                    <fieldset>
                      <legend class="screen-reader-text"><span><?php _e('Add Next And Previous Post', $this->plugin_name);?></span></legend>
                      <label for="<?php echo $this->plugin_name; ?>-next-prev-post-<?php echo $post_type->name ?>">
                        <input type="checkbox" id="<?php echo $this->plugin_name; ?>-next-prev-post-<?php echo $post_type->name ?>" name="<?php echo $this->plugin_name; ?>[next-prev-post-<?php echo $post_type->name ?>]" value="1"
                          <?php
                            $next_prev_post_option = $options['next-prev-post-' . $post_type->name];
                            isset($next_prev_post_option) ? checked($next_prev_post_option) : '';
                          ?>
                        />
                        <span><?php esc_attr_e($post_type->label, $this->plugin_name); ?></span>
                      </label>
                    </fieldset>
                  <?php
                    else :
                      continue;
                    endif;
                  ?>
                <?php endforeach; ?>
              <?php endif;  ?>
              <p>
                <strong>Note:&nbsp;</strong>Make sure the Post Type/s has&nbsp;
                <a href="https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-rest-api-support-for-custom-content-types/" target="_blank">
                  REST API support
                </a>
                &nbsp;for your theme.
              </p>
            </div>

            <div class="inside">
              <?php submit_button(__('Save settings', $this->plugin_name), 'primary','submit', TRUE); ?>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
