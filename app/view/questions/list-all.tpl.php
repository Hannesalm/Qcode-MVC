<?php if (is_array($questions)) : ?>
    <?php foreach ($questions as $question) : ?>
        <?php
        $gravatar = "http://www.gravatar.com/avatar";
        if(isset($question->gravatar)){
            $gravatar = $question->gravatar;
        }
        $content = $this->textFilter->doFilter($question->getProperties()['content'], 'shortcode, markdown');

        if(isset($question->gravatar))
            $gravatar = $question->gravatar;

        $answers_count = "0";
        if(isset($question->answers))
            if($question->answers == 1){
                $answers_count = $question->answers . " answer";
            } else {
                $answers_count = $question->answers . " answers";
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
                <div class="pull-left" href="<?= $gravatar?>">
                    <img class="thumbnail" src="<?=$gravatar?>">
                </div>
                <div class="media-body">
                    <a href="<?= $this->di->get('url')->create('questions/id/' . $question->question_id) ?>"><h4 class="media-heading"><?= $question->title?></h4></a>
                    <p class="text-right">By <?= $question->name?></p>
                    <p><?= $content?></p>
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
    <?php endforeach; ?>
<?php endif; ?>


