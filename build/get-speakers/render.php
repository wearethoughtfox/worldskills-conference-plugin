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
                'is_moderator' => boolval(get_post_meta($speaker->ID, 'is_moderator', true)),
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
            
            // Start the list item
            echo '<li class="has-standard-font-size" style="display: flex; align-items: center;">';
        
            // Thumbnail
            if (has_post_thumbnail($speaker_id)) {
                $thumbnail_id = get_post_thumbnail_id($speaker_id);
                $full_image_url = wp_get_attachment_image_src($thumbnail_id, 'full');
                $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                
                if ($full_image_url) {
                    echo '<div style="flex: 0 0 200px; overflow: hidden;">';
                    echo '<a href="' . get_permalink($speaker_id) . '" target="_self" style="display: block; width: 200px; height: 200px;">';
                    echo '<img src="' . esc_url($full_image_url[0]) . '" 
                               alt="' . esc_attr($alt_text) . '" 
                               style="width: 100%; height: 100%; object-fit: contain; object-position: top;">';
                    echo '</a>';
                    echo '</div>';
                }
            }
            
            // Title and Excerpt wrapper
            echo '<div style="flex: 1;">';
            
            // Title
            echo '<a href="' . get_permalink($speaker_id) . '" style="font-weight: bold; display: block; margin-bottom: 5px;">' . $speaker_data['post_title'] . '</a>';
            
            // Moderator tag if applicable
            if ($speaker_data['is_moderator']) {
                echo '<div class="has-small-font-size" style="background-color: #F7F7F7; display: inline-flex; align-items: center; padding: 5px 10px; border-radius: 4px; margin-bottom: .75rem;">Moderator</div>';
            }
            
            // Excerpt with new styling
            echo '<div style="line-height:1.2;" class="has-small-font-size has-inria-serif-font-family">';
            echo '<p style="margin: 0;">' . get_the_excerpt($speaker_id) . '</p>';
            echo '</div>';
            
            echo '</div>';
            
            // Close the list item
            echo '</li>';
            
            // Horizontal rule
            echo '<hr class="hr-lg">';
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