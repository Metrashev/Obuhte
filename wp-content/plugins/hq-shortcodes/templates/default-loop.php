<?php

// Posts are found
if ($posts->have_posts()) {
    echo '<div id="articles">';
    while ($posts->have_posts()) :
        $posts->the_post();
        global $post;
        ?>

        <?php get_template_part('blog/content', get_post_format()); ?>

        <?php

    endwhile;
    echo '</div>';
}
// Posts not found
else {
    get_template_part('blog/content', 'none');
}
?>