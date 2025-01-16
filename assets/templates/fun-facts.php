<section class="fun-facts">
    <?php 
        $funFacts = new WP_Query([
            'post_type' => 'fun-facts',
            'posts_per_page' => 4,
        ]);

        if ($funFacts->have_posts()) :
        while ($funFacts->have_posts()) : $funFacts->the_post();
    ?>
    <ul>
        <li>
            <span><?php the_field('statistique'); ?></span>
            <h3><?php the_field('titre'); ?></h3>
        </li>
    </ul>
    <?php
        endwhile;
        endif;
    ?>
</section>