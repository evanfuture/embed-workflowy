<?php
/**
 * Plugin Name: Embed Workflowy
 * Plugin URI: http://evanpayne.com/embed-workflowy
 * Description: A simple plugin to embed your Workflowy list in the Wordpress Admin.
 * Version: 1.0.1
 * Author: Evan Payne
 * Author URI: http://evanpayne.com
 * License: GPL2
 */

/**
Enable Embed Worflowy Settings
**/
class EmbedWorkflowySettings
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
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Embed Workflowy Settings',
            'Embed Workflowy',
            'manage_options',
            'embed-workflowy-settings',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'workflowy_id' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Embed Workflowy Settings</h2>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'workflowy_option_group' );
                do_settings_sections( 'embed-workflowy-settings' );
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
            'workflowy_option_group', // Option group
            'workflowy_id', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'embed-workflowy-settings' // Page
        );

        add_settings_field(
            'id_number', // ID
            'ID Number', // Title
            array( $this, 'id_number_callback' ), // Callback
            'embed-workflowy-settings', // Page
            'setting_section_id' // Section
        );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = sanitize_text_field( $input['id_number'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function id_number_callback()
    {
        printf(
            '<input type="text" id="id_number" name="workflowy_id[id_number]" value="%s" />',
            isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
        );
    }

}

if( is_admin() )
    $embed_workflowy_settings_page = new EmbedWorkflowySettings();

/**
Enable Embed Worflowy Page in Dashboard
**/

  add_action('admin_menu', 'register_workflowy_pages');

function register_workflowy_pages() {
    add_submenu_page( 'index.php', 'Workflowy', 'Workflowy', 'manage_options', 'workflowy', 'workflowy_callback' );
}

function workflowy_callback() {
  $workflowy_array = get_option( 'workflowy_id' );
  foreach ($workflowy_array as $key_name => $key_value) {
    $workflowy_id = $key_value;
  }
  if($workflowy_id == '') {
    echo 'You can bookmark a specific list by entering its id on the <a href="options-general.php?page=embed-workflowy-settings">Settings Page</a>.';
  }
  echo '<div class="wrap">';
    echo '<iframe src="https://workflowy.com/#/' . $workflowy_id . '" width="100%" height="800" frameborder="0"></iframe>';
  echo '</div>';
}
