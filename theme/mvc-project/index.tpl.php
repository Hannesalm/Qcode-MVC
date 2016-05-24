<?php
$loggedIn = false;
if($this->session->isAuthenticated() == 1){
  $loggedIn = true;
  $userName = $this->session->get('userName');
  $userID = $this->session->get('userID');
}
?>
<!doctype html>
<html class='no-js' lang='<?=$lang?>'>
<head>
<meta charset='utf-8'/>
<title><?=$title . $title_append?></title>
<?php if(isset($favicon)): ?><link rel='icon' href='<?=$this->url->asset($favicon)?>'/><?php endif; ?>
<?php foreach($stylesheets as $stylesheet): ?>
<link rel='stylesheet' type='text/css' href='<?=$this->url->asset($stylesheet)?>'/>
<?php endforeach; ?>
<?php if(isset($style)): ?><style><?=$style?></style><?php endif; ?>
<script src='<?=$this->url->asset($modernizr)?>'></script>
</head>
<body>
<div class='container'>
  <div class=''>
    <?php if(isset($header)) echo $header?>
    <?php $this->views->render('header')?>
</div>
<?php if ($this->views->hasContent('navbar')) : ?>
  <nav class="navbar navbar-default navbar-top">
    <div class="container-fluid">
      <?php if ($loggedIn) : ?>
    <div class="btn-group pull-right" role="group">
      <a href="<?= $this->di->get('url')->create('questions/create') ?>" type="button" class="btn btn-default navbar-btn"><span class="glyphicon glyphicon-question-sign"></span> Ask a question</a>
      <a href="<?= $this->di->get('url')->create('users/id/' . $userID) ?>" type="button" class="btn btn-default navbar-btn"><span class="glyphicon glyphicon-user"></span> <?= $userName?></a>
      <a href="<?=$this->di->get('url')->create('users/logout')?>" type="button" class="btn btn-info navbar-btn"><span class="glyphicon glyphicon-log-out"></span> Logg out</a>
    </div>
      <?php endif; ?>
      <?php if (!$loggedIn) : ?>
        <div class="btn-group pull-right" role="group">
          <a href="<?=$this->di->get('url')->create('users/login')?>" type="button" class="btn btn-default navbar-btn"><span class="glyphicon glyphicon-log-in"></span> Sign in</a>
          <a href="<?=$this->di->get('url')->create('users/create')?>" type="button" class="btn btn-info navbar-btn"><span class="glyphicon glyphicon-plus"></span> Create account</a>
        </div>
      <?php endif; ?>
      <div class="navbar-header">
        <a class="brand" href="<?=$this->di->get('url')->create('')?>">
          <img alt="Brand" src="<?=$this->url->asset("img/logo.png")?>">
        </a>
      </div>
        <p class="navbar-text">Qcode</p>
      <?php $this->views->render('navbar')?>
    </div>

  </nav>
<?php endif; ?>
  <?php if(isset($main)) echo $main?>
  <?php $this->views->render('main')?>
  <div class="row">
    <?php if ($this->views->hasContent('column1')) : ?>
      <div class="col-md-8"><?php $this->views->render('column1')?></div>
    <?php endif; ?>
    <?php if ($this->views->hasContent('sidebar')) : ?>
      <div class="col-md-4"><?php $this->views->render('sidebar')?></div>
    <?php endif; ?>
  </div>
  <div class="row">
    <?php if ($this->views->hasContent('column2')) : ?>
      <div class=""><?php $this->views->render('column2')?></div>
    <?php endif; ?>
  </div>



<div class="navbar navbar-default navbar-bottom footer">
  <?php if(isset($footer)) echo $footer?>
    <div class="">
      <?php $this->views->render('footer')?>
    </div>
</div>
</div>
<?php if(isset($jquery)):?><script src='<?=$this->url->asset($jquery)?>'></script><?php endif; ?>

<?php if(isset($javascript_include)): foreach($javascript_include as $val): ?>
<script src='<?=$this->url->asset($val)?>'></script>
<?php endforeach; endif; ?>

<?php if(isset($google_analytics)): ?>
<script>
  var _gaq=[['_setAccount','<?=$google_analytics?>'],['_trackPageview']];
  (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
  g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
  s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
<?php endif; ?>

</body>
</html>