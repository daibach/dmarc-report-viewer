<html>
<head>
  <title><?php echo SITE_TITLE; ?></title>
  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <style>
  h1 { padding-bottom: 15px; }
  h1 span { color: #5cb85c; }

  .well h2 { margin: 0 0 15px 0; }
  h2 { padding-top: 15px; }
  table th, table td { padding: 5px; border-bottom: #ddd 1px solid; }
  table th { font-weight: bold; background: #005EA5; color: #fff; }
  table tr:nth-child(even) { background: #f5f5f5; }
  table tr.pass td { background: #d6e9c6; border-color: #3c763d; }
  table td.disposition_quarantine { background: #f0ad4e; }
  table td.disposition_reject { color: #fff; font-weight: bold; background: #D53880; }
  table.records td { font-size: 0.8em; }
  .info { font-size: 1.2em; padding: 15px 0; }
  .info strong { display: inline-block; width: 120px; }

  .navbar-inverse { background: #2B8CC4; border-color: #2B8CC4;}
  .navbar-inverse .navbar-brand { color: #fff; font-weight: bold; }
  .navbar-inverse .navbar-nav>li>a { color: #fff; }
  .navbar-inverse .navbar-nav>.active>a { background: #005EA5; }
  .navbar-inverse .navbar-nav>li>a:hover { background: #005EA5; }

  footer { background: #F8F8F8; border-top: #DEE0E2 1px solid; margin: 30px 0 0 0; padding: 15px 0 30px 0; }
  </style>
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
