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
    </section>

    <!-- SECTION PLUS D'INFOS -->
    <div class="plus-dinfos">
        <div>
            <img src="#" alt="img">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
            <div class="ctas">
                <button class="btn-left">Faire un don</button>
                <button class="btn-right">Nous Connaître</button>
            </div>
        </div>
        <img src="#" alt="img-2">
    </div>

    <!-- SECTION BÉNÉVOLAT -->
    <div class="benevolat">
        <img src="#" alt="img">
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