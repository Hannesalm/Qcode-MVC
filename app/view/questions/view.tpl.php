<?php
$loggedIn = false;
$userID = -1;
if($this->session->isAuthenticated() == 1){
    $loggedIn = true;
    $userName = $this->session->get('userName');
    $userID = $this->session->get('userID');
}

$gravatar = "http://www.gravatar.com/avatar";
if(isset($question->gravatar)){
    $gravatar = $question->gravatar;
}

$content = $this->textFilter->doFilter($question->getProperties()['content'], 'shortcode, markdown');

$answers_count = "0";
if(isset($question->getProperties()['answers']))
    if($question->getProperties()['answers'] == 1){
        $answers_count = $question->getProperties()['answers'] . " answer";
    } else {
        $answers_count = $question->getProperties()['answers'] . " answers";
    }


$currentDownvotes = $question->getProperties()['downvote'];
$currentUpvotes = $question->getProperties()['upvote'];

$score = $currentUpvotes - $currentDownvotes;

$now = new DateTime();
$then = new DateTime($question->made);
$sinceThen = $then->diff($now);


if($sinceThen->y == 0){
    if($sinceThen->m == 0){
        if($sinceThen->d == 0){
            if($sinceThen->h == 0){
                if($sinceThen->i == 0){
                    $myFormatForView = $sinceThen->s ." secounds ago";
                } else {
                    $myFormatForView = $sinceThen->i ." minutes and " . $sinceThen->s ." secounds ago";
                }
            } else {
                $myFormatForView = $sinceThen->h ." hours and ". $sinceThen->i ." minutes ago";
            }
        } else {
            $myFormatForView = $sinceThen->d ." days and ". $sinceThen->h ." hours ago";
        }
    } else {
        $myFormatForView = $sinceThen->d ." months and ". $sinceThen->d ." days ago";
    }
} else {
    $myFormatForView = $sinceThen->y ." years and ". $sinceThen->m ." months ago";
}
?>
<div class="well">
    <div class="media">
        <div class="pull-left" href="<?= $gravatar?>">
            <img class="thumbnail" src="<?= $gravatar?>">
        </div>
        <div class="media-body">
            <a href="<?= $this->di->get('url')->create('questions/id/' . $question->question_id) ?>"><h4 class="media-heading"><?=$question->getProperties()['title']?></h4></a>
            <p class="text-right">By <?= $question->name?></p>
            <p><?=$content?></p>
            <div class="footer navbar-header">
                <ul class="list-inline">
                    <li><span><i class="glyphicon glyphicon-calendar"></i><?= $myFormatForView?></span></li>
                    <li>|</li>
                    <span class=""><i class="glyphicon glyphicon-comment"></i> <?= $answers_count?></span>
                    <li>|</li>
                    <li>
                        <a href="<?= $this->di->get('url')->create('questions/upvote/' . $question->question_id) ?>"><span class="glyphicon glyphicon-chevron-up"></span></a>
                        <span><?=$score?></span>
                        <a href="<?= $this->di->get('url')->create('questions/downvote/' . $question->question_id) ?>"><span class="glyphicon glyphicon-chevron-down"></span></a>
                    </li>
                    <li> |
                        <span class="glyphicon glyphicon-tags"></span>
                        <div class="btn-group " role="group" aria-label="...">
                            <?php foreach ($tags[$question->question_id] as $tag) : ?>
                                <a href="<?= $this->di->get('url')->create('questions/listByTag/' . $tag) ?>" class="btn btn-xs btn-primary "><span class="glyphicon glyphicon-remove"></span> <?= $tag ?></a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php foreach ($answers as $answer) : ?>
<?php
$addComment = "";
if($loggedIn){
    $addComment = "<a href='" . $this->di->get('url')->create('questions/commentID/' . $answer->comment_id) ."' class=\"btn btn-default btn-xs pull-right\">Add comment</a>";
}
if($userID == $question->id){
    if($question->accepted_answer == 1){
        if($answer->accepted == 1){
            $accepted = "<a href='". $this->di->get('url')->create('questions/accept/' . $answer->comment_id) ."' class='btn btn-xs btn-success'><span class='glyphicon glyphicon-ok'></span>Accepted</a>";
        } else {
            $accepted = "<a href='". $this->di->get('url')->create('questions/accept/' . $answer->comment_id) ."' class='btn btn-xs btn-info'><span class='glyphicon glyphicon-ok'></span>Accept</a>";
        }
    } else {
        $accepted = "<a href='". $this->di->get('url')->create('questions/accept/' . $answer->comment_id) ."' class='btn btn-xs btn-info'><span class='glyphicon glyphicon-ok'></span>Accept</a>";
    }
} else {
    if($answer->accepted == 1){
        $accepted = "<div class='btn btn-xs btn-success'><span class='glyphicon glyphicon-ok'></span>Accepted</div>";
    } else {
        $accepted = "";
    }
}

$gravatarAnswer = "http://www.gravatar.com/avatar";
if(isset($answer->gravatar)){
    $gravatarAnswer = $answer->gravatar;
}
$content = $this->textFilter->doFilter($answer->content, 'shortcode, markdown');

?>
<div class="media">
    <div class="media-left">
        <div>
            <img class="thumbnail" src="<?=$gravatarAnswer?>" alt="Gravatar">
        </div>
        <div class="text-center">
            <?= $accepted ?>
        </div>
    </div>
    <div class="media-body">

        <blockquote>
            <div style="min-height: 100px; font-size: 14px;">
        <p class="text-right">By <?= $answer->name?></p>
                <?=$content?>
                <?=$addComment?>
            </div>
            <?php if(is_array($comments)) : ?>
            <?php foreach ($comments as $comment) : ?>
                    <?php if($comment->comment_parent_id == $answer->comment_id) : ?>
                        <?php $content = $this->textFilter->doFilter($comment->content, 'shortcode, markdown');

                        $gravatar = "http://www.gravatar.com/avatar";
                        if(isset($comment->gravatar)){
                            $gravatar = $comment->gravatar;
                        }
                        ?>

        <div class="media">
            <div class="media-body" style="border-top: solid 1px grey;">
                <div style="min-height: 100px; font-size: 14px;">
                    <?=$content?> - <a href="#">By <?= $comment->name?></a>
                <div class="clearfix"></div>
                </div>
            </div>
        </div>
                    <?php endif ?>
            <?php endforeach; ?>
            <?php endif ?>
        </blockquote>
    </div>
<?php endforeach; ?>
</div>
<?php
$showForm = "";
if($loggedIn){
    $showForm = $form;
}
?>
<div class="container">
    <?= $showForm?>
</div>







