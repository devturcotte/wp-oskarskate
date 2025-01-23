<section class="partenaires">
    <h2><?php the_field('titre_partenaires') ?></h2>
    <ul>
        <?php
            $i=1;
            while($i<=4){
            $partenaire = get_field('partenaire-'.$i);
            $image = $partenaire["image"];
            $imageUrl = $image["url"];

            $url = $partenaire["url"];
        ?>
        <li>
            <a href="<?php echo esc_url($url); ?>" target="_blank">
                <img src="<?php echo esc_url($imageUrl); ?>" alt="Logo Partenaire">
            </a>
        </li>
        <?php
            $i++;
            }
        ?>
    </ul>
</section>