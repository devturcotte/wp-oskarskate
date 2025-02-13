<section class="equipe-main-container">
    <?php 
        $equipe = get_field("equipe");
        $sticker1 = $equipe["sticker-1"];
    ?>
    <h2><?php echo $equipe["titre"] ?></h2>
    <div class="hr"></div>
    <div class="equipe-secondary-container">
        <img src="<?php echo esc_url($sticker1); ?>" alt="Sticker" class="sticker">

        <div class="membres-nav">
            <button class="btn-previous">
                <i class="fa-solid fa-chevron-left fa-xl"></i>
            </button>
            <ul>
                <?php 
                    $equipeNav = new WP_Query([
                        'post_type' => 'equipe',
                        'posts_per_page' => -1,
                    ]);
                    $i = 1;
                    if ($equipeNav->have_posts()) :
                    while ($equipeNav->have_posts()) { 

                        $equipeNav->the_post();
                        
                        $miniature = get_field("miniature");
                        $miniatureUrl = $miniature["url"];
                ?>
                    <li class="btn-membre" id="<?php echo $i; ?>">
                        <img src="<?php echo esc_url($miniatureUrl); ?>" alt="Membre">
                    </li>
                <?php
                $i++;   
                }
                endif;
                ?>
            </ul>
            <button class="btn-next">
                <i class="fa-solid fa-chevron-right fa-xl"></i>
            </button>
        </div>
        <ul class="les-membres">
            <?php 
                $equipe = new WP_Query([
                    'post_type' => 'equipe',
                    'posts_per_page' => -1,
                ]);
                $j = 1;
                if ($equipe->have_posts()) {
                while ($equipe->have_posts()) { 

                    $equipe->the_post();

                    $bgImage = get_field("image_de_fond");
                    $bgImageUrl = $bgImage["url"];

                    $nom = get_field("nom");
                    $stats = get_field("fun-stats");
                    $statsTitre = $stats["titre_section"];

                    $stat1 = $stats["stat-1"];
                    $stat_t_1 = $stat1["titre-stat-1"];
                    $stat_q_1 = $stat1["quantite-1"];

                    $stat2 = $stats["stat-2"];
                    $stat_t_2 = $stat2["titre-stat-2"];
                    $stat_q_2 = $stat2["quantite-2"];

                    $stat3 = $stats["stat-3"];
                    $stat_t_3 = $stat3["titre-stat-3"];
                    $stat_q_3 = $stat3["quantite-3"];

                    $stat4 = $stats["stat-4"];
                    $stat_t_4 = $stat4["titre-stat-4"];
                    $stat_q_4 = $stat4["quantite-4"];

                    $anecdote = get_field("anecdote");
                    $anecdoteTitre = $anecdote["titre_section"];
                    $anecdoteTexte = $anecdote["texte"];

                    $description = get_field("description");
                    $descriptionTitre = $description["titre_section"];
                    $descriptionTexte = $description["texte"];

                    $photo = get_field("photo");
                    $photoUrl = $photo["url"];

                    $themeMembre = get_field("theme_du_membre");

            ?>
            <li class="membre-container" id="<?php echo $j; ?>">
                <figure>
                    <img src="<?php echo esc_url($bgImageUrl); ?>" alt="background">
                </figure>

                <section class="membre <?php echo $themeMembre; ?>">
                    <figure>
                        <img src="<?php echo esc_url($photoUrl); ?>" alt="Team Oskar" class="membre-photo">
                    </figure>
                    <h3 class="nom"><?php echo $nom; ?></h3>
                    <div class="contenu"> 
                        <section class="fun-stats">
                            <h4><?php echo $statsTitre; ?></h4>
                            <div class="stats-container">
                                <div class="stat">
                                    <h5><?php echo $stat_t_1; ?></h5>
                                    <div class="quantite _<?php echo $stat_q_1; ?>">
                                    </div>
                                </div>
                                <div class="stat">
                                    <h5><?php echo $stat_t_2; ?></h5>
                                    <div class="quantite _<?php echo $stat_q_2; ?>">
                                    </div>
                                </div>
                                <div class="stat">
                                    <h5><?php echo $stat_t_3; ?></h5>
                                    <div class="quantite _<?php echo $stat_q_3; ?>">
                                    </div>
                                </div>
                                <div class="stat">
                                    <h5><?php echo $stat_t_4; ?></h5>
                                    <div class="quantite _<?php echo $stat_q_4; ?>">
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="anecdote">
                            <h4><?php echo $anecdoteTitre; ?></h4>
                            <p><?php echo $anecdoteTexte; ?></p>
                        </section>
                        <section class="description">
                            <h4><?php echo $descriptionTitre; ?></h4>
                            <p><?php echo $descriptionTexte; ?></p>
                        </section>
                    </div>
                </section>  
            </li>
            
            <?php
            $j++;
            wp_reset_postdata();
            };
            ?>
        </ul>
        <?php
        } else{
        ?>
            <section class="aucun-membre">
            </section>
        <?php
        };
        ?>
    </div>
</section>