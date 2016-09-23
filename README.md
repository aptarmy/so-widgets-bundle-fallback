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
- remove unnecessary folders, we need only 'beas' and 'icon' folder
- replace 'plugin_dir_url' with 'theme_dir_url' recursively

## To include this fallback in your WordPress theme
```
function apt_so_widget_framework_fallback () {
	if ( !class_exists('SiteOrigin_Widgets_Bundle') ) {
		require get_template_directory() . '/inc/widget_framework/widget_framework.php';
	}
	require get_template_directory() . '/inc/apt_widgets/apt_widgets.php';
}
add_action('after_setup_theme', 'apt_so_widget_framework_fallback', 1);
```