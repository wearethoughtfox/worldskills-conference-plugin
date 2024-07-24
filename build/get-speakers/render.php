<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

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
        'posts_per_page' => -1,  // Retrieve all matching speakers
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                array('key' => 'speaker_boost', 'compare' => 'EXISTS'),
                array('key' => 'speaker_reduce', 'compare' => 'EXISTS'),
                array('key' => 'last_name', 'compare' => 'EXISTS'),
            ),
        ),
    );

    // Execute the query for speakers
    $related_speakers = new WP_Query($args);

    if ($related_speakers->have_posts()) {
        $speakers = $related_speakers->posts;
    
        // Prepare speakers data for sorting
        $speakers_data = array_map(function($speaker) {
            return array(
                'ID' => $speaker->ID,
                'post_title' => $speaker->post_title,
                'boost' => get_post_meta($speaker->ID, 'speaker_boost', true),
                'reduce' => get_post_meta($speaker->ID, 'speaker_reduce', true),
                'last_name' => strtolower(get_post_meta($speaker->ID, 'last_name', true)),
            );
        }, $speakers);

        // Sort speakers
        $sorted_speakers = wp_list_sort($speakers_data, array(
            'boost' => 'DESC',
            'reduce' => 'ASC',
            'last_name' => 'ASC',
            'post_title' => 'ASC',
        ));

        echo '<h3 class="wp-block-heading has-medium-font-size has-global-padding" style="font-weight:900; text-transform:uppercase">Speakers</h3><ul class="ws-speaker-list no-margin-block-start">';
        foreach ($sorted_speakers as $speaker_data) {
            $speaker_id = $speaker_data['ID'];
            // Retrieve thumbnail if available
            $thumbnail = has_post_thumbnail($speaker_id) ? get_the_post_thumbnail($speaker_id, 'thumbnail') : '';
    
            // Build list item with thumbnail, title, and excerpt
            echo '<li class="has-standard-font-size">' . 
                    $thumbnail . 
                    '<a href="' . get_permalink($speaker_id) . '">' . $speaker_data['post_title'] . '</a>' .
                    '<p class="no-margin-block-start">' . get_the_excerpt($speaker_id) . '</p>' .
                 '</li> <hr class="hr-lg">';
        }
        echo '</ul>';
    } else {
        echo '<p>No related speakers found.</p>';
    }
    wp_reset_postdata();  // Reset post data after custom query
    
} else {
    echo '<p>No conference terms found for this session.</p>';
}
?>