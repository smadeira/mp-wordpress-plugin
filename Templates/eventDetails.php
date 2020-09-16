<?php  namespace MinistryPlatform\Templates;

use MinistryPlatform\Templates\baseTemplate;

class eventDetails extends baseTemplate
{

    public static function render($event){

	    $startMonth = date('F', strtotime($event[ 'Event_Start_Date' ]));
	    $startDay = date('j', strtotime($event[ 'Event_Start_Date' ]));
	    $startTime = date('g:i a', strtotime($event[ 'Event_Start_Date' ]));

	    $endMonth = date('F', strtotime($event[ 'Event_End_Date' ]));
	    $endDay = date('j', strtotime($event[ 'Event_End_Date' ]));
	    $endTime = date('g:i a', strtotime($event[ 'Event_End_Date' ]));

	    if ($startDay == $endDay) {
	    	$dateString = $startMonth . ' ' . $startDay . ', ' .$startTime . ' - ' . $endTime;    
	    } else {
    		$dateString = $startMonth . ' ' . $startDay .  ' - ' . $endMonth . ' ' . $endDay;
	    }

	    // $dateString = $startMonth . ' ' . $startDay . ', ' .$startTime . ' - ';	    
	    // if ($startDay != $endDay) $dateString .= $endMonth . ' ' . $endDay . ', ';

	    // $dateString .= $endTime;

	    $content = <<<markup
		<div class="content">
			<div class="row">
				<div class="x-text "><a href="/events"><<< All Events</a></div>
			</div>
			<div class="row">
				<div class="title">
					<div class="x-text cs-ta-center mpapi-event-title"><h1>{$event['Event_Title']}</h1></div>
				</div>
			</div>
			<!-- <div class="mpapi-event-image cs-ta-center"><img src="https://connect.mygcc.org/ministryplatformapi/files/{$event['Image_ID']}"></div> -->
			<div class="cs-ta-center"><img src="https://connect.mygcc.org/ministryplatformapi/files/{$event['Image_ID']}" width="800"></div>

			<div class="h3 mpapi-event-time">$dateString</div>

			<div class="mpapi-event-description"><p>{$event['Description']}</p></div>

			<div class="mpapi-event-description"><p>{$event['Meeting_Instructions']}</p></div>

			<div class="mpapi-event-location-block">
				<h4>Location</h4>
				<p><span class="mpapi-event-loc-name">{$event['Location_Name']}</span><br>
					{$event['Address_Line_1']}<br>
					{$event['City']} {$event['State/Region']} {$event['Postal_Code']}
				</p>
			</div>
			<div class="mpapi-event-contact">
				<h4>Contact</h4>
				<p>{$event['Nickname']} {$event['Last_Name']} - <a href="mailto:{$event['Email_Address']}">{$event['Email_Address']}</a>
				</p>
			</div>
				<div class="mpapi-event-signup">{{signup}}{{opportunities}}</div>	
		</div>
markup;


		if (static::hasMpRegistration($event)) {
	   		$signup = static::mpRegistrationLink($event);

		} else if (static::hasExternalRegistration($event) ) {
			$signup = static::externalRegistrationLink($event);

		} else {
	    	$signup = null;
		
	    }	    


	    if ($event['Opportunities'] > 0 ) {
	    	$opportunities = '<a class="x-btn x-btn-global" href="https://connect.mygcc.org/portal/opportunity_finder.aspx?filter=event:' . $event[ 'Event_ID' ] . '">Volunteer</a>';
	    } else {
	    	$opportunities = null;
	    }
	    

	    $content = str_replace(['{{opportunities}}','{{signup}}'] , [$opportunities, $signup], $content);

	    return $content;

    }

    public static function hasMpRegistration($mp_event)
	{
		return ( 
			$mp_event[ 'Registration_Active' ] 
			&& (strtotime($mp_event[ 'Registration_Start' ]) <= time()) 
			&& (strtotime($mp_event[ 'Registration_End' ]) >= time()) 
			&& $mp_event[ 'Online_Registration_Product' ] );

	}

	public static function hasExternalRegistration($mp_event)
	{
		return ( 
			$mp_event[ 'Registration_Active' ] 
			&& (strtotime($mp_event[ 'Registration_Start' ]) <= time()) 
			&& (strtotime($mp_event[ 'Registration_End' ]) >= time()) 
			&& $mp_event[ 'External_Registration_URL' ] );

	}


	public static function mpRegistrationLink($mp_event)
	{
		return '<br><a class="x-btn x-btn-global" href="https://connect.mygcc.org/portal/event_signup.aspx?id=' . $mp_event[ 'Event_ID' ] . '" target="_blank">Sign Up</a>';
	}


	public static function externalRegistrationLink($mp_event)
	{
		return '<br><a class="x-btn x-btn-global" href="' . $mp_event[ 'External_Registration_URL' ] . '" target="_blank">Sign Up</a>';
	}

}