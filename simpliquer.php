<?php 
/* 
Template Name: S'impliquer
*/
?>

<?php get_header(); ?>

<main class="main_simpliquer">
    <div class="bg-container">
        <!-- IMAGE ENTÊTE & TITRE (+ petit texte) -->
        <?php get_template_part('/assets/templates/banner-simpliquer'); ?>

        <!-- SECTION FUN FACTS DE CHIFFRES -->
        <?php get_template_part('/assets/templates/fun-facts'); ?>

        <!-- SECTION MON EXPÉRIENCE DANS NOTRE COMMUNAUTÉ -->
        <?php get_template_part('/assets/templates/histoire'); ?>

        <!-- SECTION PLUS D'INFOS -->
        <?php get_template_part('/assets/templates/plus-dinfos'); ?>

        <!-- SECTION BÉNÉVOLAT -->
        <?php get_template_part('/assets/templates/benevolat'); ?>

        <img class="ramp-bg" src="<?php echo get_template_directory_uri() ?>/assets/images/ramp.svg" alt="Ramp background">
    </div>
    
    <!-- SECTION NOS PARTENAIRES -->
    <?php get_template_part('/assets/templates/partenaires'); ?>
</main>

<?php get_footer(); ?>