<?php

/**
 * Created by PhpStorm.
 * User: benoti
 * Date: 20/02/2017
 * Time: 18:20
 */
defined( 'ABSPATH' ) || exit;

class brozzme_switch_duplicate_settings
{

    /**
     * brozzme_switch_duplicate_settings constructor.
     */
    public function __construct(){
        add_action('admin_init', array($this, '_settings_init'));
        add_action('admin_menu', array($this, '_add_admin_menu'));

        $this->general_options = get_option('bsd_settings');
    }

    /**
     *
     */
    public function _add_admin_menu(){

        add_submenu_page( BFSL_PLUGINS_DEV_GROUPE_ID,
            __('Switcher & Duplicate', B7E_SD_TEXT_DOMAIN),
            __('Switcher & Duplicate', B7E_SD_TEXT_DOMAIN),
            'manage_options',
            B7E_SD_SLUG,
            array( $this, 'options_bsd_page' )
        );
    }

    /**
     *
     */
    public function _settings_init(){
        register_setting('bsdGeneralSettings', 'bsd_settings');
        register_setting('bsdSwitcherSettings', 'bsd_switcher_settings');
        register_setting('bsdDuplicateSettings', 'bsd_duplicate_settings');
        
        //general settings
        add_settings_section(
            'bsdGeneralSettings_section',
            __('General settings option for Brozzme Switch and Duplicate', B7E_SD_TEXT_DOMAIN),
            array($this, 'bsdGeneralSettings_section_callback'),
            'bsdGeneralSettings'
        );

        add_settings_field(
            'bsd_enable',
            __('Enable whole plugin',B7E_SD_TEXT_DOMAIN),
            array($this, 'bsd_enable_render'),
            'bsdGeneralSettings',
            'bsdGeneralSettings_section'
        );
        add_settings_field(
            'bsd_enable_switcher',
            __('Enable post-type switcher', B7E_SD_TEXT_DOMAIN),
            array($this, 'bsd_enable_switcher_render'),
            'bsdGeneralSettings',
            'bsdGeneralSettings_section'
        );
        add_settings_field(
            'bsd_enable_duplicate',
            __('Enable post duplicate', B7E_SD_TEXT_DOMAIN),
            array($this, 'bsd_enable_duplicate_render'),
            'bsdGeneralSettings',
            'bsdGeneralSettings_section'
        );
        add_settings_field(
            'bsd_suppress_options_on_desactivation',
            __('Erase settings on plugin deletion', B7E_SD_TEXT_DOMAIN),
            array($this, 'bsd_suppress_options_on_desactivation_render'),
            'bsdGeneralSettings',
            'bsdGeneralSettings_section'
        );

        /* Switcher settings */
        add_settings_section(
            'bsdSwitcherSettings_section',
            __('Post types Switcher settings', B7E_SD_TEXT_DOMAIN),
            array($this, 'bsdSwitcherSettings_section_callback'),
            'bsdSwitcherSettings'
        );

        add_settings_field(
            'bsd_post_type',
            __( 'Enable switcher for Post type', B7E_SD_TEXT_DOMAIN ),
            array($this, 'bsd_post_type_render'),
            'bsdSwitcherSettings',
            'bsdSwitcherSettings_section'
        );
        add_settings_field(
            'bsd_post_type_column',
            __( 'Add Post type column', B7E_SD_TEXT_DOMAIN ),
            array($this, 'bsd_post_type_column_render'),
            'bsdSwitcherSettings',
            'bsdSwitcherSettings_section'
        );
        /* end of Switcher settings */
        /* Duplicate settings */
        add_settings_section(
            'bsdDuplicateSettings_section',
            __('Post types Duplicate settings', B7E_SD_TEXT_DOMAIN),
            array($this, 'bsdDuplicateSettings_section_callback'),
            'bsdDuplicateSettings'
        );
        add_settings_field(
            'bsd_copy_taxonomy',
            __( 'Duplicate post taxonomy', B7E_SD_TEXT_DOMAIN ),
            array($this, 'bsd_copy_taxonomy_render'),
            'bsdDuplicateSettings',
            'bsdDuplicateSettings_section'
        );
        add_settings_field(
            'bsd_copy_custom_fields',
            __( 'Duplicate post custom fields', B7E_SD_TEXT_DOMAIN ),
            array($this, 'bsd_copy_custom_fields_render'),
            'bsdDuplicateSettings',
            'bsdDuplicateSettings_section'
        );
        /* end of duplicate settings */
    }

    /**
     *
     */
    public function bsdGeneralSettings_section_callback(){

    }

    /**
     *
     */
    public function bsdDuplicateSettings_section_callback(){

    }

    /**
     *
     */
    public function bsdSwitcherSettings_section_callback(){

    }

    /**
     *
     */
    public function options_bsd_page(){
        ?>
        <div class="wrap">
            <h2>Brozzme Switch and Duplicate posts</h2>
            <?php

            $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_settings';
            ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=<?php echo B7E_SD_SLUG;?>&tab=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General settings', B7E_SD_TEXT_DOMAIN );?></a>
                <a href="admin.php?page=<?php echo B7E_SD_SLUG;?>&tab=switcher_settings" class="nav-tab <?php echo $active_tab == 'switcher_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Switcher settings', B7E_SD_TEXT_DOMAIN );?></a>
                <a href="admin.php?page=<?php echo B7E_SD_SLUG;?>&tab=duplicate_settings" class="nav-tab <?php echo $active_tab == 'duplicate_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Duplicate settings', B7E_SD_TEXT_DOMAIN );?></a>

                <a href="admin.php?page=<?php echo B7E_SD_SLUG;?>&tab=help_options" class="nav-tab <?php echo $active_tab == 'help_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Help', B7E_SD_TEXT_DOMAIN );?></a>
            </h2>

            <form action='options.php' method='post'>
                <?php
                if( $active_tab == 'help_options' ) {
                    settings_fields('BrozzmeHelp');

                    $this->brozzme_switch_duplicate_help_page();
                }
                elseif( $active_tab == 'switcher_settings' ) {
                    settings_fields('bsdSwitcherSettings');
                    do_settings_sections('bsdSwitcherSettings');
                    submit_button();
                }
                elseif( $active_tab == 'duplicate_settings' ) {
                    settings_fields('bsdDuplicateSettings');
                    do_settings_sections('bsdDuplicateSettings');
                    submit_button();
                }

                else {
                    settings_fields('bsdGeneralSettings');
                    do_settings_sections('bsdGeneralSettings');
                    submit_button();
                }

                ?>

            </form>
        </div>
        <?php
    }

    /* General settings */

    /**
     *
     */
    public function bsd_enable_render(){
        ?>
        <select name="bsd_settings[bsd_enable]">
        <option value="true" <?php selected( $this->general_options['bsd_enable'], 'true' ); ?>><?php _e( 'Yes', B7E_SD_TEXT_DOMAIN );?></option>
        <option value="false" <?php selected( $this->general_options['bsd_enable'], 'false' ); ?>><?php _e( 'No', B7E_SD_TEXT_DOMAIN );?></option>

        </select><?php

    }

    /**
     *
     */
    public function bsd_enable_switcher_render(){
        ?>
        <select name="bsd_settings[bsd_enable_switcher]">
        <option value="true" <?php selected( $this->general_options['bsd_enable_switcher'], 'true' ); ?>><?php _e( 'Yes', B7E_SD_TEXT_DOMAIN );?></option>
        <option value="false" <?php selected( $this->general_options['bsd_enable_switcher'], 'false' ); ?>><?php _e( 'No', B7E_SD_TEXT_DOMAIN );?></option>

        </select><?php
    }

    /**
     *
     */
    public function bsd_enable_duplicate_render(){
        ?>
        <select name="bsd_settings[bsd_enable_duplicate]">
        <option value="true" <?php selected( $this->general_options['bsd_enable_duplicate'], 'true' ); ?>><?php _e( 'Yes', B7E_SD_TEXT_DOMAIN );?></option>
        <option value="false" <?php selected( $this->general_options['bsd_enable_duplicate'], 'false' ); ?>><?php _e( 'No', B7E_SD_TEXT_DOMAIN );?></option>

        </select><?php
    }

    /**
     *
     */
    public function bsd_suppress_options_on_desactivation_render(){
        ?>
        <select name="bsd_settings[bsd_suppress_options_on_desactivation]">
        <option value="true" <?php selected( $this->general_options['bsd_suppress_options_on_desactivation'], 'true' ); ?>><?php _e( 'Yes', B7E_SD_TEXT_DOMAIN );?></option>
        <option value="false" <?php selected( $this->general_options['bsd_suppress_options_on_desactivation'], 'false' ); ?>><?php _e( 'No', B7E_SD_TEXT_DOMAIN );?></option>

        </select><?php
    }

    /* end general settings */

    /* Switcher settings */
    /**
     * @return mixed
     */
    public function _get_site_post_types(){
        $args = apply_filters( 'bsd_post_type_filter', array(
            'public'  => true,
            'show_ui' => true

        ));

        $website_post_types = get_post_types($args, 'objects', 'and'); // search for public and queriable cpt

        
        return $website_post_types;
    }

    /**
     * @return array
     */
    public function post_types_args(){
        $options = get_option('bsd_switcher_settings');

        $available_args = array(
            'public'=> true,
            'publicly_queryable'=> true,
            'exclude_from_search'=> false,
            'show_ui'=> true,
            'hierarchical'=> true,
            '_builtin'=> false,
        );
        // ask _builtin false but show page and post post-type

        $choosen_args = $options['bsd_post_types_args'];

        foreach ($available_args as $arg => $arg_value) {

        }

        return $available_args;
    }

    /**
     *
     */
    public function bsd_post_type_render(){
        $options = get_option( 'bsd_switcher_settings' );

        $website_post_types = $this->_get_site_post_types();

        $choosen_cpt = $options['bsd_post_type'];

        foreach ($website_post_types as $object => $post_type) {
           // echo '<pre>';var_dump($post_type);echo'</pre>';
            if($object == 'attachment'){
                continue;
            }
            if (in_array($post_type->name, $choosen_cpt) == $post_type->name) {
                ?>
                <div class="choiceCheck">
                    <input type="checkbox" name="bsd_switcher_settings[bsd_post_type][]"
                           value="<?php echo $post_type->name;?>"
                           checked/><label> <?php echo $post_type->label;?> <small>(<?php echo $post_type->name;?>)</small></label>
                </div>
                <?php
            }
            else {
                ?>
                <div class="choiceCheck">
                    <input type="checkbox" name="bsd_switcher_settings[bsd_post_type][]"
                           value="<?php echo $post_type->name;?>"/><label> <?php echo $post_type->label;?>  <small>(<?php echo $post_type->name;?>)</small></label>
                </div>
                <?php
            }

        }

    }

    /**
     * @return array
     */
    private function get_post_type_args() {

        return (array) apply_filters( 'bsd_post_type_filter', array(
            'public'  => true,
            'show_ui' => true
        ) );
    }

    /**
     *
     */
    public function bsd_post_type_column_render(){
        $options = get_option( 'bsd_switcher_settings' );
        ?>
        <select name="bsd_switcher_settings[bsd_post_type_column]">
        <option value="true" <?php selected( $options['bsd_post_type_column'], 'true' ); ?>><?php _e( 'Yes', B7E_SD_TEXT_DOMAIN );?></option>
        <option value="false" <?php selected( $options['bsd_post_type_column'], 'false' ); ?>><?php _e( 'No', B7E_SD_TEXT_DOMAIN );?></option>

        </select><?php

    }
    /* Duplicate settings */

    /**
     *
     */
    public function bsd_copy_taxonomy_render(){
        $options = get_option( 'bsd_duplicate_settings' );
        ?>
        <select name="bsd_duplicate_settings[bsd_copy_taxonomy]">
        <option value="true" <?php selected( $options['bsd_copy_taxonomy'], 'true' ); ?>><?php _e( 'Yes', B7E_SD_TEXT_DOMAIN );?></option>
        <option value="false" <?php selected( $options['bsd_copy_taxonomy'], 'false' ); ?>><?php _e( 'No', B7E_SD_TEXT_DOMAIN );?></option>

        </select><?php

    }

    /**
     *
     */
    public function bsd_copy_custom_fields_render(){
        $options = get_option( 'bsd_duplicate_settings' );
        ?>
        <select name="bsd_duplicate_settings[bsd_copy_custom_fields]">
        <option value="true" <?php selected( $options['bsd_copy_custom_fields'], 'true' ); ?>><?php _e( 'Yes', B7E_SD_TEXT_DOMAIN );?></option>
        <option value="false" <?php selected( $options['bsd_copy_custom_fields'], 'false' ); ?>><?php _e( 'No', B7E_SD_TEXT_DOMAIN );?></option>

        </select><?php

    }

    /**
     *
     */
    public function brozzme_switch_duplicate_help_page(){

        ?>
        <p>You will find link to duplicate your content when hovering the row actions area.</p>
        <p>The switcher select box is available on the quick edit screen and in a post (or other post-type page) in the postbox area.</p>
        <?php
    }
}