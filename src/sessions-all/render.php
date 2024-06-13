<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>

<div class="schedule">
<span class="track-slot" aria-hidden="true" style="grid-column: track-1; grid-row: tracks;">Plenary Hall</span>
	<span class="track-slot" aria-hidden="true" style="grid-column: track-2; grid-row: tracks;">Dome A</span>
	<span class="track-slot" aria-hidden="true" style="grid-column: track-3; grid-row: tracks;">Dome B</span>
	<span class="track-slot" aria-hidden="true" style="grid-column: track-4; grid-row: tracks;">Networking area</span>



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

// Array to hold the generated time slots
$time_slots = array();

// Generate time slots every 30 minutes
$current_time = clone $start_datetime;
while ($current_time <= $end_datetime) {
    $time_slot = $current_time->format('H:i'); // Format as "09:00", "09:30", etc.
    $time_slots[] = $time_slot;
    $current_time->add(new DateInterval('PT30M')); // Add 30 minutes
}

// Output the time slots with the specified HTML structure
foreach ($time_slots as $time_slot) {
    // Convert time to grid-row format like "time-0900" for 09:00
    $grid_row = 'time-' . str_replace(':', '', $time_slot);

    // Output HTML
    echo '<h2 class="time-slot" style="grid-row: ' . $grid_row . ';">' . $time_slot . '</h2>' . "\n";
}
?>


<?php
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
if ($session_query->have_posts()) {

    // Loop through the posts
    while ($session_query->have_posts()) {
        $session_query->the_post();

        // Get custom field values
        $track = get_post_meta(get_the_ID(), 'track', true);
        $event_time = str_replace(':', '', get_post_meta(get_the_ID(), 'event_time', true)); 
        $event_time_end = str_replace(':', '', get_post_meta(get_the_ID(), 'event_time_end', true)); 

        // CSS Grid placement
        $grid_column = esc_attr("$track");
        $grid_row_start = esc_attr("time-$event_time");
        $grid_row_end = esc_attr("time-$event_time_end");

        // Display session details
        echo "<div class='session $grid_column' style='grid-column: $grid_column; grid-row: $grid_row_start / $grid_row_end;'>";
        echo '<h3 class="session-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
        echo '<span class="session-time">' . esc_html($event_time) . ' - ' . esc_html($event_time_end) . '</span>';
        echo '</div>';
    }

} else {
    echo '<p>No sessions found.</p>'; // Message if no posts found
}

// Restore original post data, important if using multiple queries
wp_reset_postdata();
?>
</div>

