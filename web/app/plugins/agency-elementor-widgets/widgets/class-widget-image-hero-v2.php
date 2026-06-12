<?php
/**
 * Image Hero V2 Elementor widget — a background image with a single heading that
 * can be pinned to any corner (or edge/centre): top-left, top-right,
 * bottom-left, bottom-right, plus the centre positions.
 *
 * Brand-free: every colour reads an `aew-*` Elementor global with a neutral-grey
 * placeholder; render() resolves global-bound picks via Color_Vars (§6.8 / #19).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

/**
 * Background-image hero with a corner-positionable heading.
 */
class Widget_Image_Hero_V2 extends Widget_Base {

	private const ASSET_SLUG = 'image-hero-v2';

	public function get_name(): string {
		return 'agency-image-hero-v2';
	}

	public function get_title(): string {
		return esc_html__( 'Image Hero V2', 'agency-elementor-widgets' );
	}

	public function get_icon(): string {
		return 'eicon-image';
	}

	public function get_categories(): array {
		return [ 'agency-widgets' ];
	}

	public function get_keywords(): array {
		return [ 'hero', 'image', 'banner', 'heading', 'corner' ];
	}

	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * Re-point _padding to the inner content; defaults EMPTY (guide §5 / #16).
	 *
	 * @param bool $with_common_controls Whether common controls are included.
	 * @return array<string, mixed>
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-imhv2__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ];
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	protected function register_controls(): void {
		$this->controls_media();
		$this->controls_content();
		$this->controls_position();
		$this->controls_layout();
		$this->style_heading();
		$this->style_overlay();
	}

	private function controls_media(): void {
		$this->start_controls_section( 's_media', [ 'label' => esc_html__( 'Image', 'agency-elementor-widgets' ) ] );
		$this->add_control( 'image', [ 'label' => esc_html__( 'Background image', 'agency-elementor-widgets' ), 'type' => Controls_Manager::MEDIA ] );
		$this->add_control( 'image_mobile', [ 'label' => esc_html__( 'Mobile image (optional)', 'agency-elementor-widgets' ), 'type' => Controls_Manager::MEDIA ] );
		$this->add_control( 'focal', [
			'label'   => esc_html__( 'Image focal point', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'center center',
			'options' => [
				'center center' => esc_html__( 'Center', 'agency-elementor-widgets' ),
				'center top'    => esc_html__( 'Top', 'agency-elementor-widgets' ),
				'center bottom' => esc_html__( 'Bottom', 'agency-elementor-widgets' ),
				'left center'   => esc_html__( 'Left', 'agency-elementor-widgets' ),
				'right center'  => esc_html__( 'Right', 'agency-elementor-widgets' ),
			],
			'selectors' => [ '{{WRAPPER}} .aew-imhv2__media' => 'background-position: {{VALUE}};' ],
		] );
		$this->end_controls_section();
	}

	private function controls_content(): void {
		$this->start_controls_section( 's_content', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ) ] );
		$this->add_control( 'heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 3, 'default' => '', 'description' => esc_html__( 'Leave empty for an image-only hero.', 'agency-elementor-widgets' ) ] );
		$this->add_control( 'heading_tag', [ 'label' => esc_html__( 'Heading tag', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SELECT, 'default' => 'h1', 'options' => [ 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3' ] ] );
		$this->add_control( 'heading_link', [ 'label' => esc_html__( 'Heading link (optional)', 'agency-elementor-widgets' ), 'type' => Controls_Manager::URL, 'default' => [ 'url' => '' ] ] );
		$this->end_controls_section();
	}

	private function controls_position(): void {
		$this->start_controls_section( 's_position', [ 'label' => esc_html__( 'Heading position', 'agency-elementor-widgets' ) ] );

		$this->add_responsive_control( 'v_align', [
			'label'   => esc_html__( 'Vertical', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::CHOOSE,
			'default' => 'bottom',
			'options' => [
				'top'    => [ 'title' => esc_html__( 'Top', 'agency-elementor-widgets' ), 'icon' => 'eicon-v-align-top' ],
				'middle' => [ 'title' => esc_html__( 'Middle', 'agency-elementor-widgets' ), 'icon' => 'eicon-v-align-middle' ],
				'bottom' => [ 'title' => esc_html__( 'Bottom', 'agency-elementor-widgets' ), 'icon' => 'eicon-v-align-bottom' ],
			],
			'selectors'            => [ '{{WRAPPER}} .aew-imhv2__content' => 'align-items: {{VALUE}};' ],
			'selectors_dictionary' => [ 'top' => 'flex-start', 'middle' => 'center', 'bottom' => 'flex-end' ],
		] );

		$this->add_responsive_control( 'h_align', [
			'label'   => esc_html__( 'Horizontal', 'agency-elementor-widgets' ),
			'type'    => Controls_Manager::CHOOSE,
			'default' => 'left',
			'options' => [
				'left'   => [ 'title' => esc_html__( 'Left', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-left' ],
				'center' => [ 'title' => esc_html__( 'Center', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-center' ],
				'right'  => [ 'title' => esc_html__( 'Right', 'agency-elementor-widgets' ), 'icon' => 'eicon-text-align-right' ],
			],
			'selectors'            => [ '{{WRAPPER}} .aew-imhv2__content' => 'justify-content: {{VALUE}};' ],
			'selectors_dictionary' => [
				'left'   => 'flex-start; text-align: left',
				'center' => 'center; text-align: center',
				'right'  => 'flex-end; text-align: right',
			],
		] );

		$this->end_controls_section();
	}

	private function controls_layout(): void {
		$this->start_controls_section( 's_layout', [ 'label' => esc_html__( 'Layout', 'agency-elementor-widgets' ) ] );

		$this->add_responsive_control( 'height', [
			'label'          => esc_html__( 'Height', 'agency-elementor-widgets' ),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => [ 'px', 'vh' ],
			'range'          => [ 'px' => [ 'min' => 200, 'max' => 1200 ], 'vh' => [ 'min' => 20, 'max' => 100 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 620 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 420 ],
			'selectors'      => [ '{{WRAPPER}} .aew-imhv2' => 'height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'inset', [
			'label'          => esc_html__( 'Heading inset (padding)', 'agency-elementor-widgets' ),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => [ 'px' ],
			'range'          => [ 'px' => [ 'min' => 0, 'max' => 160 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 48 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'      => [ '{{WRAPPER}} .aew-imhv2__content' => '--aew-imhv2-pad: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'radius', [
			'label'      => esc_html__( 'Corner radius', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 0 ],
			'selectors'  => [ '{{WRAPPER}} .aew-imhv2' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'show_overlay', [ 'label' => esc_html__( 'Dark overlay (for legibility)', 'agency-elementor-widgets' ), 'type' => Controls_Manager::SWITCHER, 'default' => '' ] );

		$this->end_controls_section();
	}

	private function style_heading(): void {
		$this->start_controls_section( 'ss_heading', [ 'label' => esc_html__( 'Heading', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'heading_color', [ 'label' => esc_html__( 'Color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-imhv2-heading: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'heading_typo',
			'selector'       => '{{WRAPPER}} .aew-imhv2__heading',
			'fields_options' => [
				'typography'  => [ 'default' => 'custom' ],
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 80 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 48 ] ],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
			],
		] );
		$this->add_responsive_control( 'heading_max', [
			'label'      => esc_html__( 'Heading max width', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [ 'px' => [ 'min' => 200, 'max' => 1200 ], '%' => [ 'min' => 20, 'max' => 100 ] ],
			'default'    => [ 'unit' => '%', 'size' => 100 ],
			'selectors'  => [ '{{WRAPPER}} .aew-imhv2__heading' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );
		$this->end_controls_section();
	}

	private function style_overlay(): void {
		$this->start_controls_section( 'ss_overlay', [ 'label' => esc_html__( 'Overlay', 'agency-elementor-widgets' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_overlay' => 'yes' ] ] );
		$this->add_control( 'overlay_color', [ 'label' => esc_html__( 'Color', 'agency-elementor-widgets' ), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => [ '{{WRAPPER}}' => '--aew-imhv2-overlay: {{VALUE}};' ] ] );
		$this->add_responsive_control( 'overlay_opacity', [
			'label'      => esc_html__( 'Opacity', 'agency-elementor-widgets' ),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
			'default'    => [ 'size' => 0.35 ],
			'selectors'  => [ '{{WRAPPER}} .aew-imhv2__overlay' => 'opacity: {{SIZE}};' ],
		] );
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

	protected function render(): void {
		$s          = $this->get_settings_for_display();
		$image      = $s['image'] ?? [];
		$image_url  = is_array( $image ) ? ( $image['url'] ?? '' ) : '';
		$mobile     = $s['image_mobile'] ?? [];
		$mobile_url = is_array( $mobile ) ? ( $mobile['url'] ?? '' ) : '';
		$heading    = (string) ( $s['heading'] ?? '' );
		$tag        = (string) ( $s['heading_tag'] ?? 'h1' );
		$tag        = in_array( $tag, [ 'h1', 'h2', 'h3' ], true ) ? $tag : 'h1';
		$link       = $this->parse_link( $s['heading_link'] ?? [] );

		$v_align = (string) ( $s['v_align'] ?? 'bottom' );
		$v_align = in_array( $v_align, [ 'top', 'middle', 'bottom' ], true ) ? $v_align : 'bottom';
		$this->add_render_attribute( 'wrapper', 'class', [ 'aew-imhv2', 'aew-imhv2--v-' . $v_align ] );
		$this->add_render_attribute( 'wrapper', 'data-aew-image-hero-v2', '' );

		$color_vars = Color_Vars::build( $this, $s, [
			'heading_color' => '--aew-imhv2-heading',
			'overlay_color' => '--aew-imhv2-overlay',
		] );

		$bg = '';
		if ( '' !== $image_url ) {
			$bg .= '--aew-imhv2-bg:url(' . esc_url( $image_url ) . ');';
		}
		if ( '' !== $mobile_url ) {
			$bg .= '--aew-imhv2-bg-m:url(' . esc_url( $mobile_url ) . ');';
		}
		$style = trim( $color_vars . $bg );
		if ( '' !== $style ) {
			$this->add_render_attribute( 'wrapper', 'style', $style );
		}
		?>
		<section <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="aew-imhv2__media" role="img" aria-label="<?php echo esc_attr( $heading ); ?>"></div>
			<?php if ( 'yes' === ( $s['show_overlay'] ?? '' ) ) : ?>
				<div class="aew-imhv2__overlay" aria-hidden="true"></div>
			<?php endif; ?>
			<div class="aew-imhv2__content">
				<?php if ( '' !== trim( $heading ) ) : ?>
					<<?php echo esc_attr( $tag ); ?> class="aew-imhv2__heading">
						<?php if ( '' !== $link['url'] ) : ?>
							<a class="aew-imhv2__heading-link" href="<?php echo esc_url( $link['url'] ); ?>"
								<?php echo $link['target'] ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>
								<?php echo $link['rel'] ? 'rel="' . esc_attr( $link['rel'] ) . '"' : ''; ?>>
								<?php echo nl2br( esc_html( $heading ) ); ?>
							</a>
						<?php else : ?>
							<?php echo nl2br( esc_html( $heading ) ); ?>
						<?php endif; ?>
					</<?php echo esc_attr( $tag ); ?>>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
