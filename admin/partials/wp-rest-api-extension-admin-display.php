<div class="wrap">
  <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-1">
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox">
          <form method="post" name="wp_rest_api_extension_settings" action="options.php">
            

            <div class="inside">
              <?php submit_button('Save all changes', 'primary','submit', TRUE); ?>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
