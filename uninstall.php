<?php
/**
 * Uninstall routine for OpenPanel Self-Hosted.
 *
 * Deletes all plugin options from the WordPress database
 * when the plugin is uninstalled via the WordPress backend.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

$opsh_options = array(
    'opsh_enabled',
    'opsh_client_id',
    'opsh_api_url',
    'opsh_script_local',
    'opsh_track_screen_views',
    'opsh_track_outgoing_links',
    'opsh_track_attributes',
    'opsh_session_replay',
    'opsh_session_replay_mask_inputs',
    'opsh_no_track_logged_in',
    'opsh_respect_dnt',
    'opsh_disable_debug',
);

foreach ( $opsh_options as $opsh_option ) {
    delete_option( $opsh_option );
}
