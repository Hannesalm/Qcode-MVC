<?php
$loggedIn = false;
$userID = null;
if($this->session->isAuthenticated() == 1){
    $loggedIn = true;
    $userName = $this->session->get('userName');
    $userID = $this->session->get('userID');
}

$createComment = "";
if($loggedIn){
    $createComment = $form;
}

$gravatar = "http://www.gravatar.com/avatar";
if(isset($answer->gravatar)){
    $gravatar = $answer->gravatar;
}
$content = $this->textFilter->doFilter($answer->content, 'shortcode, markdown');
?>

<div class="media">
    <div class="media-left">
        <a href="#">
            <img class="thumbnail" src="<?=$gravatar?>" alt="Gravatar">
        </a>
    </div>
    <div class="media-body">

        <blockquote>
            <div style="min-height: 100px; font-size: 14px;">
                <?=$content?>
            </div>
        </blockquote>
    </div>
</div>

<div class="container">
    <?= $createComment?>
</div>
