<?php
declare(strict_types=1);

use Cake\Core\Configure;

foreach ((array)Configure::read('DistrictUI.stylesheets', []) as $stylesheet) {
    $this->prepend('css', $this->Html->css($stylesheet));
}
foreach ((array)Configure::read('DistrictUI.iconStylesheets', []) as $stylesheet) {
    $this->prepend('css', $this->Html->css($stylesheet));
}
foreach ((array)Configure::read('DistrictUI.scripts', []) as $script) {
    $this->append('script', $this->Html->script($script));
}

if (Configure::check('App.author')) {
    $this->prepend(
        'meta',
        $this->Html->meta('author', null, ['name' => 'author', 'content' => Configure::read('App.author')]),
    );
}
$this->prepend('meta', $this->Html->meta('favicon.ico', '/favicon.ico', ['type' => 'icon']));
?>
<!doctype html>
<?= $this->fetch('html') ?>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?= h($this->fetch('title')) ?></title>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
    </head>

    <?php
    echo $this->fetch('tb_body_start');
    echo $this->fetch('tb_flash');
    echo $this->fetch('content');
    echo $this->fetch('tb_footer');
    echo $this->fetch('script');
    echo $this->fetch('tb_body_end');
    ?>

</html>
