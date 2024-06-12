<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>

<h2 id="schedule-heading">Conference Schedule</h2>
<div class="schedule" aria-labelledby="schedule-heading">
<span class="track-slot" aria-hidden="true" style="grid-column: track-1; grid-row: tracks;">Plenary Hall</span>
	<span class="track-slot" aria-hidden="true" style="grid-column: track-2; grid-row: tracks;">Dome A</span>
	<span class="track-slot" aria-hidden="true" style="grid-column: track-3; grid-row: tracks;">Dome B</span>
	<span class="track-slot" aria-hidden="true" style="grid-column: track-4; grid-row: tracks;">Networking area</span>

	<h2 class="time-slot" style="grid-row: time-0900;">9:00</h2>

	<h2 class="time-slot" style="grid-row: time-0930;">9:30</h2>

	<h2 class="time-slot" style="grid-row: time-1000;">10:00am</h2>

	<h2 class="time-slot" style="grid-row: time-1030;">10:30am</h2>

	<h2 class="time-slot" style="grid-row: time-1100;">11:00am</h2>

	<h2 class="time-slot" style="grid-row: time-1130;">11:30am</h2>


    <?php
// The arguments for WP_Query
$args = array(
    'post_type' => 'session',  // Your custom post type name
    'posts_per_page' => -1,    // -1 to fetch all posts
    'post_status' => 'publish' // Only fetch published posts
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

