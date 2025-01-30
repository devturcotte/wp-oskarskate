<?php 
/* 
Template Name: Dons
*/
?>

<?php get_header(); ?>

<main class="main_dons">
    <?php 
        get_template_part('/assets/templates/banner'); 
        get_template_part('/assets/templates/module_investir');
        get_template_part('/assets/templates/campagne-dons'); 
        get_template_part('/assets/templates/partenaires');
    ?>
</main>

<?php get_footer(); ?>