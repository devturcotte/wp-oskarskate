<?php
        $discuter = get_field("discuter");
        $sticker2 = $discuter["sticker-2"];
?>
<section class="discuter">
        <h2><?php echo $discuter["texte"]; ?></h2>
        <button><?php echo $discuter["bouton"]; ?></button>
        <img src="<?php echo esc_url($sticker2) ?>" alt="Sticker" class="sticker">
</section>