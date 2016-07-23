<ol class="breadcrumb">
  <li><a href="/">Home</a></li>
  <li>Reports</li>
</ol>

<h1>DMARC Reports</h1>

<div class="row">
  <div class="col-md-6">
    <h2>Aggregate reports</h2>
    <ul>
      <?php foreach($report_domains as $d) : ?>
        <li><a href="/reports/dmarc-aggregate-reports/<?php echo $d->domain; ?>"><?php echo $d->domain; ?></a></li>
      <?php endforeach;?>
    </ul>
  </div>
  <div class="col-md-6">
    <h2>Domains</h2>
    <ul>
      <?php foreach($tlds as $tld) : ?>
        <li><a href="/reports/domains/<?php echo $tld->domain_tld; ?>">.<?php echo $tld->domain_tld; ?></a></li>
      <?php endforeach;?>
    </ul>
  </div>
</div>
