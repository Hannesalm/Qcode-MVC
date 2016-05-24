<?php if (is_array($users)) : ?>

<table class="table table-hover">

    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Joined</th>
        <th>Score</th>
    </tr>
    </thead>
    <tbody>

        <?php foreach ($users as $user) : ?>
            <?php $status = $user->status; $deleted = $user->deleted?>
            <?php
            $score = "0";
            if(isset($user->score)){
                $score = $user->score;
            }
            ?>
            <tr>
                <td><a href="<?= $this->di->get('url')->create('users/id/' . $user->id) ?>"><?= $user->name ?></td></a>
                <td><?= $user->email ?></td>
                <td><?= $user->created ?></td>
                <td><?=$score?></td>
            </tr>
        <?php endforeach; ?>
            </tbody>
        </table>
<?php endif; ?>


