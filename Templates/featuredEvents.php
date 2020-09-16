<?php  namespace MinistryPlatform\Templates;

use MinistryPlatform\Templates\baseTemplate;

class featuredEvents extends baseTemplate
{

    public static function render($events){

	    $content = '<div class="mpapi-featured-events">';

	    foreach ( $events as $event ) {
		    $content .= '
                    <div class="mpapi-child-event">
                        <a href="/event-details?id=' . $event['Event_ID'] . '">
                            <img class="mpapi-event-image" src="https://connect.mygcc.org/ministryplatformapi/files/' . $event[ 'Image_ID' ] . '">
                        </a>
                        <p>' . $event['Event_Title'] . '</p>
                    </div>
                ';
	    }

	    $content .= "</div>";

	    return $content;

    }

}



