<?php
$menus = get_registered_nav_menus();

$args       = array(
  'public'   => true,
  '_builtin' => false,
);
$output     = 'objects';
$operator   = 'and';
$post_types = get_post_types( $args, $output, $operator );

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
              <h4><?php echo 'Nav menus'; ?></h4>

              <?php foreach ( $menus as $location => $description ) : ?>
                <fieldset>
                  <legend class="screen-reader-text"><span><?php _e('Add Registered nav Menu API', $this->plugin_name);?></span></legend>
                  <label for="<?php echo $this->plugin_name; ?>-registered-nav-menu-<?php echo $location ?>">
                    <input type="checkbox" id="<?php echo $this->plugin_name; ?>-registered-nav-menu-<?php echo $location ?>" name="<?php echo $this->plugin_name; ?>[registered-nav-menu-<?php echo $location ?>]" value="1" <?php checked( $options['registered-nav-menu-' . $location], 1 ); ?>/>
                      <span><?php esc_attr_e($description, $this->plugin_name); ?></span>
                  </label>
                </fieldset>
              <?php endforeach;  ?>
            </div>

            <h2 class="hndle"><?php echo 'Add Next And Previous Links'; ?></h2>
            <div class="inside">
              <h4><?php echo 'Post types'; ?></h4>

              <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Add Next And Previous Links', $this->plugin_name);?></span></legend>
                <label for="<?php echo $this->plugin_name; ?>-next-prev-links-page">
                  <input type="checkbox" id="<?php echo $this->plugin_name; ?>-next-prev-links-page" name="<?php echo $this->plugin_name; ?>[next-prev-links-page]" value="1" <?php checked( $options['next-prev-links-page'], 1 ); ?>/>
                    <span><?php esc_attr_e('Page', $this->plugin_name); ?></span>
                </label>
              </fieldset>
              <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Add Next And Previous Links', $this->plugin_name);?></span></legend>
                <label for="<?php echo $this->plugin_name; ?>-next-prev-links-post">
                  <input type="checkbox" id="<?php echo $this->plugin_name; ?>-next-prev-links-post" name="<?php echo $this->plugin_name; ?>[next-prev-links-post]" value="1" <?php checked( $options['next-prev-links-post'], 1 ); ?>/>
                    <span><?php esc_attr_e('Post', $this->plugin_name); ?></span>
                </label>
              </fieldset>
              <?php if ( ! empty( $post_types ) ) : ?>
                <?php foreach ( $post_types as $post_type ) : ?>
                  <fieldset>
                    <legend class="screen-reader-text"><span><?php _e('Add Next And Previous Links', $this->plugin_name);?></span></legend>
                    <label for="<?php echo $this->plugin_name; ?>-next-prev-links-<?php echo $post_type->name ?>">
                      <input type="checkbox" id="<?php echo $this->plugin_name; ?>-next-prev-links-<?php echo $post_type->name ?>" name="<?php echo $this->plugin_name; ?>[next-prev-links-<?php echo $post_type->name ?>]" value="1" <?php checked( $options['next-prev-links-' . $post_type->name], 1 ); ?>/>
                        <span><?php esc_attr_e($post_type->label, $this->plugin_name); ?></span>
                    </label>
                  </fieldset>
                <?php endforeach; ?>
              <?php endif;  ?>
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
