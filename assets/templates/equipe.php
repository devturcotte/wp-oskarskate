<?php 
    $equipe = get_field("equipe");

    $sticker1 = $equipe["sticker-1"];

    $logo = $equipe["logo"];
    $logoUrl = $logo["url"];
?>
<section class="equipe-main-container">
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
                $template = rand(1, 5);
                $j = 1;
                if ($equipe->have_posts()) {
                while ($equipe->have_posts()) { 

                    $equipe->the_post();

                    $bgImage = get_field("image_de_fond");
                    $bgImageUrl = $bgImage["url"];

                    $nom = get_field("nom");
                    $stats = get_field("fun-stats");
                    $statsTitre = $stats["titre_section"];

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
                    <h3 class="nom"><?php echo $nom; ?></h3>
                    <figure>
                        <img src="<?php echo esc_url($photoUrl); ?>" alt="Team Oskar" class="membre-photo">
                    </figure>
                    <div class="contenu"> 
                        <section class="fun-stats">
                            <h4><?php echo $statsTitre; ?></h4>
                            <div class="stats-container">
                                <div class="stat">
                                    <h5>Test</h5>
                                    <div class="quantite">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                    </div>
                                </div>
                                <div class="stat">
                                    <h5>Balance</h5>
                                    <div class="quantite">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                    </div>
                                </div>
                                <div class="stat">
                                    <h5>Ollie</h5>
                                    <div class="quantite">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                    </div>
                                </div>
                                <div class="stat">
                                    <h5>Speed</h5>
                                    <div class="quantite">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
                                        <input type="radio">
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
        
        <img src="<?php echo esc_url($logoUrl); ?>" alt="Logo Oskar" class="logo">
    </div>
</section>