<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<?php
// Set the target post type for the query to 'session'
$target_post_type = 'session';

// Get the terms related to the current speaker post
$terms = get_the_terms($post->ID, 'conference');
if ($terms && !is_wp_error($terms)) {
    $term_ids = wp_list_pluck($terms, 'term_id');

    // Setup the query arguments to get sessions
    $args = array(
        'post_type' => $target_post_type,
        'tax_query' => array(
            array(
                'taxonomy' => 'conference',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ),
        ),
        'posts_per_page' => -1  // Retrieve all matching sessions
    );

    // Execute the query for sessions
    $related_sessions = new WP_Query($args);
    if ($related_sessions->have_posts()) {
        echo '<h3 class="wp-block-heading has-standard-font-size" style="font-weight:900; text-transform:uppercase">Sessions</h3><ul class="ws-date-list no-margin-block-start">';
        while ($related_sessions->have_posts()) : $related_sessions->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        endwhile;
        echo '</ul>';
    } else {
        echo '<p>No related sessions found.</p>';
    }
    wp_reset_postdata();  // Reset post data after custom query
}
?>
