<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FAQ_Accordion {

    private $option_name = 'faq_accordion_settings';

    public function run() {
        add_shortcode('faq_accordion', array($this, 'render_faq_accordion'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    // بارگذاری CSS و JS در فرانت
    public function enqueue_scripts() {
        if (!is_singular()) return;

        $opts = get_option($this->option_name, array());

        $custom_css = "
        .faq-accordion .faq-item { border-bottom: 1px solid #ddd; }
        .faq-question {
            background: ".esc_attr($opts['question_bg_color'] ?? '#f7f7f7').";
            color: ".esc_attr($opts['question_text_color'] ?? '#333').";
            padding: ".intval($opts['question_padding'] ?? 10)."px;
            width: 100%;
            text-align: left;
            border: none;
            font-size: ".intval($opts['question_font_size'] ?? 16)."px;
            cursor: pointer;
            font-family: ".esc_attr($opts['question_font_family'] ?? 'inherit').";
        }
        .faq-answer {
            display: none;
            padding: 10px;
            background: ".esc_attr($opts['answer_bg_color'] ?? '#fff').";
            color: ".esc_attr($opts['answer_text_color'] ?? '#222').";
            font-family: ".esc_attr($opts['answer_font_family'] ?? 'inherit').";
        }
        .faq-item.active .faq-answer { display: block; }
        ";

        wp_register_style('faq-accordion-style', false);
        wp_enqueue_style('faq-accordion-style');
        wp_add_inline_style('faq-accordion-style', $custom_css);

        wp_enqueue_script('faq-accordion-script', plugin_dir_url(__FILE__) . 'js/faq-accordion.js', array(), '1.0', true);
    }

    // رندر آکوردیون FAQ
    public function render_faq_accordion($atts) {
        if (!is_singular()) return '';

        $post_id = get_the_ID();
        $serialized_data = get_post_meta($post_id, 'rank_math_schema_FAQPage', true);
        if (!$serialized_data) return '';

        $data = maybe_unserialize($serialized_data);
        if (!$data || !isset($data['mainEntity'])) return '';

        ob_start();
        ?>
        <div class="faq-accordion">
            <?php foreach ($data['mainEntity'] as $faq): ?>
                <div class="faq-item">
                    <button class="faq-question" type="button"><?php echo esc_html($faq['name']); ?></button>
                    <div class="faq-answer"><?php echo wp_kses_post($faq['acceptedAnswer']['text']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // اضافه کردن منوی تنظیمات در پنل مدیریت
    public function add_admin_menu() {
        add_options_page(
            __('FAQ Accordion Settings', 'faq-accordion-rankmath'),
            __('FAQ Accordion', 'faq-accordion-rankmath'),
            'manage_options',
            'faq-accordion-settings',
            array($this, 'settings_page_html')
        );
    }

    // ثبت تنظیمات به همراه sanitize_callback
    public function register_settings() {
        register_setting('faq_accordion_group', $this->option_name, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);

        add_settings_section(
            'faq_accordion_main_section',
            __('تنظیمات استایل FAQ آکوردیون', 'faq-accordion-rankmath'),
            null,
            'faq-accordion-settings'
        );

        add_settings_field(
            'question_bg_color',
            __('رنگ پس‌زمینه سوال', 'faq-accordion-rankmath'),
            array($this, 'field_color_picker'),
            'faq-accordion-settings',
            'faq_accordion_main_section',
            ['label_for' => 'question_bg_color']
        );

        add_settings_field(
            'question_text_color',
            __('رنگ متن سوال', 'faq-accordion-rankmath'),
            array($this, 'field_color_picker'),
            'faq-accordion-settings',
            'faq_accordion_main_section',
            ['label_for' => 'question_text_color']
        );

        add_settings_field(
            'question_font_size',
            __('اندازه فونت سوال (px)', 'faq-accordion-rankmath'),
            array($this, 'field_number'),
            'faq-accordion-settings',
            'faq_accordion_main_section',
            ['label_for' => 'question_font_size']
        );

        add_settings_field(
            'answer_bg_color',
            __('رنگ پس‌زمینه جواب', 'faq-accordion-rankmath'),
            array($this, 'field_color_picker'),
            'faq-accordion-settings',
            'faq_accordion_main_section',
            ['label_for' => 'answer_bg_color']
        );

        add_settings_field(
            'answer_text_color',
            __('رنگ متن جواب', 'faq-accordion-rankmath'),
            array($this, 'field_color_picker'),
            'faq-accordion-settings',
            'faq_accordion_main_section',
            ['label_for' => 'answer_text_color']
        );

        add_settings_field(
            'question_padding',
            __('فاصله داخلی سوال (px)', 'faq-accordion-rankmath'),
            array($this, 'field_number'),
            'faq-accordion-settings',
            'faq_accordion_main_section',
            ['label_for' => 'question_padding']
        );

        add_settings_field(
            'question_font_family',
            __('فونت سوال', 'faq-accordion-rankmath'),
            array($this, 'field_text'),
            'faq-accordion-settings',
            'faq_accordion_main_section',
            ['label_for' => 'question_font_family']
        );

        add_settings_field(
            'answer_font_family',
            __('فونت جواب', 'faq-accordion-rankmath'),
            array($this, 'field_text'),
            'faq-accordion-settings',
            'faq_accordion_main_section',
            ['label_for' => 'answer_font_family']
        );
    }

    // تابع sanitize برای پاکسازی ورودی‌ها
    public function sanitize_settings($input) {
        $output = [];

        if (isset($input['question_bg_color'])) {
            $output['question_bg_color'] = sanitize_hex_color($input['question_bg_color']);
        }

        if (isset($input['question_text_color'])) {
            $output['question_text_color'] = sanitize_hex_color($input['question_text_color']);
        }

        if (isset($input['question_font_size'])) {
            $output['question_font_size'] = intval($input['question_font_size']);
        }

        if (isset($input['answer_bg_color'])) {
            $output['answer_bg_color'] = sanitize_hex_color($input['answer_bg_color']);
        }

        if (isset($input['answer_text_color'])) {
            $output['answer_text_color'] = sanitize_hex_color($input['answer_text_color']);
        }

        if (isset($input['question_padding'])) {
            $output['question_padding'] = intval($input['question_padding']);
        }

        if (isset($input['question_font_family'])) {
            $output['question_font_family'] = sanitize_text_field($input['question_font_family']);
        }

        if (isset($input['answer_font_family'])) {
            $output['answer_font_family'] = sanitize_text_field($input['answer_font_family']);
        }

        return $output;
    }

    // صفحه تنظیمات HTML
    public function settings_page_html() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('تنظیمات FAQ Accordion', 'faq-accordion-rankmath'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('faq_accordion_group');
                do_settings_sections('faq-accordion-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // فیلد رنگ (color picker)
    public function field_color_picker($args) {
        $options = get_option($this->option_name);
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        echo '<input type="text" id="'.esc_attr($args['label_for']).'" name="'.esc_attr($this->option_name).'['.esc_attr($args['label_for']).']" value="'.esc_attr($value).'" class="faq-color-field" />';
        $this->enqueue_color_picker();
    }

    // فیلد عددی
    public function field_number($args) {
        $options = get_option($this->option_name);
        $value = isset($options[$args['label_for']]) ? intval($options[$args['label_for']]) : '';
        echo '<input type="number" min="0" id="'.esc_attr($args['label_for']).'" name="'.esc_attr($this->option_name).'['.esc_attr($args['label_for']).']" value="'.esc_attr($value).'" />';
    }

    // فیلد متن ساده
    public function field_text($args) {
        $options = get_option($this->option_name);
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        echo '<input type="text" id="'.esc_attr($args['label_for']).'" name="'.esc_attr($this->option_name).'['.esc_attr($args['label_for']).']" value="'.esc_attr($value).'" />';
    }

    // بارگذاری color picker در پنل تنظیمات
    public function enqueue_color_picker() {
        static $loaded = false;
        if ($loaded) return;
        $loaded = true;
        
        wp_register_style('faq-accordion-style', false, array(), '1.0');
        wp_enqueue_style('faq-accordion-style');
        wp_add_inline_style('faq-accordion-style', $custom_css);

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        ?>
        <script>
        jQuery(document).ready(function($){
            $('.faq-color-field').wpColorPicker();
        });
        </script>
        <?php
    }
}
