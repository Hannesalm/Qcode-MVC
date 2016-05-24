<?php
$gravatar = "";
if(isset($user->getProperties()['email'])){
    $gravatar = $user->get_gravatar($user->getProperties()['email']);
}
?>

<div class="container">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <div class="account-wall">
                <img class="profile-img" src="<?=$gravatar?>"
                     alt="Avatar">
                <div><?= $content?></div>
            </div>
        </div>
    </div>
</div>
