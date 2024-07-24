<?php
$post_id = get_the_ID();
$terms = get_the_terms($post_id, 'session-type');
?>

<div <?php echo get_block_wrapper_attributes(); ?>>
    <?php if ($terms && !is_wp_error($terms)) : ?>
        <span class="session-type has-small-font-size">
            <?php echo esc_html($terms[0]->name); ?>
        </span>
    <?php endif; ?>
</div>
