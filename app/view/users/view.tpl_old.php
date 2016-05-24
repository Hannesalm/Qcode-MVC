<?php
$presentation = $this->textFilter->doFilter($user->getProperties()['presentation'], 'shortcode, markdown');

$answers = "0";
if(isset($user->getProperties()['user_answers']))
$answers = $user->getProperties()['user_answers'];

$questions = "0";
if(isset($user->getProperties()['questions']))
$questions = $user->getProperties()['questions'];

$score = "0";
if(isset($user->getProperties()['score']))
$score = $user->getProperties()['score'];

$loggedIn = false;
$userID = null;
if($this->session->isAuthenticated() == 1){
    $loggedIn = true;
    $userName = $this->session->get('userName');
    $userID = $this->session->get('userID');
}
$updateButton = "";
$userIDofQuestion = $user->getProperties()['id'];
if($userIDofQuestion == $userID){
    $updateButton = "<div class='panel-footer'><a href='" . $this->url->create('users/edit/' . $user->getProperties()['id']) ."' data-original-title=\"Edit this user\" data-toggle=\"tooltip\" type=\"button\" class=\"btn btn-sm btn-warning btn-block\">Update</a> </div>
    ";
}

?>
<div class="row row-height">
    <div class="col-md-6">
        <div class="panel panel-info" style="height: 310px;">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$user->getProperties()['name']?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="<?=$user->get_gravatar($user->getProperties()['email']) ?>" class="img-responsive"> </div>

                    <div class=" col-md-9 col-lg-9 ">
                        <table class="table table-user-information">
                            <tbody>
                            <tr>
                                <td>Email:</td>
                                <td><?=$user->getProperties()['email']?></td>
                            </tr>
                            <tr>
                                <td>Joined:</td>
                                <td><?=$user->getProperties()['created']?></td>

                            </tr>

                            </tbody>
                        </table>

                        <div class="col-xs-12 divider text-center">
                            <div class="col-xs-12 col-sm-4 emphasis">
                                <h2><strong><?=$answers?></strong></h2>
                                <p><small>Answers</small></p>

                            </div>
                            <div class="col-xs-12 col-sm-4 emphasis">
                                <h2><strong><?=$questions?></strong></h2>
                                <p><small>Questions</small></p>

                            </div>
                            <div class="col-xs-12 col-sm-4 emphasis">
                                <h2><strong><?=$score?></strong></h2>
                                <p><small>Score</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?=$updateButton?>
        </div></div>
    <div class="col-md-6"><div class="panel panel-info " style="height: 310px; overflow-y: scroll;overflow-x: hidden;">
            <div class="panel-heading">
                <h3 class="panel-title">Presentation</h3>
            </div>
            <div class="panel-body" style="min-height: 270px;">
                <?=$presentation?>
            </div>

        </div></div>
</div>
<?php foreach ($usersQuestions as $question) : ?>
    <?php
    $gravatar = "http://www.gravatar.com/avatar";

    $content = $this->textFilter->doFilter($question->content, 'shortcode, markdown');

    if(isset($question->gravatar)){
        $gravatar = $question->gravatar;
    }

    $awnsers = "0";
    if(isset($question->awnsers)){
        $awnsers = $question->awnsers;
    }

    $now = new DateTime();
    $then = new DateTime($question->made);
    $sinceThen = $then->diff($now);

    $currentDownvotes = $question->downvote;
    $currentUpvotes = $question->upvote;

    $score = $currentUpvotes - $currentDownvotes;

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
            <a class="pull-left" href="<?= $question->gravatar?>">
                <img class="thumbnail" src="<?=$user->get_gravatar($user->getProperties()['email']) ?>">
            </a>
            <div class="media-body">
                <a href="<?= $this->di->get('url')->create('questions/id/' . $question->question_id) ?>"><h4 class="media-heading"><?= $question->title?></h4></a>
                <p class="text-right">By <?= $user->getProperties()['name']?></p>
                <p><?= $content?></p>
                <div class="footer navbar-header">
                    <ul class="list-inline">
                        <li><span><i class="glyphicon glyphicon-calendar"></i><?= $myFormatForView?></span></li>
                        <li>|</li>
                        <span class=""><i class="glyphicon glyphicon-comment"></i> <?=$awnsers?> answers</span>
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
                                    <button type="button" class="btn btn-xs btn-primary "><span class="glyphicon glyphicon-remove"></span> <?= $tag ?></button>
                                <?php endforeach; ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>





