<?php 
        $banniere = get_field('banniere');
        $titre = $banniere["titre"];
        $image = $banniere["image"];
        $imageUrl = $image["url"];
        $texte = $banniere["texte"];
        $bouton = $banniere["bouton"];
?>
<section class="banner dons">
    <section>
        <img src="<?php echo esc_url($imageUrl); ?>" alt="Bannière">
        <h2><?php echo $titre ?></h2>
    </section>
</section>