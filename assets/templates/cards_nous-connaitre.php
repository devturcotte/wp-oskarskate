<div class="cards-container">
    <?php
        $i = 1;
        while($i <= 2){
        $carte = get_field("carte-".$i);
        $image = $carte["image"];
        $imageUrl = $image["url"];
    ?>
        <div class="card _<?php echo $i; ?>">
            <figure>
                <img src="<?php echo esc_url($imageUrl); ?>" alt="Carte">
            </figure>
            <section>
                <h3><?php echo $carte["titre"]; ?></h3>
                <p><?php echo $carte["texte"]; ?></p>
            </section>
        </div>
    <?php
        $i++;
        }
    ?>
</div>