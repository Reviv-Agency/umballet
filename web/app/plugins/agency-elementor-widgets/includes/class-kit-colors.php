<?php
/**
 * Seeds the plugin's brand-neutral global colour palette into the active
 * Elementor kit so every widget can resolve `var(--e-global-color-aew-*)`.
 *
 * The widgets' CSS map each palette token to one of these globals with a hex
 * fallback (e.g. `--aew-cta: var(--e-global-color-aew-cta, #632B3A)`). Seeding
 * is idempotent and additive: existing kit colours are never overwritten, so a
 * site owner can recolour the whole widget set from
 * Elementor → Site Settings → Global Colours.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

final class Kit_Colors {

	/** Option flag so seeding runs once per palette version. */
	private const OPTION_SEEDED = 'aew_kit_colors_seeded';

	/** Bump when the palette set below changes to re-run seeding. */
	private const VERSION = '1';

	/**
	 * The universal palette: global id => [ title, default hex ].
	 * Ids match the `--e-global-color-<id>` references in the widget CSS.
	 *
	 * @return array<string, array{0:string,1:string}>
	 */
	public static function palette(): array {
		return [
			'aew-cta'              => [ 'Main CTA', '#632B3A' ],
			'aew-cta-hover'       => [ 'CTA Hover', '#89505F' ],
			'aew-background'      => [ 'Background', '#F1EADF' ],
			'aew-text'            => [ 'Text', '#1B150E' ],
			'aew-cards'           => [ 'Cards', '#FFFFFF' ],
			'aew-lines'           => [ 'Lines & Accents', '#C2BAAB' ],
			'aew-secondary-bg'    => [ 'Headers (H1-H2)', '#876137' ],
			'aew-secondary-accent' => [ 'Secondary Accent', '#3E382F' ],
			'aew-misc-accent'     => [ 'Misc Accent', '#3E382F' ],
			'aew-secondary-cards' => [ 'Secondary Cards', '#C2BAAB' ],
			'aew-gold-light'      => [ 'Gold Light', '#876137' ],
		];
	}

	public static function init(): void {
		add_action( 'admin_init', [ self::class, 'maybe_seed' ] );
	}

	/** Self-heal: seed once (per version) whenever an admin loads. */
	public static function maybe_seed(): void {
		if ( get_option( self::OPTION_SEEDED ) === self::VERSION ) {
			return;
		}
		self::seed();
	}

	/**
	 * Add any missing aew-* global colours to the active kit. Idempotent;
	 * never overwrites a colour the site owner has already set.
	 */
	public static function seed(): void {
		$kid = (int) get_option( 'elementor_active_kit' );
		if ( ! $kid ) {
			return; // Elementor not ready yet — maybe_seed() retries next admin load.
		}

		$settings = get_post_meta( $kid, '_elementor_page_settings', true );
		if ( ! is_array( $settings ) ) {
			$settings = [];
		}
		$custom = ( isset( $settings['custom_colors'] ) && is_array( $settings['custom_colors'] ) )
			? $settings['custom_colors']
			: [];

		$existing = [];
		foreach ( $custom as $c ) {
			if ( isset( $c['_id'] ) ) {
				$existing[ $c['_id'] ] = true;
			}
		}

		$added = false;
		foreach ( self::palette() as $id => $def ) {
			if ( isset( $existing[ $id ] ) ) {
				continue;
			}
			$custom[] = [
				'_id'   => $id,
				'title' => $def[0],
				'color' => $def[1],
			];
			$added = true;
		}

		if ( $added ) {
			$settings['custom_colors'] = $custom;
			update_post_meta( $kid, '_elementor_page_settings', $settings );
			self::regenerate_kit_css( $kid );
		}

		update_option( self::OPTION_SEEDED, self::VERSION );
	}

	private static function regenerate_kit_css( int $kid ): void {
		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			try {
				\Elementor\Core\Files\CSS\Post::create( $kid )->update();
			} catch ( \Throwable ) {
				// Ignore — kit CSS regenerates lazily on the next front-end render.
			}
		}
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			try {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			} catch ( \Throwable ) {
				// Non-fatal.
			}
		}
	}
}
