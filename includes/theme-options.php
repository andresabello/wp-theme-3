<?php
class AcSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    /**
     * Start up
     */
    public function __construct()
    {
        // Add the page to the admin menu
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        // Register page options
        add_action( 'admin_init', array( $this, 'page_init' ) );          
    }
    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        $pi_options_page = add_options_page(
                            'Settings Admin', 
                            'AC Theme Options', 
                            'manage_options', 
                            'ac-setting-admin', 
                            array( $this, 'create_admin_page' )
                        );
        add_action( 'admin_print_scripts-' . $pi_options_page, array( $this, 'ac_print_scripts' ) );
    }
    /**
     * Scripts to upload images
    */
    public function ac_print_scripts() {  
        // Stylesheet used by Thickbox
        wp_enqueue_style( 'thickbox' );
        // Css rules for Color Picker
        wp_enqueue_style( 'wp-color-picker' );
        // Thickbox scripts
        wp_enqueue_script( 'thickbox' );
        // Media Upload scripts
        wp_enqueue_script( 'media-upload' );
        // Custom script to start thickbox
        wp_enqueue_script( 'ac-upload', get_template_directory_uri() . '/assets/js/ac-upload.js', array( 'thickbox', 'media-upload' ) );
        // Make sure to add the wp-color-picker dependecy to js file
        wp_enqueue_script( 'ca_custom_js', get_template_directory_uri() .'/assets/js/ac-picker.js', array( 'jquery', 'wp-color-picker' ), '', true  );
    }
    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'ac_option_name' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>AC Settings</h2>
            <a href="http://whitetreatmentdata.com/ac-framework-docs/">Documentation</a>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'ac_option_group' );   
                do_settings_sections( 'ac-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }
    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'ac_option_group', // Option group
            'ac_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
        //Main 
        add_settings_section(
            'setting_section_main', // ID
            'AC Main Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'ac-setting-admin' // Page
        );
        add_settings_field( 
            'ac_logo', 
            'Logo Image', 
            array( $this, 'logo_callback' ), 
            'ac-setting-admin', 
            'setting_section_main' 
        );
        add_settings_field( 
            'ac_font_color', 
            'Font Color', 
            array( $this, 'font_color_callback' ), 
            'ac-setting-admin', 
            'setting_section_main' 
        );
        add_settings_field( 
            'ac_font_family', 
            'Font Family', 
            array( $this, 'font_family_callback' ), 
            'ac-setting-admin', 
            'setting_section_main' 
        );
        add_settings_field( 
            'ac_main_color_picker', 
            'Main Color', 
            array( $this, 'ac_color_settings_field' ), 
            'ac-setting-admin', 
            'setting_section_main' 
        );
        add_settings_field( 
            'ac_second_color_picker', 
            'Secondary Color', 
            array( $this, 'ac_color_second_settings_field' ), 
            'ac-setting-admin', 
            'setting_section_main' 
        );
        add_settings_field( 
            'ac_number', 
            'Phone Number', 
            array( $this, 'phone_number_callback' ), 
            'ac-setting-admin', 
            'setting_section_main' 
        );
        //Header CTA
        add_settings_section(
            'setting_section_header', // ID
            'AC Header Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'ac-setting-admin' // Page
        );  
        add_settings_field(
            'upper_cta', // ID
            'Upper CTA HTML', // Title 
            array( $this, 'upper_cta_callback' ), // Callback
            'ac-setting-admin', // Page
            'setting_section_header' // Section           
        );
        //Main CTA
        add_settings_section(
            'setting_section_main_cta', // ID
            'AC Main CTA Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'ac-setting-admin' // Page
        );  
        add_settings_field( 
            'ac_main_image', 
            'Main CTA Image', 
            array( $this, 'main_image_callback' ), 
            'ac-setting-admin', 
            'setting_section_main_cta' 
        );
        add_settings_field( 
            'ac_img_cta', 
            'Main Image CTA', 
            array( $this, 'img_cta_callback' ), 
            'ac-setting-admin', 
            'setting_section_main_cta' 
        );
        add_settings_field(
            'caption', 
            'Caption', 
            array( $this, 'caption_callback' ), 
            'ac-setting-admin', 
            'setting_section_main_cta'
        ); 
        add_settings_field( 
            'ac_main_image_caption', 
            'Main CTA Caption Image', 
            array( $this, 'main_image_caption_callback' ), 
            'ac-setting-admin', 
            'setting_section_main_cta' 
        );
        //Form
        add_settings_section(
            'setting_section_form', // ID
            'AC Form Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'ac-setting-admin' // Page
        );  
        add_settings_field( 
            'ac_form_image', 
            'Form Backgound', 
            array( $this, 'form_callback' ), 
            'ac-setting-admin', 
            'setting_section_form' 
        );
        //Footer
        add_settings_section(
            'setting_section_footer', // ID
            'AC Footer Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'ac-setting-admin' // Page
        );  
        add_settings_field( 
            'ac_footer_image', 
            'Footer Backgound', 
            array( $this, 'footer_bg_callback' ), 
            'ac-setting-admin', 
            'setting_section_footer' 
        );
    }
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        //Header Options
        $new_input = array();
        //Main Options
        if( isset( $input['ac_logo'] ) )
            $new_input['ac_logo'] = sanitize_text_field( $input['ac_logo'] );
        if( isset( $input['ac_font_color'] ) )
            $new_input['ac_font_color'] = sanitize_text_field( $input['ac_font_color'] );
        if( isset( $input['ac_font_family'] ) )
            $new_input['ac_font_family'] = sanitize_text_field( $input['ac_font_family'] );
        if( isset( $input['ac_main_color_picker'] ) )
            $new_input['ac_main_color_picker'] = sanitize_text_field( $input['ac_main_color_picker'] );
        if( isset( $input['ac_second_color_picker'] ) )
            $new_input['ac_second_color_picker'] = sanitize_text_field( $input['ac_second_color_picker'] );
        if( isset( $input['ac_number'] ) )
            $new_input['ac_number'] = sanitize_text_field( $input['ac_number'] );
        //Header CTA Options
        if( isset( $input['upper_cta'] ) )
            $new_input['upper_cta'] = htmlspecialchars( $input['upper_cta'] );
        //Main CTA Section options
        if( isset( $input['ac_main_image'] ) )
            $new_input['ac_main_image'] = sanitize_text_field( $input['ac_main_image'] ); 
        if( isset( $input['ac_img_cta'] ) )
            $new_input['ac_img_cta'] = htmlspecialchars( $input['ac_img_cta'] ); 
        if( isset( $input['ac_main_image_caption'] ) )
            $new_input['ac_main_image_caption'] = htmlspecialchars( $input['ac_main_image_caption'] );  
        if( isset( $input['caption'] ) )
            $new_input['caption'] = htmlspecialchars( $input['caption'] );
        //Middle CTA Section options
        if( isset( $input['ac_form_image'] ) )
            $new_input['ac_form_image'] = sanitize_text_field( $input['ac_form_image'] );
        if( isset( $input['ac_footer_image'] ) )
            $new_input['ac_footer_image'] = sanitize_text_field( $input['ac_footer_image'] );
        return $new_input;
    }
    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter & Upload your settings below:';
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function logo_callback()
    {   
        printf(
            '<span class="upload">
                <input type="hidden" id="ac_logo" class="regular-text text-upload" name="ac_option_name[ac_logo]" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/></br>
            </span>',
            isset( $this->options['ac_logo'] ) ? esc_url( $this->options['ac_logo']) : '',
            isset( $this->options['ac_logo'] ) ? esc_url( $this->options['ac_logo']) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function font_family_callback()
    {   
        $fonts = array('Open Sans', 'Droid Sans', 'Lato', 'Bitter', 'Helvetica', 'Georgia', 'Trebuchet MS');
        echo '<select id="ac_font_family" name="ac_option_name[ac_font_family]" value="true">';
            foreach ($fonts as $key => $font) {
                echo '<option value="' . $font . '"';
                if ( $font === $this->options['ac_font_family']) {
                    echo '" selected="selected"';
                }
                echo '>' . $font . '</option>';
             } 
        echo '</select>';
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function font_color_callback()
    {   
        printf(
            '<input type="text" name="ac_option_name[ac_font_color]" value="%s" class="ac-color-picker" >',
            isset( $this->options['ac_font_color'] ) ? $this->options['ac_font_color'] : ''
        ); 
    }
    /** 
     *  Main Color Picker
     */    
    public function ac_color_settings_field() 
    {    
        printf(
            '<input type="text" name="ac_option_name[ac_main_color_picker]" value="%s" class="ac-color-picker" >',
            isset( $this->options['ac_main_color_picker'] ) ? $this->options['ac_main_color_picker'] : ''
        );   
    }
    /** 
     *  Secondary
     */    
    public function ac_color_second_settings_field() 
    {    
        printf(
            '<input type="text" name="ac_option_name[ac_second_color_picker]" value="%s" class="ac-color-picker" >',
            isset( $this->options['ac_second_color_picker'] ) ? $this->options['ac_second_color_picker'] : ''
        );   
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function phone_number_callback()
    {
        printf(
            '<input type="text" id="ac_number" name="ac_option_name[ac_number]" value="%s"/>',
            isset( $this->options['ac_number'] ) ? esc_attr( $this->options['ac_number']) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function upper_cta_callback()
    {
        printf(
            '<textarea type="text" id="upper_cta" name="ac_option_name[upper_cta]" class="ac-textarea">%s</textarea>',
            isset( $this->options['upper_cta'] ) ? esc_attr( $this->options['upper_cta']) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function caption_callback()
    {
        printf(
            '<textarea type="text" id="caption" name="ac_option_name[caption]" class="ac-textarea">%s</textarea>',
            isset( $this->options['caption'] ) ? esc_attr( $this->options['caption']) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function main_image_callback()
    {   
        printf(
            '<span class="upload">
                <input type="hidden" id="ac_main_image" class="regular-text text-upload" name="ac_option_name[ac_main_image]" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/></br>
            </span>',
            isset( $this->options['ac_main_image'] ) ? esc_url( $this->options['ac_main_image']) : '',
            isset( $this->options['ac_main_image'] ) ? esc_url( $this->options['ac_main_image']) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function img_cta_callback()
    {   
        printf(
            '<textarea type="text" id="ac_img_cta" name="ac_option_name[ac_img_cta]" class="ac-textarea">%s</textarea>',
            isset( $this->options['ac_img_cta'] ) ? esc_attr( $this->options['ac_img_cta']) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function main_image_caption_callback()
    {   
        printf(
            '<span class="upload">
                <input type="hidden" id="ac_main_image_caption" class="regular-text text-upload" name="ac_option_name[ac_main_image_caption]" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/></br>
            </span>',
            isset( $this->options['ac_main_image_caption'] ) ? esc_url( $this->options['ac_main_image_caption']) : '',
            isset( $this->options['ac_main_image_caption'] ) ? esc_url( $this->options['ac_main_image_caption']) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function form_callback()
    {   
        printf(
            '<span class="upload">
                <input type="hidden" id="ac_form_image" class="regular-text text-upload" name="ac_option_name[ac_form_image]" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/></br>
            </span>',
            isset( $this->options['ac_form_image'] ) ? esc_url( $this->options['ac_form_image']) : '',
            isset( $this->options['ac_form_image'] ) ? esc_url( $this->options['ac_form_image']) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function footer_bg_callback()
    {   
        printf(
            '<span class="upload">
                <input type="hidden" id="ac_footer_image" class="regular-text text-upload" name="ac_option_name[ac_footer_image]" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/></br>
            </span>',
            isset( $this->options['ac_footer_image'] ) ? esc_url( $this->options['ac_footer_image']) : '',
            isset( $this->options['ac_footer_image'] ) ? esc_url( $this->options['ac_footer_image']) : ''
        );
    } 
}
if( is_admin() )
    $ac_settings_page = new AcSettingsPage();