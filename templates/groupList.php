<?php namespace MinistryPlatform\Templates;

use MinistryPlatform\Templates\baseTemplate as baseTemplate;


class groupList extends baseTemplate
{

    public static function render($events){

	    $content = '<div class="mpapi-featured-events">';

	    foreach ( $events as $event ) {
		    $content .= '
                    <div class="mpapi-child-event">
                        <a href="/event-details?id=' . $event['Event_ID'] . '">
                            <img class="mpapi-event-image" src="'. getenv('MP_API_ENDPOINT') . '/files/' . $event[ 'Image_ID' ] . '">
                        </a>
                        <p>' . $event['Event_Title'] . '</p>
                    </div>
                ';
	    }

	    $content .= "</div>";

	    return $content;

    }

}



