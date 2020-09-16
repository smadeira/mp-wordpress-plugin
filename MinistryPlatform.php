<?php namespace MinistryPlatform;
/**
 * Plugin Name: Ministry Platform Data Access
 * Description: A wordpress plugin wrapper for accessing Ministry Platform
 * utilizing the the ministry platform API
 * Version: 2.1.0
 */

use MinistryPlatform\Templates\featuredEvents;
use MinistryPlatform\Templates\featuredEventsWithDate;
use MinistryPlatform\Templates\eventDetails;
use MinistryPlatformAPI\MinistryPlatformTableAPI as MP;

// Setup autoloading of supporting classes
require_once __DIR__ . '/vendor/autoload.php';


// Load the configuration for the admin menu and supporting environment functions
require_once('MP_Admin_Menu.php');
mpLoadConnectionParameters();

/* This is where you put your wonderful code to build your shortcodes  */
add_shortcode('mpapi_feature_events', ['MinistryPlatform\MP_API_SHORTCODES', 'mpapi_featured_events_sc']);
add_shortcode('mpapi_event_details', ['MinistryPlatform\MP_API_SHORTCODES', 'mpapi_event_details_sc']);
add_shortcode('mpapi_nextsteps', ['MinistryPlatform\MP_API_SHORTCODES', 'mpapi_nextsteps_sc']);
add_shortcode('mpapi_baptism', ['MinistryPlatform\MP_API_SHORTCODES', 'mpapi_baptism_sc']);

class MP_API_SHORTCODES
{
    /**
     * Shortcode that will pull featured events for the home page
     *
     * @param array $atts
     * @param null $content
     * @return null|string
     */
    public static function mpapi_featured_events_sc($atts = [], $content = null)
    {
        $mp = new MP();

        // Authenticate to get access token required for API calls
        if ($mp->authenticate()) {

            $events = $mp->table('Events')
                ->select("Event_ID, Event_Title, Event_Start_Date, Meeting_Instructions, Event_End_Date, Location_ID_Table.[Location_Name], dp_fileUniqueId AS Image_ID")
                ->filter('((Events.Event_Start_Date between dateadd(minute,-60,getdate()) and dateadd(day, 30, getdate())) OR (Events.Featured_Event_Date < getdate() and Events.Event_Start_Date >= getdate())) AND Featured_On_Calendar = 1 and Visibility_Level_ID_Table.[Visibility_Level_ID] = 4 AND Event_Type_ID_Table.[Event_Type_ID] IN (1,14,19,21) AND Events.[_Approved] = 1 AND ISNULL(Events.[Cancelled], 0) = 0')
                ->orderBy('Event_Start_Date')
                ->get();
           
            $content = featuredEvents::render($events);
        }
        // always return
        return $content;
    }

   /**
    * Shortcode that will pull event details for a given event
    *
    * @param array $atts
    * @param null $content
    * @return null|string
    */
   public static function mpapi_event_details_sc($atts = [], $content = null)
   {
       $mp = new MP();

       // Authenticate to get access token required for API calls
       if ( $mp->authenticate() ) {
           $event = $mp->table('Events')
                        ->select("Event_ID, Event_Title, Events.Meeting_Instructions, Events.Description, Event_Start_Date, Event_End_Date, External_Registration_URL, Featured_On_Calendar, Event_Type_ID_Table.[Event_Type],
                        Event_Type_ID_Table.[Event_Type_ID], Primary_Contact_Table.[Nickname], Primary_Contact_Table.[First_Name], Primary_Contact_Table.[Last_Name],
                        Primary_Contact_Table.[Email_Address], Visibility_Level_ID_Table.[Visibility_Level_ID], Location_ID_Table.[Location_Name],
                        Location_ID_Table_Address_ID_Table.[Address_Line_1], Location_ID_Table_Address_ID_Table.[Address_Line_2], Location_ID_Table_Address_ID_Table.[City],
                        Location_ID_Table_Address_ID_Table.[State/Region], Location_ID_Table_Address_ID_Table.[Postal_Code],
                        Registration_Active, Registration_Start, Registration_End, Online_Registration_Product, dp_fileUniqueId AS Image_ID")
                        ->filter('Event_ID = ' . $_GET['id'] )
                        ->first();

            $opps = $mp->table('Opportunities')
                          ->select("count(*) as [Opportunities]")
                          ->filter("Add_To_Event = " . $event['Event_ID'])
                          ->first();

          $event['Opportunities'] = $opps['Opportunities'];


            $content = eventDetails::render($event);
       }
       // always return
       return $content;

   }
   

   /**
    * Shortcode that will pull next step dates
    *  (beginning of configurable event query)
    *
    * @param array $atts
    * @param null  $content
    * @return null|string
    */
   public static function mpapi_nextsteps_sc($atts = [], $content = null)
   {
       $mp = new MP();

       // Authenticate to get access token required for API calls
       if ( $mp->authenticate() ) {
           $events = $mp->table('Events')
                        ->select("Event_ID, Event_Title, Event_Start_Date, Event_End_Date, Location_ID_Table.[Location_Name], dp_fileUniqueId AS Image_ID")
                        ->filter('(Events.Event_Start_Date between getdate() and dateadd(day, 60, getdate())) AND Event_Title like \'Next Steps%\' and Visibility_Level_ID_Table.[Visibility_Level_ID] = 4 AND Events.[_Approved] = 1 AND ISNULL(Events.[Cancelled], 0) = 0')
                        ->orderBy('Event_Start_Date')
                        ->get();

           $content = featuredEventsWithDate::render($events);
       }
       // always return
       return $content;

   }


   public static function mpapi_baptism_sc($atts = [], $content = null)
   {
      $mp = new MP();

      // Authenticate to get access token required for API calls
       if ( $mp->authenticate() ) {
           $events = $mp->table('Events')
                        ->select("Event_ID, Event_Title, Event_Start_Date, Event_End_Date, Location_ID_Table.[Location_Name], dp_fileUniqueId AS Image_ID")
                        ->filter("(getdate() between Events.Registration_Start AND Events.Registration_End) AND Event_Title like 'Believer%' and Visibility_Level_ID_Table.[Visibility_Level_ID] = 4 AND Events.[_Approved] = 1 AND ISNULL(Events.[Cancelled], 0) = 0")
                        ->orderBy('Event_Start_Date')
                        ->get();

           $content = featuredEventsWithDate::render($events);
       }
       // always return
       return $content;

   }
}