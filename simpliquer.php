<?php 
/* 
Template Name: S'impliquer
*/
?>

<?php get_header(); ?>

<main class="main_simpliquer">
    <div class="bg-container">
        <?php 
            get_template_part('/assets/templates/banner'); 
            get_template_part('/assets/templates/fun-stats');
            get_template_part('/assets/templates/histoire');
            get_template_part('/assets/templates/plus-dinfos');
            get_template_part('/assets/templates/benevolat');
        ?>
        <img class="ramp-bg" src="<?php echo get_template_directory_uri() ?>/assets/images/ramp.svg" alt="Ramp background">
    </div>
    <?php get_template_part('/assets/templates/partenaires'); ?>
</main>

<?php get_footer(); ?>