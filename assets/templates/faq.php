<section class="faq">
    <h2 class="titre-principal"><?php the_field("titre_section_faq"); ?></h2>

    <ul class="questions-container">
        <?php 
        $faq = new WP_Query([
        'post_type' => 'faq',
        'posts_per_page' => -1,
        ]);

        if ($faq->have_posts()) {
        while ($faq->have_posts()) { 
            $faq->the_post();
            $question = get_field("question");
            $reponse = get_field("reponse");
        ?>
            <li>
                <div class="question">
                    <h3><?php echo $question; ?></h3>
                    <i class="fa-solid fa-plus fa-lg"></i>
                </div>
                <p class="reponse hidden"><?php echo $reponse; ?></p>
            </li>
        <?php
            wp_reset_postdata();
        };
            };
        } 
        else{ ?>
            <li class="aucune-question">
                Aucune questions dans la FAQ
            </li>
        <?php
        };
        ?>
    </ul>
</section>

</section>
