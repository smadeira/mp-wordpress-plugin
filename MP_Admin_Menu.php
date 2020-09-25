<?php namespace MinistryPlatform;

/**
 * This function introduces a single plugin menu option into the WordPress 'Plugins'
 * menu.
 */

add_action('admin_menu', 'MinistryPlatform\ministry_platform_plugin_menu');
function ministry_platform_plugin_menu()
{

    add_plugins_page(
        'Ministry Platform Plugin',         // The title to be displayed in the browser window for this page.
        'MP Plugin',                        // The text to be displayed for this menu item
        'administrator',                    // Which type of users can see this menu item
        'ministry_platform_plugin_options', // The unique ID - that is, the slug - for this menu item
        'MinistryPlatform\ministry_platform_plugin_display'  // The name of the function to call when rendering the page for this menu
    );

} // end sandbox_example_theme_menu

/**
 * Renders a simple page to display for the plugin menu defined above.
 */
function ministry_platform_plugin_display()
{
?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">
        <!-- Add the icon to the page -->
        <div id="icon-themes" class="icon32"></div>
        <h2>Ministry Platform Plugin Options</h2>
        <p class="description">Here you can set the parameters to authenticate to and use the Ministry Platform API</p>
        <!-- Make a call to the WordPress function for rendering errors when settings are saved. -->
        <?php settings_errors(); ?>

        <!-- Create the form that will be used to render our options -->
        <form method="post" action="options.php">
            <?php settings_fields( 'ministry_platform_plugin_options' ); ?>
            <?php do_settings_sections( 'ministry_platform_plugin_options' ); ?>
            <?php submit_button(); ?>
        </form>
    </div> <!-- /.wrap -->


<?php
} // end sandbox_plugin_display


add_action('admin_init', 'MinistryPlatform\ministry_platform_initialize_plugin_options');


function ministry_platform_initialize_plugin_options()
{

    // If the options don't exist, add them
    if( false == get_option( 'ministry_platform_plugin_options' ) ) {
        add_option( 'ministry_platform_plugin_options' );
    } // end if


    // First, we register a section. This is necessary since all future options must belong to one.
    add_settings_section(
        'ministry_platform_settings_section',                           // ID used to identify this section and with which to register options
        'API Configuration Options',                                  // Title to be displayed on the administration page
        'MinistryPlatform\ministry_platform_general_options_callback',  // Callback used to render the description of the section
        'ministry_platform_plugin_options'                              // Page on which to add this section of options
    );

    // Next, we will introduce the fields for the configuration information.
    add_settings_field(
        'MP_API_ENDPOINT',                                  // ID used to identify the field throughout the theme
        'API Endpoint',                                     // The label to the left of the option interface element
        'MinistryPlatform\mp_api_endpoint_callback',        // The name of the function responsible for rendering the option interface
        'ministry_platform_plugin_options',                 // The page on which this option will be displayed
        'ministry_platform_settings_section',               // The name of the section to which this field belongs
        [                                                   // The array of arguments to pass to the callback. In this case, just a description.
            'ex: https://my.mychurch.org/ministryplatformapi'
        ]
    );

    add_settings_field(
        'MP_OAUTH_DISCOVERY_ENDPOINT',
        'Oauth Discovery Endpoint',
        'MinistryPlatform\mp_oauth_discovery_callback',
        'ministry_platform_plugin_options',
        'ministry_platform_settings_section',
        [
            'ex: https://my.mychurch.org/ministryplatform/oauth'
        ]
    );

    add_settings_field(
        'MP_CLIENT_ID',
        'MP Client ID',
        'MinistryPlatform\mp_client_id_callback',
        'ministry_platform_plugin_options',
        'ministry_platform_settings_section',
        [
            'The Client ID is defined in MP on the API Client page.'
        ]
    );

    add_settings_field(
        'MP_CLIENT_SECRET',
        'MP Client Secret',
        'MinistryPlatform\mp_client_secret_callback',
        'ministry_platform_plugin_options',
        'ministry_platform_settings_section',
        [
            'The Client Secret is defined in MP on the API Client page.'
        ]
    );

    add_settings_field(
        'MP_API_SCOPE',
        'Scope',
        'MinistryPlatform\mp_api_scope_callback',
        'ministry_platform_plugin_options',
        'ministry_platform_settings_section',
        [
            'Will usually be http://www.thinkministry.com/dataplatform/scopes/all'
        ]
    );

    // Finally, we register the fields with WordPress
    register_setting(
        'ministry_platform_plugin_options',
        'ministry_platform_plugin_options'
    );


} // end ministry_platform_initialize_plugin_options

function ministry_platform_general_options_callback()
{
    echo '<p>The following parameters are required to authenticate to the API using oAuth and then execute API calls to Ministry Platform.</p>';
}


function get_option_value($key, $options) {

    // If the options don't exist, return empty string
    if (! is_array($options)) return '';

    // If the key is in the array, return the value, else return empty string.

    return  array_key_exists($key, $options) ? $options[$key] : '';

}

function mp_api_endpoint_callback($args)
{
    $options = get_option('ministry_platform_plugin_options');


    $opt = get_option_value('MP_API_ENDPOINT', $options);


    // Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
    $html = '<input type="text" id="MP_API_ENDPOINT" name="ministry_platform_plugin_options[MP_API_ENDPOINT]" value="'. $opt . '" size="60"/>';

    // Here, we will take the first argument of the array and add it to a label next to the checkbox
    $html .= '<label for="MP_API_ENDPOINT"> ' . $args[0] . '</label>';

    echo $html;

} // end mp_api_endpoint_callback

function mp_oauth_discovery_callback($args)
{
    $options = get_option('ministry_platform_plugin_options');

    $opt = get_option_value('MP_OAUTH_DISCOVERY_ENDPOINT', $options);

    // Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
    $html = '<input type="text" id="MP_OAUTH_DISCOVERY_ENDPOINT" name="ministry_platform_plugin_options[MP_OAUTH_DISCOVERY_ENDPOINT]" value="'. $opt . '" size="60"/>';

    // Here, we will take the first argument of the array and add it to a label next to the checkbox
    $html .= '<label for="MP_OAUTH_DISCOVERY_ENDPOINT"> ' . $args[0] . '</label>';

    echo $html;
} // end mp_oauth_discovery_callback

function mp_client_id_callback($args)
{
    $options = get_option('ministry_platform_plugin_options');

    $opt = get_option_value('MP_CLIENT_ID', $options);

    // Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
    $html = '<input type="text" id="MP_CLIENT_ID" name="ministry_platform_plugin_options[MP_CLIENT_ID]" value="'. $opt . '" size="60"/>';

    // Here, we will take the first argument of the array and add it to a label next to the checkbox
    $html .= '<label for="MP_CLIENT_ID"> ' . $args[0] . '</label>';

    echo $html;
} // end mp_client_id_callback

function mp_client_secret_callback($args)
{
    $options = get_option('ministry_platform_plugin_options');

    $opt = get_option_value('MP_CLIENT_SECRET', $options);

    // Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
    $html = '<input type="text" id="MP_CLIENT_SECRET" name="ministry_platform_plugin_options[MP_CLIENT_SECRET]" value="'. $opt . '" size="60"/>';

    // Here, we will take the first argument of the array and add it to a label next to the checkbox
    $html .= '<label for="MP_CLIENT_SECRET"> ' . $args[0] . '</label>';

    echo $html;
} // end mp_client_secret_callback

function mp_api_scope_callback($args)
{
    $options = get_option('ministry_platform_plugin_options');

    $opt = get_option_value('MP_API_SCOPE', $options);

    // Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field
    $html = '<input type="text" id="MP_API_SCOPE" name="ministry_platform_plugin_options[MP_API_SCOPE]" value="'. $opt . '" size="60"/>';

    // Here, we will take the first argument of the array and add it to a label next to the checkbox
    $html .= '<label for="MP_API_SCOPE"> ' . $args[0] . '</label>';

    echo $html;
} // end mp_api_scope_callback


/**
 * Get oAuth and API connection parameters from the database
 *
 */
function mpLoadConnectionParameters()
{
    // If no options available then just return - it hasn't been setup yet
    if ( !$options = get_option('ministry_platform_plugin_options', '') ) return;

    foreach ($options as $option => $value) {
        $envString = $option . '=' . $value;
        putenv($envString);
    }

}