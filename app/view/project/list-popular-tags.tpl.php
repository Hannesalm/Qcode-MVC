<?php if (is_array($tags)) : ?>
<div class="list-group">
    <div class="list-group-item list-group-item-info">
        Most popular tags
        <div class="pull-right">
            Score
        </div>
    </div>
        <?php foreach ($tags as $tag) : ?>
            <div class="list-group-item"><a href="<?= $this->di->get('url')->create('questions/listByTag/' . $tag->title) ?>" class="btn btn-xs btn-primary "><span class="glyphicon glyphicon-remove"></span> <?= $tag->title ?></a><span class="badge"><?= $tag->count ?></span></div>
        <?php endforeach; ?>
</div>
<?php endif; ?>



