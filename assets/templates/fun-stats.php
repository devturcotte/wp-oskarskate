<section class="fun-stats">
    <ul>
        <?php
            $i=1;
            while($i<=4){
            $stat = get_field('funstat-'.$i);
        ?>
        <li>
            <span><?php echo $stat["nombre"]; ?></span>
            <h3><?php echo $stat["titre"]; ?></h3>
        </li>
        <?php
            $i++;
            }
        ?>
    </ul>
</section>