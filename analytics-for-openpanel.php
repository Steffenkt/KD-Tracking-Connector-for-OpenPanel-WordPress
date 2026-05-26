<?php
/**
 * Plugin Name:       Tracking Connector for OpenPanel
 * Plugin URI:        https://github.com/Steffenkt/Analytics-for-OpenPanel-WordPress
 * Description:       Integrates OpenPanel Analytics into WordPress. Supports self-hosted instances and the official cloud. Configurable under Settings → OpenPanel.
 * Version:           1.0.1
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Steffen Kaster
 * Author URI:        https://github.com/Steffenkt
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tracking-connector-for-openpanel
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'OPSH_VERSION',    '1.0.1' );
define( 'OPSH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// ---------------------------------------------------------------------------
// 1. Activation: Set default options
// ---------------------------------------------------------------------------

register_activation_hook( __FILE__, 'opsh_activate' );

function opsh_activate() {
    $defaults = array(
        'opsh_enabled'                    => false,
        'opsh_client_id'                  => '',
        'opsh_api_url'                    => '',
        'opsh_script_local'               => true,
        'opsh_track_screen_views'         => true,
        'opsh_track_outgoing_links'       => true,
        'opsh_track_attributes'           => true,
        'opsh_session_replay'             => false,
        'opsh_session_replay_mask_inputs' => true,
        'opsh_no_track_logged_in'         => true,
        'opsh_respect_dnt'                => true,
        'opsh_disable_debug'              => true,
    );
    foreach ( $defaults as $key => $value ) {
        if ( get_option( $key ) === false ) {
            add_option( $key, $value );
        }
    }
}

// ---------------------------------------------------------------------------
// 2. Load translations
// ---------------------------------------------------------------------------
// WordPress 4.6+ loads plugin translations automatically when the plugin
// is hosted on WordPress.org. No manual load_plugin_textdomain() call needed.

// ---------------------------------------------------------------------------
// 3. Admin menu
// ---------------------------------------------------------------------------

add_action( 'admin_menu', 'opsh_admin_menu' );

function opsh_admin_menu() {
    add_options_page(
        __( 'OpenPanel Analytics', 'tracking-connector-for-openpanel' ),
        __( 'OpenPanel', 'tracking-connector-for-openpanel' ),
        'manage_options',
        'tracking-connector-for-openpanel',
        'opsh_settings_page'
    );
}

// ---------------------------------------------------------------------------
// 4. Register Settings API
// ---------------------------------------------------------------------------

add_action( 'admin_init', 'opsh_admin_init' );

function opsh_admin_init() {

    // --- General ---
    add_settings_section(
        'opsh_section_general',
        __( 'Allgemein', 'tracking-connector-for-openpanel' ),
        '__return_false',
        'tracking-connector-for-openpanel'
    );

    register_setting( 'opsh_settings_group', 'opsh_enabled',   array( 'sanitize_callback' => 'opsh_sanitize_bool' ) );
    add_settings_field(
        'opsh_enabled',
        __( 'OpenPanel aktivieren', 'tracking-connector-for-openpanel' ),
        'opsh_field_checkbox',
        'tracking-connector-for-openpanel',
        'opsh_section_general',
        array( 'key' => 'opsh_enabled', 'description' => __( 'Tracking-Script im Frontend ausgeben.', 'tracking-connector-for-openpanel' ) )
    );

    register_setting( 'opsh_settings_group', 'opsh_client_id', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    add_settings_field(
        'opsh_client_id',
        __( 'Client ID', 'tracking-connector-for-openpanel' ),
        'opsh_field_text',
        'tracking-connector-for-openpanel',
        'opsh_section_general',
        array( 'key' => 'opsh_client_id', 'type' => 'text', 'description' => __( 'Die Client-ID deiner OpenPanel-Anwendung.', 'tracking-connector-for-openpanel' ) )
    );

    register_setting( 'opsh_settings_group', 'opsh_api_url', array( 'sanitize_callback' => 'esc_url_raw' ) );
    add_settings_field(
        'opsh_api_url',
        __( 'API-URL (Self-Hosted)', 'tracking-connector-for-openpanel' ),
        'opsh_field_text',
        'tracking-connector-for-openpanel',
        'opsh_section_general',
        array( 'key' => 'opsh_api_url', 'type' => 'url', 'description' => __( 'Nur bei Self-Hosting angeben, z.&nbsp;B. https://openpanel.example.com/api. Leer lassen für die offizielle Cloud.', 'tracking-connector-for-openpanel' ) )
    );

    // --- Script delivery ---
    add_settings_section(
        'opsh_section_script',
        __( 'Script-Auslieferung', 'tracking-connector-for-openpanel' ),
        '__return_false',
        'tracking-connector-for-openpanel'
    );

    register_setting( 'opsh_settings_group', 'opsh_script_local', array( 'sanitize_callback' => 'opsh_sanitize_bool' ) );
    add_settings_field(
        'opsh_script_local',
        __( 'op1.js lokal ausliefern', 'tracking-connector-for-openpanel' ),
        'opsh_field_checkbox',
        'tracking-connector-for-openpanel',
        'opsh_section_script',
        array( 'key' => 'opsh_script_local', 'description' => __( 'Script aus dem Plugin-Ordner laden (kein externer Request zu openpanel.dev). Deaktiviert: Script wird von https://openpanel.dev/op1.js geladen.', 'tracking-connector-for-openpanel' ) )
    );

    // --- Tracking options ---
    add_settings_section(
        'opsh_section_tracking',
        __( 'Tracking-Optionen', 'tracking-connector-for-openpanel' ),
        '__return_false',
        'tracking-connector-for-openpanel'
    );

    register_setting( 'opsh_settings_group', 'opsh_track_screen_views', array( 'sanitize_callback' => 'opsh_sanitize_bool' ) );
    add_settings_field(
        'opsh_track_screen_views',
        __( 'Seitenaufrufe (trackScreenViews)', 'tracking-connector-for-openpanel' ),
        'opsh_field_checkbox',
        'tracking-connector-for-openpanel',
        'opsh_section_tracking',
        array( 'key' => 'opsh_track_screen_views', 'description' => __( 'Seitenaufrufe automatisch tracken.', 'tracking-connector-for-openpanel' ) )
    );

    register_setting( 'opsh_settings_group', 'opsh_track_outgoing_links', array( 'sanitize_callback' => 'opsh_sanitize_bool' ) );
    add_settings_field(
        'opsh_track_outgoing_links',
        __( 'Ausgehende Links (trackOutgoingLinks)', 'tracking-connector-for-openpanel' ),
        'opsh_field_checkbox',
        'tracking-connector-for-openpanel',
        'opsh_section_tracking',
        array( 'key' => 'opsh_track_outgoing_links', 'description' => __( 'Klicks auf externe Links automatisch tracken.', 'tracking-connector-for-openpanel' ) )
    );

    register_setting( 'opsh_settings_group', 'opsh_track_attributes', array( 'sanitize_callback' => 'opsh_sanitize_bool' ) );
    add_settings_field(
        'opsh_track_attributes',
        __( 'HTML-Attribute (trackAttributes)', 'tracking-connector-for-openpanel' ),
        'opsh_field_checkbox',
        'tracking-connector-for-openpanel',
        'opsh_section_tracking',
        array( 'key' => 'opsh_track_attributes', 'description' => __( 'Tracking über data-track-Attribute an HTML-Elementen.', 'tracking-connector-for-openpanel' ) )
    );

    // --- Session Replay ---
    add_settings_section(
        'opsh_section_replay',
        __( 'Session Replay', 'tracking-connector-for-openpanel' ),
        '__return_false',
        'tracking-connector-for-openpanel'
    );

    register_setting( 'opsh_settings_group', 'opsh_session_replay', array( 'sanitize_callback' => 'opsh_sanitize_bool' ) );
    add_settings_field(
        'opsh_session_replay',
        __( 'Session Replay aktivieren', 'tracking-connector-for-openpanel' ),
        'opsh_field_checkbox',
        'tracking-connector-for-openpanel',
        'opsh_section_replay',
        array( 'key' => 'opsh_session_replay', 'description' => __( 'Mausbewegungen, Scrollverhalten und Klicks aufzeichnen.', 'tracking-connector-for-openpanel' ) )
    );

    register_setting( 'opsh_settings_group', 'opsh_session_replay_mask_inputs', array( 'sanitize_callback' => 'opsh_sanitize_bool' ) );
    add_settings_field(
        'opsh_session_replay_mask_inputs',
        __( 'Formularfelder maskieren (maskAllInputs)', 'tracking-connector-for-openpanel' ),
        'opsh_field_checkbox',
        'tracking-connector-for-openpanel',
        'opsh_section_replay',
        array( 'key' => 'opsh_session_replay_mask_inputs', 'description' => __( 'Empfohlen: Verhindert, dass Eingaben in Formularfelder im Replay sichtbar sind.', 'tracking-connector-for-openpanel' ) )
    );

    // --- Privacy & Debug ---
    add_settings_section(
        'opsh_section_privacy',
        __( 'Datenschutz &amp; Debug', 'tracking-connector-for-openpanel' ),
        '__return_false',
        'tracking-connector-for-openpanel'
    );

    register_setting( 'opsh_settings_group', 'opsh_no_track_logged_in', array( 'sanitize_callback' => 'opsh_sanitize_bool' ) );
    add_settings_field(
        'opsh_no_track_logged_in',
        __( 'Eingeloggte Nutzer nicht tracken', 'tracking-connector-for-openpanel' ),
        'opsh_field_checkbox',
        'tracking-connector-for-openpanel',
        'opsh_section_privacy',
        array( 'key' => 'opsh_no_track_logged_in', 'description' => __( 'Admins und eingeloggte Benutzer werden nicht getrackt – verhindert verfälschte Statistiken.', 'tracking-connector-for-openpanel' ) )
    );

    register_setting( 'opsh_settings_group', 'opsh_respect_dnt', array( 'sanitize_callback' => 'opsh_sanitize_bool' ) );
    add_settings_field(
        'opsh_respect_dnt',
        __( 'Do Not Track (DNT) respektieren', 'tracking-connector-for-openpanel' ),
        'opsh_field_checkbox',
        'tracking-connector-for-openpanel',
        'opsh_section_privacy',
        array( 'key' => 'opsh_respect_dnt', 'description' => __( 'Nutzer mit aktivem DNT-Browser-Header werden nicht getrackt.', 'tracking-connector-for-openpanel' ) )
    );

    register_setting( 'opsh_settings_group', 'opsh_disable_debug', array( 'sanitize_callback' => 'opsh_sanitize_bool' ) );
    add_settings_field(
        'opsh_disable_debug',
        __( 'Bei WP_DEBUG deaktivieren', 'tracking-connector-for-openpanel' ),
        'opsh_field_checkbox',
        'tracking-connector-for-openpanel',
        'opsh_section_privacy',
        array( 'key' => 'opsh_disable_debug', 'description' => __( 'Kein Tracking auf lokalen/Staging-Umgebungen wenn WP_DEBUG aktiv ist.', 'tracking-connector-for-openpanel' ) )
    );
}

// ---------------------------------------------------------------------------
// 5. Render settings page
// ---------------------------------------------------------------------------

function opsh_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'opsh_settings_group' );
            do_settings_sections( 'tracking-connector-for-openpanel' );
            submit_button( __( 'Änderungen speichern', 'tracking-connector-for-openpanel' ) );
            ?>
        </form>
    </div>
    <?php
}

// ---------------------------------------------------------------------------
// 6. Field render callbacks
// ---------------------------------------------------------------------------

function opsh_field_checkbox( $args ) {
    $key   = $args['key'];
    $value = (bool) get_option( $key );
    $desc  = isset( $args['description'] ) ? $args['description'] : '';
    printf(
        '<input type="hidden" name="%1$s" value="0" />' .
        '<label><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s /> %3$s</label>',
        esc_attr( $key ),
        checked( $value, true, false ),
        wp_kses( $desc, array( 'code' => array(), 'em' => array(), 'strong' => array() ) )
    );
}

function opsh_field_text( $args ) {
    $key   = $args['key'];
    $value = get_option( $key, '' );
    $type  = isset( $args['type'] ) ? $args['type'] : 'text';
    $desc  = isset( $args['description'] ) ? $args['description'] : '';
    printf(
        '<input type="%1$s" id="%2$s" name="%2$s" value="%3$s" class="regular-text" />' .
        '<p class="description">%4$s</p>',
        esc_attr( $type ),
        esc_attr( $key ),
        esc_attr( $value ),
        wp_kses( $desc, array( 'code' => array(), 'em' => array(), 'strong' => array(), 'a' => array( 'href' => array(), 'target' => array() ) ) )
    );
}

// ---------------------------------------------------------------------------
// 7. Sanitize helpers
// ---------------------------------------------------------------------------

function opsh_sanitize_bool( $value ) {
    // PHP treats '0' as falsy and '1' as truthy – correct for checkbox values.
    return (bool) $value;
}

// ---------------------------------------------------------------------------
// 8. Output script in frontend
// ---------------------------------------------------------------------------

add_action( 'wp_enqueue_scripts', 'opsh_inject_script' );

function opsh_inject_script() {

    // Master switch: bail out if plugin is disabled
    if ( ! (bool) get_option( 'opsh_enabled', false ) ) {
        return;
    }

    // Skip tracking when WP_DEBUG is active
    if ( (bool) get_option( 'opsh_disable_debug', true ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        return;
    }

    // Skip tracking for logged-in users
    if ( (bool) get_option( 'opsh_no_track_logged_in', true ) && is_user_logged_in() ) {
        return;
    }

    $client_id = sanitize_text_field( get_option( 'opsh_client_id', '' ) );
    if ( empty( $client_id ) ) {
        return;
    }

    $api_url          = esc_url( get_option( 'opsh_api_url', '' ) );
    $script_local     = (bool) get_option( 'opsh_script_local', true );
    $track_screens    = (bool) get_option( 'opsh_track_screen_views', true );
    $track_outgoing   = (bool) get_option( 'opsh_track_outgoing_links', true );
    $track_attributes = (bool) get_option( 'opsh_track_attributes', true );
    $session_replay   = (bool) get_option( 'opsh_session_replay', false );
    $mask_inputs      = (bool) get_option( 'opsh_session_replay_mask_inputs', true );
    $respect_dnt      = (bool) get_option( 'opsh_respect_dnt', true );

    // Build init options array
    $options = array(
        'clientId'           => $client_id,
        'trackScreenViews'   => $track_screens,
        'trackOutgoingLinks' => $track_outgoing,
        'trackAttributes'    => $track_attributes,
    );

    if ( ! empty( $api_url ) ) {
        $options['apiUrl'] = $api_url;
    }

    if ( $session_replay ) {
        $options['sessionReplay'] = array(
            'enabled'       => true,
            'maskAllInputs' => $mask_inputs,
        );
    }

    $options_json = wp_json_encode( $options );

    // The DNT filter is a JS function and cannot be JSON-encoded.
    // It is appended via Object.assign() instead.
    if ( $respect_dnt ) {
        $init_call = "window.op('init', Object.assign(" . $options_json . ", {filter: function(){ return navigator.doNotTrack !== '1'; }}));";
    } else {
        $init_call = "window.op('init', " . $options_json . ");";
    }

    // phpcs:ignore WordPress.Security.EscapeOutput -- init_call is built from wp_json_encode and a hardcoded JS string
    $inline_js = "window.op=window.op||function(){var n=[];return new Proxy(function(){arguments.length&&n.push([].slice.call(arguments))},{get:function(t,r){return\"q\"===r?n:function(){n.push([r].concat([].slice.call(arguments)))}},has:function(t,r){return\"q\"===r}});}();\n" . $init_call;

    // Load op1.js from the plugin folder or from CDN
    if ( $script_local ) {
        $script_url = OPSH_PLUGIN_URL . 'assets/js/op1.js';
        $version    = OPSH_VERSION;
    } else {
        $script_url = 'https://openpanel.dev/op1.js';
        $version    = null;
    }

    wp_register_script(
        'opsh-tracking',
        $script_url,
        array(),
        $version,
        array(
            'strategy'  => 'defer',
            'in_footer' => false,
        )
    );

    wp_add_inline_script( 'opsh-tracking', $inline_js, 'before' );
    wp_enqueue_script( 'opsh-tracking' );
}
