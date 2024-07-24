<?php
// On the single session page

// Get the current post's tags
$tags = get_the_terms(get_the_ID(), 'session-tags');

if (!empty($tags) && !is_wp_error($tags)) {
    echo '<div class="session-tags">';
    foreach ($tags as $tag) {
        $icon_data = get_icon_data_for_tag($tag->slug);
        echo '<div class="session-tag-item">';
        if ($icon_data) {
            echo '<img src="' . esc_url($icon_data['url']) . '" 
                       alt="' . esc_attr($tag->name) . ' icon" 
                       class="session-tag-icon"
                       width="20" height="20">';
        }
        echo '<span class="session-tag-name">' . esc_html($tag->name) . '</span>';
        echo '</div>';
    }
    echo '</div>';
}