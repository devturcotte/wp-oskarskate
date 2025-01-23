<?php 
    $equipe = get_field("equipe");

    $sticker = $equipe["sticker"];
    $stickerUrl = $sticker["url"];
?>
<section class="equipe-main-container">
    <h2><?php echo $equipe["titre"] ?></h2>
    <div class="hr"></div>
    <div class="equipe-secondary-container">
        <figure>
            <img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/bg-equipe-1.svg" alt="background">
        </figure>
        <img src="<?php echo esc_url($stickerUrl); ?>" alt="Sticker" class="sticker">
    
        <div class="membres-nav">
            <i class="fa-solid fa-chevron-left fa-xl"></i>
            <ul>
                <li><img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/membre-equipe.png" alt="Membre"></li>
                <li><img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/membre-equipe.png" alt="Membre"></li>
                <li><img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/membre-equipe.png" alt="Membre"></li>
                <li><img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/membre-equipe.png" alt="Membre"></li>
            </ul>
            <i class="fa-solid fa-chevron-right fa-xl"></i>
        </div>

        <section class="membre">
            <h3 class="nom">Team OSKAR</h3>
            <img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/team-oskar.png" alt="Team Oskar" class="membre-photo">
            <div class="contenu">
                <section class="fun-stats">
                    <h4>Fun stats</h4>
                </section>
                <section class="anecdote">
                    <h4>Anecdote:</h4>
                    <p>Skate ipsum dolor sit amet, lip boned out shinner yeah death box Steve Rocco. 720 ho-ho nosegrind transfer pogo. Coper Tracker slam no comply boardslide. Indy g</p>
                </section>
                <section class="description">
                    <h4>Description:</h4>
                    <p>Skate ipsum dolor sit amet, lip boned out shinner yeah death box Steve Rocco. 720 ho-ho nosegrind transfer pogo. Coper Tracker slam no comply boardslide. Indy g</p>
                </section>
            </div>
        </section>

        <img src="<?php echo get_template_directory_uri() ?>/assets/images/logo-footer.png" alt="Logo Oskar" class="logo">
    </div>
</section>