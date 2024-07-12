<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>



<?php

if (empty($attributes['startTime']) || empty($attributes['endTime'])) {
    return 'Start time or end time is missing.';
}

$start_time = sanitize_text_field($attributes['startTime']);
$end_time = sanitize_text_field($attributes['endTime']);

// Convert start time and end time to DateTime objects
$start_datetime = new DateTime($start_time);
$end_datetime = new DateTime($end_time);



if (empty($attributes['scheduleDate'])) {
    echo 'Please provide a valid schedule date.';
    return;
}

// Sanitize and retrieve the schedule date from attributes
$schedule_date = sanitize_text_field($attributes['scheduleDate']);
$time_color = esc_attr($attributes['timeColor']);

if (!function_exists('sessions_all_get_icon_filename_for_tag')) {
    // Map session tags to icon filenames
function sessions_all_get_icon_filename_for_tag($tag_name) {
    $icon_map = array(
        'apprenticeships' => 'WorldSkills_Conference_Icon_Apprenticeships.png',
        'competitions-of-the-future' => 'WorldSkills_Conference_Icon_Competitions-of-the-future.png',  
        'digital-and-ai' => 'WorldSkills_Conference_Icon_Digital-and-AI.png',
        'employers' => 'WorldSkills_Conference_Icon_Employers.png', 
        'excellence-in-tvet' => 'WorldSkills_Conference_Icon_Excellence-in-TVET.png',  
        'global-agenda' => 'WorldSkills_Conference_Icon_Global-agenda.png',  
        'green' => 'WorldSkills_Conference_Icon_Green.png', 
        'showcase' => 'WorldSkills_Conference_Icon_Showcase.png',  
        'social-justice' => 'WorldSkills_Conference_Icon_Social-Justice.png',
        'youth' => 'WorldSkills_Conference_Icon_Youth.png',
    );

    $tag_name = strtolower($tag_name);
    return isset($icon_map[$tag_name]) ? $icon_map[$tag_name] : '';
}
}


// Get attachment ID by filename
if (!function_exists('get_attachment_id_by_filename')) {
function get_attachment_id_by_filename($filename) {
    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_wp_attached_file',
                'value' => $filename,
                'compare' => 'LIKE'
            )
        )
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        return $query->posts[0]->ID;
    }

    return null;
}
}

if (!function_exists('get_icon_data_for_tag')) {
    function get_icon_data_for_tag($tag_name) {
        $filename = sessions_all_get_icon_filename_for_tag($tag_name);
        
        if (!empty($filename)) {
            $attachment_id = get_attachment_id_by_filename($filename);
            
            if ($attachment_id) {
                $image_data = wp_get_attachment_image_src($attachment_id, 'thumbnail');
                if ($image_data) {
                    return array(
                        'url' => $image_data[0],
                        'width' => $image_data[1],
                        'height' => $image_data[2]
                    );
                }
            }
        }
        
        return null;
    }
}




// The arguments for WP_Query
$args = array(
    'post_type' => 'session',  // Your custom post type name
    'posts_per_page' => -1,    // -1 to fetch all posts
    'post_status' => 'publish', // Only fetch published posts
    'meta_query' => array(
        array(
            'key' => 'event_date', // Replace with your actual meta key for event date
            'value' => $schedule_date,
            'compare' => '=', // Adjust compare operator as needed ('=' for exact match)
            'type' => 'DATE'
        )
    )
);

// Create a new WP_Query instance
$session_query = new WP_Query($args);

// Check if there are any posts to display
$sessions = array();
if ($session_query->have_posts()) {
    while ($session_query->have_posts()) {
        $session_query->the_post();
        
        // Get custom field values
        $track = get_post_meta(get_the_ID(), 'track', true);
        $track_end = get_post_meta(get_the_ID(), 'track_end', true);
        $event_time = str_replace(':', '', get_post_meta(get_the_ID(), 'event_time', true)); 
        $event_time_end = str_replace(':', '', get_post_meta(get_the_ID(), 'event_time_end', true)); 

        $tags = wp_get_post_terms(get_the_ID(), 'session-tags');

        
        // Store session details in an array
        $sessions[] = array(
            'id' => get_the_ID(),
            'title' => get_the_title(),
            'permalink' => get_permalink(),
            'track' => $track,
            'track_end' => $track_end,
            'event_time' => $event_time,
            'event_time_end' => $event_time_end,
            'excerpt' => get_the_excerpt(),
            'tags' => $tags,
        );
    }
} else {
    echo '<p>No sessions found.</p>'; // Message if no posts found
}

// Gather all unique tags
$all_tags = array();
foreach ($sessions as $session) {
    if (!empty($session['tags']) && is_array($session['tags'])) {
        foreach ($session['tags'] as $tag) {
            if (!isset($all_tags[$tag->term_id])) {
                $all_tags[$tag->term_id] = $tag;
            }
        }
    }
}

// Restore original post data, important if using multiple queries
wp_reset_postdata();


?>

<div class="schedule schedule-<?php echo esc_attr($attributes['scheduleDate']); ?>">

    <?php
// Query terms from the custom taxonomy 'session-location'
$terms = get_terms(array(
    'taxonomy' => 'session-location',  
    'hide_empty' => false,  
    'orderby' => 'id',  
    'order' => 'ASC'    
));

// Check if there are any terms
if (!empty($terms) && !is_wp_error($terms)) {

    // Counter for grid-row values
    $grid_row_counter = 1;

    // Loop through each term
    foreach ($terms as $term) {
        // Output markup for each term
        echo '<span class="track-slot has-small-font-size" aria-hidden="true" style="grid-column: track-' .  $grid_row_counter . '; grid-row: tracks;">' . esc_html($term->name) . '</span>';
        
        // Increment the grid row counter
        $grid_row_counter++;
    }

} 


?>



<?php
// Generate the CSS for grid-template-rows
$grid_template_rows = "[tracks] auto";
$current_time = clone $start_datetime;
while ($current_time <= $end_datetime) {
    $time_label = $current_time->format('Hi');
    $grid_template_rows .= " [time-$time_label] 1fr";
    $current_time->add(new DateInterval('PT15M')); // Increment by 15 minutes
}
$grid_template_rows .= ";"; // End the CSS rule

?>

<style>
.schedule-<?php echo esc_attr($schedule_date); ?> {
    grid-template-rows: <?php echo $grid_template_rows; ?>;
}

.schedule-<?php echo esc_attr($schedule_date); ?> .time-slot {
    color: <?php echo $time_color; ?>;
}

.schedule-<?php echo esc_attr($schedule_date); ?> .track-slot {
    color: <?php echo $time_color; ?>;
}
</style>



<?php



// Generate time slots every 30 minutes
$current_time = clone $start_datetime;
while ($current_time <= $end_datetime) {
    $time_slot = $current_time->format('H:i'); // Format as "09:00", "09:30", etc.
    $grid_row = 'time-' . str_replace(':', '', $time_slot);

    // Output time slot heading
    echo '<h2 class="time-slot has-small-font-size" style="grid-row: ' . $grid_row . ';">' . date('H:i', strtotime($time_slot)) . '</h2>' . "\n";


    // Loop through the sessions and output those that fall within the current time slot
    foreach ($sessions as $session) {
        // Convert session times to DateTime objects for comparison
        $session_start_time = DateTime::createFromFormat('Hi', $session['event_time']);
        $session_end_time = DateTime::createFromFormat('Hi', $session['event_time_end']);


        // Check if the session starts within the current time slot
        if ($session_start_time->format('YmdHis') === $current_time->format('YmdHis')) {
            $track = esc_attr($session['track']);
            $track_end = esc_attr($session['track_end']);
            
            if (!empty($track_end)) {
                $grid_column = $track . "-start / " . $track_end . "-end"; 
            } else {
                $grid_column = "$track";
            }
            
            $grid_row_start = "time-" . $session['event_time'];
            $grid_row_end = "time-" . $session['event_time_end'];

            // Get the background and link color from attributes
            $background_color = esc_attr($attributes['backgroundColor']);
            $link_color = esc_attr($attributes['linkColor']);
            $meta_color = esc_attr($attributes['metaColor']);

            echo "<div class='session session-{$session['id']} $grid_column' style='grid-column: $grid_column; grid-row: $grid_row_start / $grid_row_end; --session-bg-color: $background_color; --session-link-color: $link_color;'>";
            echo '<h3 class="session-title wp-block-heading has-standard-font-size"><a href="' . esc_url($session['permalink']) . '">' . esc_html($session['title']) . '</a></h3>';
            echo '<div class="session-meta" style="--session-meta-color: ' . $meta_color . ';">';
            echo '<span class="session-time has-small-font-size">' . date('H:i', strtotime($session['event_time'])) . ' - ' . date('H:i', strtotime($session['event_time_end'])) . '</span>';          
            echo '</div>';
            echo '<div class="session-excerpt has-small-font-size">' . wp_kses_post($session['excerpt']) . '</div>';

            if (!empty($session['tags'])) {
                echo '<div class="session-icons">';
                foreach ($session['tags'] as $tag) {
                    $icon_data = get_icon_data_for_tag($tag->slug);
                    echo '<div class="session-icon-outer">';
                    if ($icon_data) {
                        echo '<img src="' . esc_url($icon_data['url']) . '" 
                                   alt="' . esc_attr($tag->name) . ' icon" 
                                   class="session-icon"
                                   width="' . esc_attr($icon_data['width']) . '"
                                   height="' . esc_attr($icon_data['height']) . '"
                                   popovertarget="popover-' . esc_attr($tag->slug) . '"
                                   popovertargetaction="show">';
                        echo '<div id="popover-' . esc_attr($tag->slug) . '" popover>' . esc_html($tag->name) . '</div>';
                    }
                    echo '</div>';
                }
                echo '</div>';
            }

            echo '</div>';
            
        }
    }

    // Add 30 minutes to the current time
    $current_time->add(new DateInterval('PT15M'));
}
?>


</div>