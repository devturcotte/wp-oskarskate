<?php 
/* 
Template Name: S'impliquer
*/
?>

<?php get_header(); ?>

<main class="main_simpliquer">
    <!-- IMAGE ENTÊTE & TITRE (+ petit texte) -->
    <?php get_template_part('/assets/templates/banner-simpliquer'); ?>

    <!-- SECTION FUN FACTS DE CHIFFRES -->
    <?php get_template_part('/assets/templates/fun-facts'); ?>

    <!-- SECTION MON EXPÉRIENCE DANS NOTRE COMMUNAUTÉ -->
    <section class="histoire">
        <h2>Mon expérience dans notre communauté</h2>
        <img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/histoire-img-1.png" alt="histoire image 1">
        <img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/histoire-img-2.png" alt="histoire image 2">
        <p>[Traduit de l’anglais]
            Cher adolescent au skatepark,
            Tu as probablement environ 15 ans, alors je ne m'attends pas à ce que tu sois très mature ou que tu veuilles une petite fille sur ta rampe de skate d'ailleurs.

            Ce que tu ne sais pas, c'est que ma fille voulait faire du skateboard depuis des mois. J'ai dû la convaincre que le skateboard n'était pas réservé aux garçons.

            Alors, quand nous sommes arrivées au skatepark et avons vu qu'il était plein d'adolescents, elle a immédiatement voulu faire demi-tour et rentrer à la maison.

            Moi aussi, je voulais secrètement partir parce que je ne voulais pas avoir à prendre ma voix de maman et échanger des mots avec toi.
            Je ne voulais pas non plus que ma fille ressente qu'elle devait avoir peur de qui que ce soit, ou qu'elle n'avait pas autant le droit d'être dans ce skatepark que toi.
        </p>
        <button>Continuer la lecture</button>
    </section>

    <!-- SECTION PLUS D'INFOS -->
    <div class="plus-dinfos">
        <div>
            <img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/image-info-1.png" alt="img">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
            <div class="ctas">
                <button class="btn-left">Faire un don</button>
                <button class="btn-right">Nous Connaître</button>
            </div>
        </div>
        <img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/image-info-2.png" alt="img-2">
    </div>

    <!-- SECTION BÉNÉVOLAT -->
    <div class="benevolat">
        <img src="<?php echo get_template_directory_uri() ?>/assets/wp_media-library/benevolat.png" alt="img">
        <section>
            <h2>Bénévolat</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
            <button>Je m'implique</button>
        </section>
    </div>

    <!-- SECTION NOS PARTENAIRES -->
    <?php get_template_part('/assets/templates/partenaires'); ?>
</main>

<?php get_footer(); ?>