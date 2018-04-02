<?php namespace MinistryPlatform;
/**
 * Plugin Name: Ministry Platform Data Access
 * Description: A wordpress plugin wrapper for accessing Ministry Platform
 * utilizing the the ministry platform API
 * Version: 2.1.0
 */

use MinistryPlatform\Templates\groupList;
use MinistryPlatformAPI\MinistryPlatformAPI as MP;

// Setup autoloading of supporting classes
require_once __DIR__ . '/vendor/autoload.php';


// Load the configuration for the admin menu and supporting environment functions
require_once('MP_Admin_Menu.php');
mpLoadConnectionParameters();

/* This is where you put your wonderful code to build your shortcodes  */

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

            $events = $mp->table('Groups')
                ->select("Groups.Group_Name,Congregation_ID_Table.Congregation_Name,Ministry_ID_Table.Ministry_Name,Group_Type_ID_Table.Group_Type,Groups.Start_Date,Groups.End_Date,Parent_Group_Table.Group_Name AS [Parent Group], Groups.Group_ID")
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