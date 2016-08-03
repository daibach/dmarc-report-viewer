<ol class="breadcrumb">
  <li><a href="/">Home</a></li>
  <li><a href="/domains">Domains</a></li>
  <li><?php echo $domain_info->domain_full; ?></li>
</ol>

<h1>Domain: <span><?php echo $domain_info->domain_full; ?></h1>

<div class="row">
  <div class="col-md-9">
    <p class="info"><strong>Last  check:</strong> <?php echo $domain_info->last_checked; ?>
    <br/><strong>SPF:</strong> <?php echo ui_produce_badge($domain_info->last_spf_result); ?>
    <br/><strong>DMARC:</strong> <?php echo ui_produce_dmarc_badge($domain_info->last_dmarc_result); ?></p>
  </div>
  <div class="col-md-3">
    <ul class="nav nav-pills nav-stacked">
      <li><a href="#spf-dns">SPF DNS records</a></li>
      <li><a href="#dmarc-dns">DMARC DNS records</a></li>
      <li><a href="#email-counts">Email counts</a></li>
      <li><a href="#dmarc-reports">DMARC reports</a></li>
      <li><a href="#dmarc-records">DMARC records</a></li>
    </ul>
  </div>
</div>

<hr/>

<div class="well">
  <div id="curve_chart" style="height: 500px"></div>
</div>

<h2 id="spf-dns">SPF DNS records</h2>

<table width="100%" class="records">
  <thead>
    <tr>
      <th class="date">Date</th>
      <th>Record</th>
    </tr>
  </thead>
  <tbody>
    <?php if($spf_dns_records) : ?>
    <?php foreach($spf_dns_records as $record) : ?>
      <tr>
        <td><?php echo $record->record_date; ?></td>
        <?php if ($record->record_empty) : ?>
          <td>none</td>
        <?php else : ?>
          <td><samp><?php echo $record->record_text; ?></samp></td>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>


<h2 id="dmarc-dns">DMARC DNS records</h2>

<table width="100%" class="records">
  <thead>
    <tr>
      <th class="date">Date</th>
      <th>Record</th>
    </tr>
  </thead>
  <tbody>
    <?php if($dmarc_dns_records) : ?>
    <?php foreach($dmarc_dns_records as $record) : ?>
      <tr>
        <td><?php echo $record->record_date; ?></td>
        <?php if ($record->record_empty) : ?>
          <td>none</td>
        <?php else : ?>
          <td><samp><?php echo $record->record_text; ?></samp></td>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<h2 id="email-counts">Counts</h2>
<table width="100%">
  <thead>
    <tr>
      <th>Date</th>
      <th>Total Sent</th>
      <th>Pass</th>
      <th>None (Fail)</th>
      <th>Quarantined</th>
      <th>Rejected</th>
      <th>Total Fail</th>
    </tr>
  </thead>
  <tbody>
    <?php if($email_counts) : foreach($email_counts as $c) : ?>
      <tr>
        <td><?php echo date('Y-m-d',mysql_to_unix($c->date)); ?></td>
        <td><?php echo $c->total; ?></td>
        <td><?php echo $c->none_pass; ?></td>
        <td><?php echo $c->none_fail; ?></td>
        <td><?php echo $c->quarantine_fail; ?></td>
        <td><?php echo $c->reject_fail; ?></td>
        <td><?php echo $c->total_fail; ?></td>
      </tr>
    <?php endforeach; endif; ?>
  </tbody>
</table>

<h2 id="dmarc-reports">DMARC reports</h2>
<table width="100%">
  <thead>
    <tr>
      <th>Date</th>
      <th>Reporter</th>
      <th>Report For</th>
      <th>Pass</th>
      <th>Fail</th>
      <th>p</th>
      <th>sp</th>
      <th>pct</th>
    </tr>
  </thead>
  <tbody>
    <?php if($dmarc_reports) : foreach($dmarc_reports as $r) : ?>
      <tr>
        <td><a href="/reports/dmarc-aggregate-report/<?php echo $r->id; ?>"><?php echo date('d M Y',mysql_to_unix($r->report_date));?></a></td>
        <td><?php echo $r->reporter; ?></td>
        <td><?php echo $r->domain; ?></td>
        <td><?php echo $r->total_pass; ?></td>
        <td><?php echo $r->total_fail; ?></td>
        <td><?php echo $r->policy_p; ?></td>
        <td><?php echo $r->policy_sp; ?></td>
        <td><?php echo $r->policy_pct; ?></td>
      </tr>
    <?php endforeach; endif; ?>
  </tbody>
</table>


<h2 id="dmarc-records">Records</h2>
<table width="100%" class="records">
  <thead>
    <tr>
      <th>Date</th>
      <th>From</th>
      <th>IP</th>
      <th>PTR</th>
      <th>Count</th>
      <th>Disposition</th>
      <th>DKIM</th>
      <th>SPF</th>
      <th>Notes</th>
      <th>View</th>
    </tr>
  </thead>
  <tbody>
<?php if($dmarc_records) : foreach($dmarc_records as $r) : ?>
    <tr <?php if($r->overall_result=='pass'):?>class="pass"<?php endif; ?>>
      <td><?php echo date('Y-m-d',mysql_to_unix($r->report_date));?></td>
      <td><?php echo $r->domain_full; ?></td>
      <td><?php echo $r->source_ip; ?></td>
      <td><span title="<?php echo str_replace('.',' .',$r->ptr); ?>" data-toggle="tooltip" data-placement="top"><?php echo ellipsize($r->ptr,25,.1); ?></span></td>
      <td><?php echo $r->count; ?></td>
      <td class="disposition_<?php echo strtolower($r->disposition); ?>"><?php echo $r->disposition; ?></td>
      <td><?php echo $r->dkim_result; ?></td>
      <td><?php echo $r->spf_result; ?></td>
      <td><?php echo $r->notes; ?></td>
      <td><a href="/reports/view-record/<?php echo $r->id; ?>">detail</a></td>
    </tr>
<?php endforeach; endif;  ?>
  </tbody>
</table>

<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Date', 'Pass', 'Quarantine', 'Reject', 'Fail'],
      <?php foreach(array_reverse($email_counts) as $c) : ?>
      ['<?php echo date('Y-m-d',mysql_to_unix($c->date)); ?>', <?php echo $c->none_pass; ?>, <?php echo $c->quarantine_fail; ?>, <?php echo $c->reject_fail; ?>, <?php echo $c->none_fail; ?>],
      <?php endforeach; ?>
    ]);
    var options = {
      legend: { position: 'right' },
      colors: ['#85994B','#f0ad4e','#D53880','#B10E1E'],
      seriesType: 'bars',
      isStacked: true
    };

    var chart = new google.visualization.ComboChart(document.getElementById('curve_chart'));

    chart.draw(data, options);
  }
</script>
