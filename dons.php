<?php 
/* 
Template Name: Dons
*/
?>

<?php get_header(); ?>

<main class="main_dons">
    <!-- IMAGE ENTÊTE & TITRE (+ petit texte) -->
    <?php get_template_part('/assets/templates/banner-dons'); ?>

    <!-- SECTION PLUGIN DE DONS -->

    <!-- SECTION 'MERCI D'AVOIR CONTRIBUÉ' -->

    <?php get_template_part('/assets/templates/campagne-dons'); ?>

    <!-- SECTION NOS PARTENAIRES -->
    <?php get_template_part('/assets/templates/partenaires'); ?>
</main>

<?php get_footer(); ?>