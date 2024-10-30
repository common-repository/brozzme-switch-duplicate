<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

function brozzme_switch_duplicate_uninstall(){
    $options = get_option('bsd_settings');

    if($options['bsd_suppress_options_on_desactivation'] == 'true'){
        delete_option('bsd_settings');
        delete_option('bsd_switcher_settings');
        delete_option('bsd_duplicate_settings');

    }
}
