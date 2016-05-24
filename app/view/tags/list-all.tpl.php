 <table class="table table-hover table-condensed">
        <thead>
        <tr>
            <th>Tag</th>
            <th>Tag count</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tags as $tag) : ?>
            <?php
            $count = "0";
            if(isset($tag->count)){
                $count = $tag->count;
            }
            ?>
        <tr>
            <td> <a href="<?= $this->di->get('url')->create('questions/listByTag/' . $tag->title) ?>" class="btn btn-md btn-primary "><span class="glyphicon glyphicon-remove"></span> <?= $tag->title ?></a></td>
            <td><h4><span class="label label-primary"><?= $count ?></span></h4></td>
        </tr>
<?php endforeach; ?>
        </tbody>
    </table>


