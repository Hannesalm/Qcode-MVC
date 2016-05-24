<?php if (is_array($users)) : ?>
<div class="list-group">
    <div class="list-group-item list-group-item-info">
        Most active users
        <div class="pull-right">
            Score
        </div>
    </div>
        <?php foreach ($users as $user) : ?>
            <a href="<?= $this->di->get('url')->create('users/id/' . $user->id) ?>" class="list-group-item"><?= $user->name ?><span class="badge"><?= $user->score ?></span></a>
        <?php endforeach; ?>
</div>
<?php endif; ?>



