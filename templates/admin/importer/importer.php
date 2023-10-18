<div>
    <?php foreach ( $menus as $slug => $label ) : ?>
        <a href="<?php menu_page_url( $slug ); ?>"><?php echo $label; ?></a>
    <?php endforeach; ?>
</div>