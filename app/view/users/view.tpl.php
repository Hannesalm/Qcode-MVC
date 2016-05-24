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

<?php
$questionCount = "0";
if(isset($usersQuestions)){
    $questionCount = count($usersQuestions);
}
$answerCount = "0";
if(isset($user_answers)){
    $answerCount = count($user_answers);
}
$commentCount = "0";
if(isset($usersQuestions)){
    $commentCount = count($comments);
}
?>
<div class="row">
    <div class="col-md-4">
        <div class="list-group">
            <div class="list-group-item active">
                Questions asked<span class="badge"><?= $questionCount?></span>
            </div>
        <?php foreach ($usersQuestions as $question) : ?>
            <a href="<?= $this->di->get('url')->create('questions/id/' . $question->question_id) ?>" class="list-group-item"><?= $question->title ?></a>
        <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="list-group">
            <div class="list-group-item active">
                Answers<span class="badge"><?= $answerCount?></span>
            </div>
            <?php foreach ($user_answers as $user_answer) : ?>
                <a href="<?= $this->di->get('url')->create('questions/id/' . $user_answer->question_id) ?>" class="list-group-item"><?= $user_answer->content ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="list-group">
            <div class="list-group-item active">
                Comments<span class="badge"><?= $commentCount?></span>
            </div>
            <?php foreach ($comments as $comment) : ?>
                <a href="<?= $this->di->get('url')->create('questions/id/' . $question->question_id) ?>" class="list-group-item"><?= $comment->content ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>





