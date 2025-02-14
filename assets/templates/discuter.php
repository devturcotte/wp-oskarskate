<section class="discuter">
        <?php
                $discuter = get_field("discuter");
                $sticker2 = $discuter["sticker-2"];
        ?>
        <h2><?php echo $discuter["texte"]; ?></h2>
        <div>
                <button><?php echo $discuter["bouton"]; ?></button>
                <img src="<?php echo $sticker2; ?>" alt="Sticker" class="sticker">
        </div>
</section>