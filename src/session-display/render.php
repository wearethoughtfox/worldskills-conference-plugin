<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>

<?php
   if (empty($attributes['postId']) || !is_numeric($attributes['postId'])) {
    return 'Please enter a valid session post ID.';
}

$post_id = intval($attributes['postId']);
$post = get_post($post_id);

$event_date = get_post_meta($post_id, 'event_date', true);
$event_time_start = get_post_meta($post_id, 'event_time', true); // Start time
$event_time_end = get_post_meta($post_id, 'event_time_end', true); // End time



// Check if the post exists and is of the custom post type 'session'
if (!$post || get_post_type($post_id) !== 'session') {
    return 'No session found with the given ID or incorrect post type.';
}

// Get the session type taxonomy terms associated with the post
$session_types = wp_get_post_terms($post_id, 'session-type', array("fields" => "names"));
if (is_wp_error($session_types) || empty($session_types)) {
    $session_type_display = 'No session type assigned.';
} else {
    $session_type_display = implode(', ', $session_types); // Join multiple terms with a comma
}

// Get the title of the post
$post_title = get_the_title($post_id);
$post_url = get_permalink($post_id);

echo "<p>" . esc_html($session_type_display) . "</p>";

echo "<h1>" . esc_html($post_title) . "</h1>";

echo "<p>" . esc_html($post_url) . "</p>";

if (!empty($event_date) && !empty($event_time_start) && !empty($event_time_end)) {
    $timezone = new DateTimeZone('Europe/Paris'); // Timezone
    $start_datetime = new DateTime($event_date . 'T' . $event_time_start, $timezone);
    $end_datetime = new DateTime($event_date . 'T' . $event_time_end, $timezone);

    $start_iso_datetime = $start_datetime->format(DateTime::ATOM); // ISO 8601 format
    $end_iso_datetime = $end_datetime->format(DateTime::ATOM);

    // Correct format for full date and start time
    $formatted_start_time = $start_datetime->format('d F Y \a\t H:i'); // Localized date and start time format

    // Format end time without GMT offset
    $formatted_end_time = $end_datetime->format('H:i'); // Only the time without timezone info

    // Output the HTML
    echo '<time datetime="' . esc_attr($start_iso_datetime) . '" data-format="d MMMM yyyy \'at\' HH:mm" data-time-zone="Europe/Paris">' . esc_html($formatted_start_time) . '</time> – ';
    echo '<time datetime="' . esc_attr($end_iso_datetime) . '" data-format="HH:mm" data-time-zone="Europe/Paris">' . esc_html($formatted_end_time) . '</time>';
}



?>



