<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class FAQ_Accordion {

    private $option_name = 'faq_accordion_settings';

    public function run() {
        add_shortcode( 'faq_accordion', array( $this, 'render_faq_accordion' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    public function enqueue_scripts() {
        if ( ! is_singular() ) {
            return;
        }

        $opts       = get_option( $this->option_name, array() );
        $custom_css = "
        .faq-accordion .faq-item { border-bottom: 1px solid #ddd; }
        .faq-question {
            background: " . esc_attr( $opts['question_bg_color'] ?? '#f7f7f7' ) . ";
            color: " . esc_attr( $opts['question_text_color'] ?? '#333' ) . ";
            padding: " . intval( $opts['question_padding'] ?? 10 ) . "px;
            width: 100%;
            text-align: left;
            border: none;
            font-size: " . intval( $opts['question_font_size'] ?? 16 ) . "px;
            cursor: pointer;
            font-family: " . esc_attr( $opts['question_font_family'] ?? 'inherit' ) . ";
        }
        .faq-answer {
            display: none;
            padding: 10px;
            background: " . esc_attr( $opts['answer_bg_color'] ?? '#fff' ) . ";
            color: " . esc_attr( $opts['answer_text_color'] ?? '#222' ) . ";
            font-family: " . esc_attr( $opts['answer_font_family'] ?? 'inherit' ) . ";
        }
        .faq-item.active .faq-answer { display: block; }
        ";

        wp_register_style( 'faq-accordion-style', false );
        wp_enqueue_style( 'faq-accordion-style' );
        wp_add_inline_style( 'faq-accordion-style', $custom_css );

        wp_enqueue_script( 'faq-accordion-script', plugin_dir_url( __FILE__ ) . '../js/faq-accordion.js', array(), '1.1', true );
    }

    public function render_faq_accordion( $atts ) {
        if ( ! is_singular() ) {
            return '';
        }

        $post_id         = get_the_ID();
        $serialized_data = get_post_meta( $post_id, 'rank_math_schema_FAQPage', true );
        if ( ! $serialized_data ) {
            return '';
        }

        $data = maybe_unserialize( $serialized_data );
        if ( ! $data || ! isset( $data['mainEntity'] ) ) {
            return '';
        }

        ob_start();
        ?>
        <div class="faq-accordion">
            <?php foreach ( $data['mainEntity'] as $faq ) : ?>
                <div class="faq-item">
                    <button class="faq-question" type="button"><?php echo esc_html( $faq['name'] ); ?></button>
                    <div class="faq-answer"><?php echo wp_kses_post( $faq['acceptedAnswer']['text'] ); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function add_admin_menu() {
        add_options_page(
            __( 'FAQ Accordion Settings', 'easy-faq-accordion-for-rank-math' ),
            __( 'FAQ Accordion', 'easy-faq-accordion-for-rank-math' ),
            'manage_options',
            'faq-accordion-settings',
            array( $this, 'settings_page_html' )
        );
    }

    public function register_settings() {
        register_setting( 'faq_accordion_group', $this->option_name, [
            'sanitize_callback' => [ $this, 'sanitize_settings' ],
        ] );

        add_settings_section(
            'faq_accordion_main_section',
            __( 'FAQ Accordion Style Settings', 'easy-faq-accordion-for-rank-math' ),
            null,
            'faq-accordion-settings'
        );

        $fields = [
            'question_bg_color'    => __( 'Question Background Color', 'easy-faq-accordion-for-rank-math' ),
            'question_text_color'  => __( 'Question Text Color', 'easy-faq-accordion-for-rank-math' ),
            'question_font_size'   => __( 'Question Font Size (px)', 'easy-faq-accordion-for-rank-math' ),
            'answer_bg_color'      => __( 'Answer Background Color', 'easy-faq-accordion-for-rank-math' ),
            'answer_text_color'    => __( 'Answer Text Color', 'easy-faq-accordion-for-rank-math' ),
            'question_padding'     => __( 'Question Padding (px)', 'easy-faq-accordion-for-rank-math' ),
            'question_font_family' => __( 'Question Font Family', 'easy-faq-accordion-for-rank-math' ),
            'answer_font_family'   => __( 'Answer Font Family', 'easy-faq-accordion-for-rank-math' ),
        ];

        foreach ( $fields as $id => $label ) {
            $type = 'text';
            if ( str_contains( $id, 'color' ) ) {
                $type = 'color';
            } elseif ( str_contains( $id, 'size' ) || str_contains( $id, 'padding' ) ) {
                $type = 'number';
            }

            add_settings_field(
                $id,
                $label,
                array( $this, 'render_field_' . $type ),
                'faq-accordion-settings',
                'faq_accordion_main_section',
                [ 'label_for' => $id ]
            );
        }
    }

    public function sanitize_settings( $input ) {
        $output = [];
        $fields = [
            'question_bg_color'    => 'hex_color',
            'question_text_color'  => 'hex_color',
            'question_font_size'   => 'int',
            'answer_bg_color'      => 'hex_color',
            'answer_text_color'    => 'hex_color',
            'question_padding'     => 'int',
            'question_font_family' => 'text',
            'answer_font_family'   => 'text',
        ];

        foreach ( $fields as $field => $type ) {
            if ( isset( $input[ $field ] ) ) {
                switch ( $type ) {
                    case 'hex_color':
                        $output[ $field ] = sanitize_hex_color( $input[ $field ] );
                        break;
                    case 'int':
                        $output[ $field ] = intval( $input[ $field ] );
                        break;
                    case 'text':
                        $output[ $field ] = sanitize_text_field( $input[ $field ] );
                        break;
                }
            }
        }
        return $output;
    }

    public function settings_page_html() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'FAQ Accordion Settings', 'easy-faq-accordion-for-rank-math' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'faq_accordion_group' );
                do_settings_sections( 'faq-accordion-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    private function render_field( $type, $args ) {
        $options = get_option( $this->option_name );
        $value   = $options[ $args['label_for'] ] ?? '';
        $id      = esc_attr( $args['label_for'] );
        $name    = esc_attr( $this->option_name . '[' . $args['label_for'] . ']' );

        switch ( $type ) {
            case 'color':
                echo '<input type="text" id="' . $id . '" name="' . $name . '" value="' . esc_attr( $value ) . '" class="faq-color-field" />';
                break;
            case 'number':
                echo '<input type="number" min="0" id="' . $id . '" name="' . $name . '" value="' . esc_attr( $value ) . '" />';
                break;
            case 'text':
                echo '<input type="text" id="' . $id . '" name="' . $name . '" value="' . esc_attr( $value ) . '" />';
                break;
        }
    }

    public function render_field_color( $args ) { $this->render_field( 'color', $args ); }
    public function render_field_number( $args ) { $this->render_field( 'number', $args ); }
    public function render_field_text( $args ) { $this->render_field( 'text', $args ); }

    public function admin_enqueue_scripts( $hook ) {
        if ( 'settings_page_faq-accordion-settings' !== $hook ) {
            return;
        }
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'faq-admin-script', plugin_dir_url( __FILE__ ) . '../js/faq-admin.js', array( 'wp-color-picker' ), '1.1', true );
    }
}