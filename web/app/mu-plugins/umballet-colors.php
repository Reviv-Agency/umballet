<?php
/**
 * Plugin Name: Umballet — Brand Colours
 * Description: Seeds this site's AEW (aew-*) Elementor global colours. Edit the palette to recolour the site (or use Elementor → Site Settings → Global Colours).
 */

defined('ABSPATH') || exit;

add_action('admin_init', function () {
	if (get_option('umballet_colors_seeded') === '1') {
		return;
	}
	$palette = [
		'aew-cta' => ['Main CTA', '#632B3A'],
		'aew-cta-hover' => ['CTA Hover', '#89505F'],
		'aew-background' => ['Background', '#F1EADF'],
		'aew-text' => ['Text', '#1B150E'],
		'aew-cards' => ['Cards', '#FFFFFF'],
		'aew-lines' => ['Lines & Accents', '#C2BAAB'],
		'aew-secondary-bg' => ['Headers (H1-H2)', '#876137'],
		'aew-secondary-accent' => ['Secondary Accent', '#3E382F'],
		'aew-misc-accent' => ['Misc Accent', '#3E382F'],
		'aew-secondary-cards' => ['Secondary Cards', '#C2BAAB'],
		'aew-gold-light' => ['Gold Light', '#876137'],
	];
	$kid = (int) get_option('elementor_active_kit');
	if (!$kid) {
		return; // Elementor not ready yet — retries on the next admin load.
	}
	$settings = get_post_meta($kid, '_elementor_page_settings', true);
	if (!is_array($settings)) {
		$settings = [];
	}
	$custom = (isset($settings['custom_colors']) && is_array($settings['custom_colors'])) ? $settings['custom_colors'] : [];
	$existing = [];
	foreach ($custom as $c) {
		if (isset($c['_id'])) {
			$existing[$c['_id']] = true;
		}
	}
	$added = false;
	foreach ($palette as $id => $def) {
		if (isset($existing[$id])) {
			continue;
		}
		$custom[] = ['_id' => $id, 'title' => $def[0], 'color' => $def[1]];
		$added = true;
	}
	if ($added) {
		$settings['custom_colors'] = $custom;
		update_post_meta($kid, '_elementor_page_settings', $settings);
		if (class_exists('\Elementor\Core\Files\CSS\Post')) {
			try {
				\Elementor\Core\Files\CSS\Post::create($kid)->update();
			} catch (\Throwable) {
			}
		}
	}
	update_option('umballet_colors_seeded', '1');
});
