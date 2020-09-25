## MP Wordpress Plugin

This package provides a basic plugin that makes use of the Ministry Platform API to access 
Ministry Platform data. It works by using the smadeira/ministry-platform-api to connect to
the REST API. The returned data can be manipulated and sent to a template for rendering and
output to a page in WordPress via shortcode.

The example code provided will output a list of active groups to a simple table wherever you embed the
[mpapi_group_list] shortcode.

## Installation
Installation consists of cloning the repository to your computer, using composer to pull all dependencies for the API, 
Create a plugin folder in your WP installation, copying the files to the plugin folder, activating the plugin, entering the 
MP API configuraiton parameters and creating a page to use your shortcode.  We'll take that one step at a time.

### Assumptions
I am assuming that you know how to use GitHub to clone a repository and use <a href="http://getcomposer.org">Composer</a> for php package management.  I also assume you are familiar with WordPress packages and the like.  This code is offered as-is and it may or may not work in your situation.  I'll try to help as much as I can but I can't provide full support for this code.  It is meant to get you started on your WP development journey with Ministry Platform.

### Clone the Repository
Go to the location where you want the plugin code to reside and from there git clone the repository.

```
git clone https://github.com/smadeira/mp-wordpress-plugin.git
```

### Composer Update
Use composer to update your code.  It will download the latest version of the MP REST API wrapper and its dependencies.
```
composer update
```

### Create a MinistryPlatform plugin
I found it easiest to keep things separated and under control by using this process to create your plugin.
<ol><li>In your Wordpress installation, create a MinistryPlatform folder under wp-content/plugins.  You should see other plugins already in the plugins folder.</li>
	<li>Copy the files from the mp-wordpress-plugin folder to this new MinistryPlatform plugin folder</li>
	<li>From the admin dashboard you should be able to click Plugins and see Minsitry Platform Data Access as a new plugin.  you need to activate it to continue.</li>
</ol>

### Enter MP and oAuth credentials
To use the API you need to provide the credentials for authenticating to the REST API as well as the API endpoint you want to get data from.  To do this, hover on the Plugins menu
option in the left column of the WP Admin dashboard.  An option for MP Plugin should be in the list.  Click MP Plugin to get to the configuration screen.  It will ask for the following:
<ul><li>API Endpoint</li><li>Oauth Discovery Endpoint</li><li>MP Client ID</li><li>MP Client Secret</li><li>Scope</li></ul>

Examples are given on the configuration page and some of the data will come from your MP installation.

### Create a Page
You will need a page or place on an existing page to embed your shortcode.  Where you put it is up to you. To use this example you can use shortcode [mpapi_group_list] to see the table of groups.

## Sample Code
Included in the plugin is a sample to get you started and verify that your installation is working correctly.

### Basic Plumbing
The top of the MinistryPlatform.php file has the required code to access the API wrapper and include the templates you need.  Other than adding more / different templates, you should
not have to make any changes to this.  

```php
<?php namespace MinistryPlatform;
/**
 * Plugin Name: Ministry Platform Data Access
 * Description: A wordpress plugin wrapper for accessing Ministry Platform
 * utilizing the the ministry platform API
 * Version: 2.1.0
 */

use MinistryPlatform\Templates\groupList;
use MinistryPlatformAPI\MinistryPlatformTableAPI as MP;

// Setup autoloading of supporting classes
require_once __DIR__ . '/vendor/autoload.php';


// Load the configuration for the admin menu and supporting environment functions
require_once('MP_Admin_Menu.php');
mpLoadConnectionParameters();
```

### Data Access
The MinistryPatform.php file has a class that contains the methods for getting data from MP.  This code will get some group information for active groups.  It will the send the
data to the groupList template for rendering as HTML.  The template will return formatted HTML that this method will return to WordPress for rendering. For each query, you can create a new method in this class and add_shortcode to register it with WordPress.  Note that the shortcode for this particular example is: [mpapi_group_list]

```php
class MP_API_SHORTCODES
{
    /**
     * Shortcode that will pull featured events for the home page
     *
     * @param array $atts
     * @param null $content
     * @return null|string
     */
    public static function mpapi_group_list_sc($atts = [], $content = null)
    {
        $mp = new MP();

        // Authenticate to get access token required for API calls
        if ($mp->authenticate()) {

            $groups = $mp->table('Groups')
                ->select("Groups.Group_Name,Congregation_ID_Table.Congregation_Name,Ministry_ID_Table.Ministry_Name,Group_Type_ID_Table.Group_Type,Groups.Start_Date,Groups.End_Date,Parent_Group_Table.Group_Name AS [Parent_Group], Groups.Group_ID")
                ->filter('(Groups.End_Date IS NULL OR Groups.End_Date >= GetDate())')
                ->orderBy('Group_Name')
                ->get();

            $content = groupList::render($groups);
        }
        // always return
        return $content;
    }
}

add_shortcode('mpapi_group_list', ['MinistryPlatform\MP_API_SHORTCODES', 'mpapi_group_list_sc']);
```

### Templates
The templates folder is where you can put all of your html templates for rendering the output. This is the sample group list template that just builds a simple table and returns the HTML
to be rendered on the page. The important things are to include the namespace and use statement on all templates you make.  baseTemplate is where you can save common functions. You can build the content as you see fit and return it to the calling method.

<b>BIG NOTE:</b> When you create a new template file you will need to run 
```
composer dump-auto -o
```
to regenerate the autoload file.  Otherwise the code won't know where to find your template.<b>End of BIG NOTE</b>.

```php
<?php namespace MinistryPlatform\Templates;

use MinistryPlatform\Templates\baseTemplate as baseTemplate;

/**
 *  Basic Template that accepts an array of groups
 *  
 *  Output is a simple HTML table of data as a proof of concept
 *
 */
class groupList extends baseTemplate
{
    public static function render($groups){

        // echo "<pre>"; print_r($groups); echo "</pre>"; die();

        $content = '
            <div class="mpapi-featured-events"> 
                <table>
                    <thead>
                    <th>Name</th>
                    <th>Congregation</th>
                    <th>Ministry Name</th>
                    <th>Group Type</th>
                    <th>Parent Group</th>
                    <th>ID</th>
                    </thead><tbody>';
        
        
                foreach( $groups as $group ) {
                    $content .= '<tr>';
                    $content .= '<td>' .  $group['Group_Name']  . '</td>';
                    $content .= '<td>' .  $group['Congregation_Name']  . '</td>';
                    $content .= '<td>' .  $group['Ministry_Name']  . '</td>';
                    $content .= '<td>' .  $group['Group_Type']  . '</td>';
                    $content .= '<td>' .  $group['Parent_Group']  . '</td>';
                    $content .= '<td>' .  $group['Group_ID']  . '</td>';
                    $content .= '</tr>';
                }

        $content .= '</tbody></table>';
        $content .= "</div>";

        return $content;
    }
}
```
