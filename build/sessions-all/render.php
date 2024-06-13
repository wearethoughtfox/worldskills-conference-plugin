<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>

<div class="schedule">

    <?php
// Query terms from the custom taxonomy 'session-location'
$term_ids = array(55, 56, 57, 58);
$terms = get_terms(array(
    'taxonomy' => 'session-location',  // Replace with your custom taxonomy slug
    'hide_empty' => false,  
    'include' => $term_ids,
    'orderby' => 'include', 
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
// Example start time and end time (you would get these from your attributes or elsewhere)
if (empty($attributes['startTime']) || empty($attributes['endTime'])) {
    return 'Start time or end time is missing.';
}

$start_time = sanitize_text_field($attributes['startTime']);
$end_time = sanitize_text_field($attributes['endTime']);

// Convert start time and end time to DateTime objects
$start_datetime = new DateTime($start_time);
$end_datetime = new DateTime($end_time);

// Ensure $attributes['scheduleDate'] is defined and not empty
if (empty($attributes['scheduleDate'])) {
    echo 'Please provide a valid schedule date.';
    return;
}

// Sanitize and retrieve the schedule date from attributes
$schedule_date = sanitize_text_field($attributes['scheduleDate']);

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
        $event_time = str_replace(':', '', get_post_meta(get_the_ID(), 'event_time', true)); 
        $event_time_end = str_replace(':', '', get_post_meta(get_the_ID(), 'event_time_end', true)); 
        
        // Store session details in an array
        $sessions[] = array(
            'id' => get_the_ID(),
            'title' => get_the_title(),
            'permalink' => get_permalink(),
            'track' => $track,
            'event_time' => $event_time,
            'event_time_end' => $event_time_end,
        );
    }
} else {
    echo '<p>No sessions found.</p>'; // Message if no posts found
}

// Restore original post data, important if using multiple queries
wp_reset_postdata();

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
        if ($session_start_time == $current_time) {
            $track = esc_attr($session['track']);
            $grid_column = "$track";
            $grid_row_start = "time-" . $session['event_time'];
            $grid_row_end = "time-" . $session['event_time_end'];

            echo "<div class='session session-{$session['id']} $grid_column' style='grid-column: $grid_column; grid-row: $grid_row_start / $grid_row_end;'>";
            echo '<h3 class="session-title wp-block-heading has-custom-mid-grey-color has-text-color has-link-color  has-standard-font-size"><a href="' . esc_url($session['permalink']) . '">' . esc_html($session['title']) . '</a></h3>';
            echo '<span class="session-time has-small-font-size">' . date('H:i', strtotime($session['event_time'])) . ' - ' . date('H:i', strtotime($session['event_time_end'])) . '</span>';
            echo '</div>';
        }
    }

    // Add 30 minutes to the current time
    $current_time->add(new DateInterval('PT30M'));
}
?>


</div>

