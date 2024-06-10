<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
  <?php
// First, determine the current post type
$current_post_type = get_post_type($post->ID);

// Depending on the current post type, set the target post type for the query
$target_post_type = ($current_post_type === 'session') ? 'speaker' : 'session';

// Get the terms related to the current post
$terms = get_the_terms($post->ID, 'conference');
if ($terms && !is_wp_error($terms)) {
    $term_ids = wp_list_pluck($terms, 'term_id');

    // Setup the query arguments
    $args = array(
        'post_type' => $target_post_type,  // Dynamically set to either 'session' or 'speaker'
        'tax_query' => array(
            array(
                'taxonomy' => 'conference',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ),
        ),
        'posts_per_page' => -1  // Retrieve all matching posts
    );

    // Execute the query
    $related_posts = new WP_Query($args);
    if ($related_posts->have_posts()) {
        // Dynamically display heading based on post type
        echo '<h3>' . ucfirst($target_post_type) . 's</h3><ul>';
        while ($related_posts->have_posts()) : $related_posts->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        endwhile;
        echo '</ul>';
    } else {
        echo '<p>No related ' . $target_post_type . 's found.</p>';
    }
    wp_reset_postdata();  // Reset post data after custom query
}
?>
