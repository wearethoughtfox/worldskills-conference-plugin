<?php
// tag-functions.php

if (!function_exists('sessions_all_get_icon_filename_for_tag')) {
    // Map session tags to icon filenames
function sessions_all_get_icon_filename_for_tag($tag_name) {
    $icon_map = array(
        'apprenticeships' => 'WorldSkills_Conference_Icon_Apprenticeships.png',
        'competitions-of-the-future' => 'WorldSkills_Conference_Icon_Competitions-of-the-future.png',  
        'digital-and-ai' => 'WorldSkills_Conference_Icon_Digital-and-AI.png',
        'employers' => 'WorldSkills_Conference_Icon_Employers.png', 
        'excellence-in-tvet' => 'WorldSkills_Conference_Icon_Excellence-in-TVET.png',  
        'global-agenda' => 'WorldSkills_Conference_Icon_Global-agenda.png',  
        'green' => 'WorldSkills_Conference_Icon_Green.png', 
        'showcase' => 'WorldSkills_Conference_Icon_Showcase.png',  
        'social-justice' => 'WorldSkills_Conference_Icon_Social-Justice.png',
        'youth' => 'WorldSkills_Conference_Icon_Youth.png',
    );

    $tag_name = strtolower($tag_name);
    return isset($icon_map[$tag_name]) ? $icon_map[$tag_name] : '';
}
}


// Get attachment ID by filename
if (!function_exists('get_attachment_id_by_filename')) {
function get_attachment_id_by_filename($filename) {
    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_wp_attached_file',
                'value' => $filename,
                'compare' => 'LIKE'
            )
        )
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        return $query->posts[0]->ID;
    }

    return null;
}
}

if (!function_exists('get_icon_data_for_tag')) {
    function get_icon_data_for_tag($tag_name) {
        $filename = sessions_all_get_icon_filename_for_tag($tag_name);
        
        if (!empty($filename)) {
            $attachment_id = get_attachment_id_by_filename($filename);
            
            if ($attachment_id) {
                $image_data = wp_get_attachment_image_src($attachment_id, 'thumbnail');
                if ($image_data) {
                    return array(
                        'url' => $image_data[0],
                        'width' => $image_data[1],
                        'height' => $image_data[2]
                    );
                }
            }
        }
        
        return null;
    }
}


