<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<?php
// Set the target post type for the query to 'speaker'
$target_post_type = 'speaker';

// Get the terms related to the current post (assumed to be a session)
$terms = get_the_terms($post->ID, 'conference');
if ($terms && !is_wp_error($terms)) {
    $term_ids = wp_list_pluck($terms, 'term_id');

    // Setup the query arguments to get speakers
    $args = array(
        'post_type' => $target_post_type,
        'tax_query' => array(
            array(
                'taxonomy' => 'conference',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ),
        ),
        'posts_per_page' => -1  // Retrieve all matching speakers
    );

    // Execute the query for speakers
    $related_speakers = new WP_Query($args);
    if ($related_speakers->have_posts()) {
        echo '<h3>Speakers</h3><ul>';
        while ($related_speakers->have_posts()) : $related_speakers->the_post();
            // Retrieve thumbnail if available
            $thumbnail = has_post_thumbnail() ? get_the_post_thumbnail($post->ID, 'thumbnail') : '';

            // Build list item with thumbnail, title, and excerpt
            echo '<li>' . 
                    $thumbnail . 
                    '<a href="' . get_permalink() . '">' . get_the_title() . '</a>' .
                    '<p>' . get_the_excerpt() . '</p>' .
                 '</li>';
        endwhile;
        echo '</ul>';
    } else {
        echo '<p>No related speakers found.</p>';
    }
    wp_reset_postdata();  // Reset post data after custom query
}
?>


