<?php

define( 'SITEORIGIN_WIDGETS_ICONS', true );

function siteorigin_widgets_icon_families_filter( $families ){
	$bundled = array(
		'elegantline' => __( 'Elegant Themes Line Icons', 'seed' ),
		'fontawesome' => __( 'Font Awesome', 'seed' ),
		'genericons' => __( 'Genericons', 'seed' ),
		'icomoon' => __( 'Icomoon Free', 'seed' ),
		'typicons' => __( 'Typicons', 'seed' ),
		'ionicons' => __( 'Ionicons', 'seed' ),
	);

	foreach ( $bundled as $font => $name) {
		include_once plugin_dir_path(__FILE__) . $font . '/filter.php';
		$families[$font] = array(
			'name' => $name,
			'style_uri' => theme_dir_url(__FILE__) . $font . '/style.css',
			'icons' => apply_filters('siteorigin_widgets_icons_' . $font, array() ),
		);
	}

	return $families;
}
add_filter( 'siteorigin_widgets_icon_families', 'siteorigin_widgets_icon_families_filter' );