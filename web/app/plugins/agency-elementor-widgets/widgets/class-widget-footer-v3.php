<?php
/**
 * Footer V3 Elementor widget — multi-column "headed link groups" footer.
 *
 * A dark footer of N columns; each column stacks several heading + divider +
 * link-list groups (e.g. "Tickets & Performances", "Company", "Support"), plus
 * an optional Contact block (address / email / phone + social icons). Built to
 * replicate the Utah Metropolitan Ballet Wix footer; generic enough for any
 * multi-group link footer.
 *
 * Brand-free: every colour reads an `aew-*` Elementor global with a neutral-grey
 * fallback; render() resolves global-bound picks via Color_Vars (§6.8 / #19).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Multi-column link footer with headed groups + contact block.
 */
class Widget_Footer_V3 extends Widget_Base {

	private const ASSET_SLUG = 'footer-v3';

	public function get_name(): string {
		return 'agency-footer-v3';
	}

	public function get_title(): string {
		return esc_html__( 'Footer V3', 'agency-elementor-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-footer';
	}

	public function get_categories(): array {
		return [ 'agency-widgets' ];
	}

	public function get_keywords(): array {
		return [ 'footer', 'links', 'columns', 'contact', 'sitemap' ];
	}

	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point _padding to the inner wrapper; defaults EMPTY (guide §5 / #16).
	 *
	 * @param bool $with_common_controls Whether common controls are included.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-ftv3__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	protected function register_controls(): void {
		$this->controls_groups();
		$this->controls_contact();
		$this->controls_layout();
		$this->style_body();
		$this->style_headings();
		$this->style_links();
		$this->style_contact();
	}

	/**
	 * CONTENT — the headed link groups (each assigned to a column).
	 *
	 * @return void
	 */
	private function controls_groups(): void {
		$this->start_controls_section( 's_groups', [ 'label' => esc_html__( 'Link groups', 'agency-elementor-widgets' ) ] );

		$rep = new Repeater();
		$rep->add_control( 'heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Heading', 'agency-elementor-widgets' ) ] );
		$rep->add_control( 'column', [ 'label' => esc_html__( 'Column', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SELECT, 'default' => '1', 'options' => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ] ] );
		$rep->add_control( 'links', [
			'label'       => esc_html__( 'Links', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 8,
			'default'     => '',
			'description' => esc_html__( 'One per line as "Label|/url" (or just "Label"). Leave a blank line to add a small gap between sub-groups.', 'agency-elementor-widgets' ),
		] );
		$rep->add_control( 'show_divider', [ 'label' => esc_html__( 'Divider under heading', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );

		$this->add_control( 'groups', [
			'label'       => esc_html__( 'Groups', 'agency-elementor-widgets' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'default'     => [],
			'title_field' => '{{{ heading }}} (col {{{ column }}})',
		] );

		$this->end_controls_section();
	}

	/**
	 * CONTENT — the Contact block (address / email / phone + social).
	 *
	 * @return void
	 */
	private function controls_contact(): void {
		$this->start_controls_section( 's_contact', [ 'label' => esc_html__( 'Contact block', 'agency-elementor-widgets' ) ] );

		$this->add_control( 'show_contact', [ 'label' => esc_html__( 'Show', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
		$this->add_control( 'contact_heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'CONTACT', 'agency-elementor-widgets' ), 'condition' => [ 'show_contact' => 'yes' ] ] );
		$this->add_control( 'contact_column', [ 'label' => esc_html__( 'Column', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SELECT, 'default' => '3', 'options' => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ], 'condition' => [ 'show_contact' => 'yes' ] ] );
		$this->add_control( 'contact_address', [ 'label' => esc_html__( 'Address', 'agency-elementor-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => [ 'show_contact' => 'yes' ] ] );
		$this->add_control( 'contact_email', [ 'label' => esc_html__( 'Email', 'agency-elementor-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => [ 'show_contact' => 'yes' ] ] );
		$this->add_control( 'contact_phone', [ 'label' => esc_html__( 'Phone', 'agency-elementor-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => [ 'show_contact' => 'yes' ] ] );

		$srep = new Repeater();
		$srep->add_control( 'platform', [
			'label'   => esc_html__( 'Icon', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'instagram',
			'options' => [
				'instagram' => 'Instagram',
				'facebook'  => 'Facebook',
				'tiktok'    => 'TikTok',
				'youtube'   => 'YouTube',
				'x'         => 'X / Twitter',
			],
			'description' => esc_html__( 'Built-in glyph — recolourable below. Or override with a custom image.', 'agency-elementor-widgets' ),
		] );
		$srep->add_control( 'icon', [ 'label' => esc_html__( 'Custom icon (optional)', 'agency-elementor-widgets' ), 'type' => Controls_Manager::MEDIA, 'description' => esc_html__( 'A raster image here can\'t be recoloured.', 'agency-elementor-widgets' ) ] );
		$srep->add_control( 'link', [ 'label' => esc_html__( 'Link', 'agency-elementor-widgets' ), 'type' => Controls_Manager::URL ] );
		$srep->add_control( 'label', [ 'label' => esc_html__( 'Aria label', 'agency-elementor-widgets' ), 'type' => Controls_Manager::TEXT, 'default' => 'Social' ] );
		$this->add_control( 'social', [ 'label' => esc_html__( 'Social icons', 'agency-elementor-widgets' ), 'type' => Controls_Manager::REPEATER, 'fields' => $srep->get_controls(), 'default' => [], 'title_field' => '{{{ label }}}', 'condition' => [ 'show_contact' => 'yes' ] ] );

		$this->end_controls_section();
	}

	/**
	 * CONTENT — layout (column count + gaps).
	 *
	 * @return void
	 */
	private function controls_layout(): void {
		$this->start_controls_section( 's_layout', [ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ) ] );

		$this->add_responsive_control( 'columns', [
			'label'          => esc_html__( 'Columns', 'agency-elementor-widgets' ),
			'type'           => Controls_Manager::SELECT,
			'default'        => '3',
			'tablet_default' => '2',
			'mobile_default' => '1',
			'options'        => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ],
			'selectors'      => [ '{{WRAPPER}} .aew-ftv3__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ],
		] );
		$this->add_responsive_control( 'col_gap', [
			'label'      => esc_html__( 'Column gap', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 16, 'max' => 120 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 48 ],
			'selectors'  => [ '{{WRAPPER}} .aew-ftv3__grid' => 'column-gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_body(): void {
		$this->start_controls_section( 'ss_body', [ 'label' => esc_html__( 'Body', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'bg_color', [ 'label' => esc_html__( 'Background', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-ftv3-bg: {{VALUE}};' ] ] );
		$this->end_controls_section();
	}

	private function style_headings(): void {
		$this->start_controls_section( 'ss_headings', [ 'label' => esc_html__( 'Headings', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'heading_color', [ 'label' => esc_html__( 'Color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-ftv3-heading: {{VALUE}};' ] ] );
		$this->add_control( 'divider_color', [ 'label' => esc_html__( 'Divider color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-ftv3-divider: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'heading_typo',
			'selector'       => '{{WRAPPER}} .aew-ftv3__heading',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 24 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 22 ] ],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 100 ] ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_links(): void {
		$this->start_controls_section( 'ss_links', [ 'label' => esc_html__( 'Links', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'link_color', [ 'label' => esc_html__( 'Color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-ftv3-link: {{VALUE}};' ] ] );
		$this->add_control( 'link_hover', [ 'label' => esc_html__( 'Color (hover)', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-ftv3-link-hover: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'link_typo',
			'selector'       => '{{WRAPPER}} .aew-ftv3__link, {{WRAPPER}} .aew-ftv3__contact-line',
			'fields_options' => [
				'typography'     => [ 'default' => 'custom' ],
				'font_family'    => [ 'default' => 'Lato' ],
				'font_weight'    => [ 'default' => '400' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 13 ] ],
				'line_height'    => [ 'default' => [ 'unit' => 'em', 'size' => 1.5 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 0.6 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_contact(): void {
		$this->start_controls_section( 'ss_contact', [ 'label' => esc_html__( 'Contact / social', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'icon_color', [ 'label' => esc_html__( 'Contact icon color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-ftv3-icon: {{VALUE}};' ] ] );
		$this->add_control( 'social_color', [ 'label' => esc_html__( 'Social icon color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}}' => '--aew-ftv3-social: {{VALUE}};' ] ] );
		$this->add_control( 'social_bg', [ 'label' => esc_html__( 'Social badge background', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '', 'description' => esc_html__( 'Leave empty for plain glyphs; set a colour for circular badges.', 'agency-elementor-widgets' ), 'selectors' => [ '{{WRAPPER}}' => '--aew-ftv3-social-bg: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'social_size', [ 'label' => esc_html__( 'Social icon size', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SLIDER, 'size_units' => [ 'px' ], 'range' => [ 'px' => [ 'min' => 16, 'max' => 48 ] ], 'default' => [ 'unit' => 'px', 'size' => 28 ], 'selectors' => [ '{{WRAPPER}} .aew-ftv3__social-link' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ] ] );
		$this->end_controls_section();
	}

	/**
	 * Parse an Elementor URL control value into url/target/rel.
	 *
	 * @param mixed $d URL control value.
	 * @return array{url:string,target:string,rel:string}
	 */
	private function parse_link( $d ): array {
		if ( ! is_array( $d ) ) {
			return [ 'url' => '', 'target' => '', 'rel' => '' ];
		}
		$t = ! empty( $d['is_external'] ) ? '_blank' : '';
		$r = $t ? 'noopener' : '';
		if ( ! empty( $d['nofollow'] ) ) {
			$r .= ' nofollow';
		}
		return [ 'url' => $d['url'] ?? '', 'target' => $t, 'rel' => trim( $r ) ];
	}

	/**
	 * Parse the links textarea into rows. Each row is either a spacer or a link.
	 *
	 * @param string $raw Links textarea.
	 * @return array<int, array{spacer:bool,label:string,url:string,external:bool}>
	 */
	private function parse_links( string $raw ): array {
		$out = [];
		$lines = preg_split( '/\r\n|\r|\n/', $raw );
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				$out[] = [ 'spacer' => true, 'label' => '', 'url' => '', 'external' => false ];
				continue;
			}
			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			$label = $parts[0];
			$url   = $parts[1] ?? '';
			$out[] = [ 'spacer' => false, 'label' => $label, 'url' => $url, 'external' => ( false !== strpos( $url, '//' ) ) ];
		}
		return $out;
	}

	/**
	 * Built-in inline SVG icons for the contact lines.
	 *
	 * @param string $name pin|mail|phone.
	 * @return string SVG markup.
	 */
	private function icon_svg( string $name ): string {
		$icons = [
			'pin'   => '<path d="M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/>',
			'mail'  => '<path d="M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm9 7L4 7v1l8 5 8-5V7l-8 5z"/>',
			'phone' => '<path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.5 21 3 13.5 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.4 0 .8-.3 1l-2.2 2.2z"/>',
		];
		$path = $icons[ $name ] ?? '';
		return '<svg class="aew-ftv3__contact-ic" viewBox="0 0 24 24" fill="currentColor" width="16" height="16" aria-hidden="true">' . $path . '</svg>';
	}

	/**
	 * Built-in single-colour social glyph (fill: currentColor → recolourable).
	 *
	 * @param string $platform instagram|facebook|tiktok|youtube|x.
	 * @return string SVG markup.
	 */
	private function social_svg( string $platform ): string {
		$icons = [
			'instagram' => '<path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.3 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.3-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.3-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.4C8.4 2.2 8.8 2.2 12 2.2zm0 1.8c-3.1 0-3.5 0-4.7.1-1.1.1-1.7.2-2.1.4-.5.2-.9.4-1.3.8-.4.4-.6.8-.8 1.3-.2.4-.3 1-.4 2.1C2.6 9.5 2.6 9.9 2.6 12s0 2.5.1 3.7c.1 1.1.2 1.7.4 2.1.2.5.4.9.8 1.3.4.4.8.6 1.3.8.4.2 1 .3 2.1.4 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1c1.1-.1 1.7-.2 2.1-.4.5-.2.9-.4 1.3-.8.4-.4.6-.8.8-1.3.2-.4.3-1 .4-2.1.1-1.2.1-1.6.1-3.7s0-2.5-.1-3.7c-.1-1.1-.2-1.7-.4-2.1-.2-.5-.4-.9-.8-1.3-.4-.4-.8-.6-1.3-.8-.4-.2-1-.3-2.1-.4C15.5 4 15.1 4 12 4zm0 3.1A4.9 4.9 0 1 1 12 17a4.9 4.9 0 0 1 0-9.8zm0 1.8a3.1 3.1 0 1 0 0 6.2 3.1 3.1 0 0 0 0-6.2zm5.1-3.2a1.1 1.1 0 1 1 0 2.3 1.1 1.1 0 0 1 0-2.3z"/>',
			'facebook'  => '<path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7A10 10 0 0 0 22 12z"/>',
			'tiktok'    => '<path d="M16.6 5.8a4.8 4.8 0 0 1-1-.1V15a4.6 4.6 0 1 1-4.6-4.6c.2 0 .4 0 .6.1v2.4a2.2 2.2 0 1 0 1.6 2.1V2h2.3a4.8 4.8 0 0 0 4.1 4.7v2.3a7 7 0 0 1-3-.7v-.1a4.8 4.8 0 0 0-.6-2.4z"/>',
			'youtube'   => '<path d="M21.6 7.2s-.2-1.4-.8-2c-.7-.8-1.5-.8-1.9-.8C16.2 4.2 12 4.2 12 4.2h0s-4.2 0-6.9.2c-.4 0-1.2 0-1.9.8-.6.6-.8 2-.8 2S2.2 8.8 2.2 10.5v1.3c0 1.6.2 3.3.2 3.3s.2 1.4.8 2c.7.8 1.7.8 2.1.9 1.5.1 6.7.2 6.7.2s4.2 0 6.9-.2c.4 0 1.2 0 1.9-.8.6-.6.8-2 .8-2s.2-1.6.2-3.3v-1.3c0-1.6-.2-3.4-.2-3.4zM9.9 14.6V8.9l5.5 2.9-5.5 2.8z"/>',
			'x'         => '<path d="M17.5 3h3l-6.6 7.5L21.8 21h-6l-4.7-6.1L5.7 21h-3l7-8L2.5 3h6.1l4.2 5.6L17.5 3zm-1.1 16h1.7L7.7 4.7H5.9L16.4 19z"/>',
		];
		$path = $icons[ $platform ] ?? $icons['instagram'];
		return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">' . $path . '</svg>';
	}

	/**
	 * Render the groups for one column index.
	 *
	 * @param array<int, array<string,mixed>> $groups All groups.
	 * @param int                              $col    Column index.
	 * @return void
	 */
	private function render_column_groups( array $groups, int $col ): void {
		foreach ( $groups as $g ) {
			if ( (int) ( $g['column'] ?? 1 ) !== $col ) {
				continue;
			}
			$heading = (string) ( $g['heading'] ?? '' );
			$rows    = $this->parse_links( (string) ( $g['links'] ?? '' ) );
			$divider = 'yes' === ( $g['show_divider'] ?? 'yes' );
			?>
			<div class="aew-ftv3__group">
				<?php if ( '' !== trim( $heading ) ) : ?>
					<h3 class="aew-ftv3__heading"><?php echo esc_html( $heading ); ?></h3>
				<?php endif; ?>
				<?php if ( $divider ) : ?>
					<span class="aew-ftv3__divider" aria-hidden="true"></span>
				<?php endif; ?>
				<ul class="aew-ftv3__links">
					<?php foreach ( $rows as $row ) : ?>
						<?php if ( $row['spacer'] ) : ?>
							<li class="aew-ftv3__spacer" aria-hidden="true"></li>
						<?php elseif ( '' !== $row['url'] ) : ?>
							<li><a class="aew-ftv3__link" href="<?php echo esc_url( $row['url'] ); ?>"<?php echo $row['external'] ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $row['label'] ); ?></a></li>
						<?php else : ?>
							<li><span class="aew-ftv3__link aew-ftv3__link--plain"><?php echo esc_html( $row['label'] ); ?></span></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		}
	}

	/**
	 * Render the contact block (when it belongs to $col).
	 *
	 * @param array<string,mixed> $s   Settings.
	 * @param int                 $col Column index.
	 * @return void
	 */
	private function render_contact( array $s, int $col ): void {
		if ( 'yes' !== ( $s['show_contact'] ?? '' ) || (int) ( $s['contact_column'] ?? 3 ) !== $col ) {
			return;
		}
		$heading = (string) ( $s['contact_heading'] ?? '' );
		$address = trim( (string) ( $s['contact_address'] ?? '' ) );
		$email   = trim( (string) ( $s['contact_email'] ?? '' ) );
		$phone   = trim( (string) ( $s['contact_phone'] ?? '' ) );
		$social  = is_array( $s['social'] ?? null ) ? $s['social'] : [];
		?>
		<div class="aew-ftv3__group aew-ftv3__group--contact">
			<?php if ( '' !== trim( $heading ) ) : ?>
				<h3 class="aew-ftv3__heading"><?php echo esc_html( $heading ); ?></h3>
				<span class="aew-ftv3__divider" aria-hidden="true"></span>
			<?php endif; ?>
			<?php if ( '' !== $address ) : ?>
				<p class="aew-ftv3__contact-line"><?php echo $this->icon_svg( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $address ); ?></span></p>
			<?php endif; ?>
			<?php if ( '' !== $email ) : ?>
				<p class="aew-ftv3__contact-line"><?php echo $this->icon_svg( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
			<?php endif; ?>
			<?php if ( '' !== $phone ) : ?>
				<p class="aew-ftv3__contact-line"><?php echo $this->icon_svg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
			<?php endif; ?>
			<?php if ( ! empty( $social ) ) : ?>
				<div class="aew-ftv3__social">
					<?php
					foreach ( $social as $si ) :
						$icon     = $si['icon'] ?? [];
						$icon_url = is_array( $icon ) ? ( $icon['url'] ?? '' ) : '';
						$slink    = $this->parse_link( $si['link'] ?? [] );
						if ( '' === $slink['url'] ) {
							continue;
						}
						$lbl      = trim( (string) ( $si['label'] ?? 'Social' ) );
						$platform = (string) ( $si['platform'] ?? 'instagram' );
						?>
						<a class="aew-ftv3__social-link<?php echo '' === $icon_url ? ' aew-ftv3__social-link--glyph' : ''; ?>" href="<?php echo esc_url( $slink['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $lbl ); ?>">
							<?php if ( '' !== $icon_url ) : ?>
								<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $lbl ); ?>" decoding="async" loading="lazy" />
							<?php else : ?>
								<?php echo $this->social_svg( $platform ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render(): void {
		$s      = $this->get_settings_for_display();
		$groups = is_array( $s['groups'] ?? null ) ? $s['groups'] : [];
		$cols   = (int) ( $s['columns'] ?? 3 );
		$cols   = max( 1, min( 4, $cols ) );

		$this->add_render_attribute( 'wrapper', 'class', 'aew-ftv3' );
		$this->add_render_attribute( 'wrapper', 'data-aew-footer-v3', '' );

		$color_vars = Color_Vars::build( $this, $s, [
			'bg_color'      => '--aew-ftv3-bg',
			'heading_color' => '--aew-ftv3-heading',
			'divider_color' => '--aew-ftv3-divider',
			'link_color'    => '--aew-ftv3-link',
			'link_hover'    => '--aew-ftv3-link-hover',
			'icon_color'    => '--aew-ftv3-icon',
			'social_color'  => '--aew-ftv3-social',
			'social_bg'     => '--aew-ftv3-social-bg',
		] );
		if ( '' !== $color_vars ) {
			$this->add_render_attribute( 'wrapper', 'style', $color_vars );
		}
		?>
		<footer <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-ftv3__inner">
				<div class="aew-ftv3__grid">
					<?php for ( $c = 1; $c <= $cols; $c++ ) : ?>
						<div class="aew-ftv3__col">
							<?php
							$this->render_column_groups( $groups, $c );
							$this->render_contact( $s, $c );
							?>
						</div>
					<?php endfor; ?>
				</div>
			</div>
		</footer>
		<?php
	}
}
