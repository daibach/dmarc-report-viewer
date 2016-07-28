<html>
<head>
  <title><?php echo SITE_TITLE; ?></title>
  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/stylesheet.css">
  <?php if(APP_CUSTOM_CSS) : ?>
    <link rel="stylesheet" href="<?php echo APP_CUSTOM_CSS_PATH; ?>">
  <?php endif;?>
</head>
<body>
  <header>
  <nav class="navbar navbar-inverse navbar-static-top">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand" href="/"><?php echo SITE_NAME; ?></a>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li><a href="/reports">Reports</a></li>
          <li><a href="/">Info</a></li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>
  </header>

  <div class="container">
