<ol class="breadcrumb">
  <li><a href="/">Home</a></li>
  <li><a href="/reports">Reports</a></li>
  <li>Domains in: <span><?php echo $tld; ?></span></li>
</ol>

<h1>Domains in: <span><?php echo $tld; ?></span></h1>

<ul>
  <?php foreach($domains as $d) : ?>
    <li><a href="/reports/domain-report/<?php echo $d->domain_name; ?>"><?php echo $d->domain_name; ?></a></li>
  <?php endforeach;?>
</ul>
