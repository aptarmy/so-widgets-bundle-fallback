# so-widgets-bundle-fallback
Fallback tool if users haven't yet install so-widgets-bundle. This is intended to be used in WordPress theme development.
We get this code from so-widget-bundle plugin and filter out all unecessary action and fix file path as follows.
- remove set_plugin_textdomain action
- remove plugin_version_check action
- remove admin_menu_init action
- remove siteorigin_widgets_version_update action
- remove handle_update action
- replace 'so-widgets-bundle' with $theme_text_domain
- replace 'SOW_BUNDLE_VERSION' with 'SOW_FALLBACK_BUNDLE_VERSION'
- replace 'SOW_BUNDLE_BASE_FILE' with 'SOW_FALLBACK_BUNDLE_BASE_FILE'
- replace 'SiteOrigin_Widgets_Bundle' with 'SiteOrigin_Fallback_Widgets_Bundle'
- change $widget_folders to empty array()
- change $default_active_widgets to empty array()
- remove 'widgets' folder
- replace 'plugin_dir_url(__FILE__)' in 'so-widgets-bundle-fallback/icons/icons.php' to 'get_template_directory_uri() . "/inc/so-widgets-bundle-fallback/icons/"'
- replace 'plugin_dir_url(SOW_FALLBACK_BUNDLE_BASE_FILE)' in 'so-widgets-bundle-fallback/icons/icons.php' to 'get_template_directory_uri() . "/inc/so-widgets-bundle-fallback/"'
- replace 'plugin_dir_url(__FILE__)' in 'so-widgets-bundle-fallback/base/inc/fields/media.class.php' to 'get_template_directory_uri() . "/inc/so-widgets-bundle-fallback/base/inc/fields/"'
- replace 'plugin_dir_url(__FILE__)' in 'so-widgets-bundle-fallback/base/inc/actions.php' to "get_template_directory_uri() . '/inc/so-widgets-bundle-fallback/base/inc/'"

## To include this fallback in your WordPress theme
```
function theme_so_plugin_fallbacak () {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( !is_plugin_active( 'so-widgets-bundle/so-widgets-bundle.php' ) ) {
		require get_template_directory() . '/inc/so-widgets-bundle-fallback/so-widgets-bundle-fallback.php';
	}
}
add_action('after_setup_theme', 'theme_so_plugin_fallbacak', 1);
```