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



