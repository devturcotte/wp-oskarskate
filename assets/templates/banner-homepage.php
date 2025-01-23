<?php 
        $banniere = get_field('banniere');
        $titre = $banniere["titre"];
        $image = $banniere["image"];
        $imageUrl = $image["url"];
        $texte = $banniere["texte"];
        $bouton = $banniere["bouton"];
?>
<section class="banner-homepage">
    <img src="<?php echo esc_url($imageUrl); ?>" alt="Bannière">
    <section>
        <h2><?php echo $titre ?></h2>
        <p><?php echo $texte ?></p>
        <button><?php echo $bouton ?></button>
    </section>  
</section>