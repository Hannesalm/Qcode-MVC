<?php
$question = $app->textFilter->doFilter($content, 'shortcode, markdown');
?>

<div class="container">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <div class="account-wall">
                <div><?= $question?></div>
            </div>
        </div>
    </div>
</div>
