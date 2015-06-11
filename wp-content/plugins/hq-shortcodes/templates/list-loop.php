<ul class="hq-posts hq-posts-list-loop">
    <?php
// Posts are found
    if ($posts->have_posts()) {
        while ($posts->have_posts()) {
            $posts->the_post();
            global $post;
            ?>
            <li id="hq-post-<?php the_ID(); ?>" class="hq-post"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php
            }
        }
// Posts not found
        else {
            ?>
        <li><?php _e('Posts not found', 'su') ?></li>
        <?php
    }
    ?>
</ul>
