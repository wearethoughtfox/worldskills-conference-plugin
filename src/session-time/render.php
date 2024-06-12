<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>

<?php
$post_id = get_the_ID(); // Get the current post ID
$event_date = get_post_meta($post_id, 'event_date', true);
$event_time_start = get_post_meta($post_id, 'event_time', true); // Start time
$event_time_end = get_post_meta($post_id, 'event_time_end', true); // End time
    
    if (!empty($event_date) && !empty($event_time_start) && !empty($event_time_end)) {
        $timezone = new DateTimeZone('Europe/Paris'); // Timezone
        $start_datetime = new DateTime($event_date . 'T' . $event_time_start, $timezone);
        $end_datetime = new DateTime($event_date . 'T' . $event_time_end, $timezone);
    
        $start_iso_datetime = $start_datetime->format(DateTime::ATOM); // ISO 8601 format
        $end_iso_datetime = $end_datetime->format(DateTime::ATOM);
    
        // Correct format for full date and start time
        $formatted_start_time = $start_datetime->format('d F Y \a\t H:i'); // Localized date and start time format
    
        // Extract GMT offset and convert to simplified format (GMT+2)
    $offset = $start_datetime->getOffset() / 3600; // Get timezone offset in hours
    $gmt_offset = $offset > 0 ? "+$offset" : "$offset"; // Format as +2 or -2 etc.
    $formatted_end_time = $end_datetime->format('H:i') . " (GMT$gmt_offset)"; // Time with simplified GMT offset
    
        // Output the HTML
        echo '<time datetime="' . esc_attr($start_iso_datetime) . '" data-format="d MMMM yyyy \'at\' HH:mm" data-time-zone="Europe/Paris">' . esc_html($formatted_start_time) . '</time> – ';
        echo '<time datetime="' . esc_attr($end_iso_datetime) . '" data-format="HH:mm (\'GMT\'Z)" data-time-zone="Europe/Paris">' . esc_html($formatted_end_time) . '</time>';
    }
    
?>



