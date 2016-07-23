<ol class="breadcrumb">
  <li><a href="/">Home</a></li>
  <li><a href="/reports">Reports</a></li>
  <li>Domain report: <?php echo $domain; ?></li>
</ol>

<h1>Domain: <span><?php echo $domain; ?></span></h1>

<div class="well">
  <div id="curve_chart" style="height: 500px"></div>
</div>

<h2>Counts</h2>
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
    <?php foreach($counts as $c) : ?>
      <tr>
        <td><?php echo date('Y-m-d',mysql_to_unix($c->date)); ?></td>
        <td><?php echo $c->total; ?></td>
        <td><?php echo $c->none_pass; ?></td>
        <td><?php echo $c->none_fail; ?></td>
        <td><?php echo $c->quarantine_fail; ?></td>
        <td><?php echo $c->reject_fail; ?></td>
        <td><?php echo $c->total_fail; ?></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<h2>DMARC reports for <?php echo $domain;?></h2>
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
    <?php foreach($reports as $r) : ?>
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
    <?php endforeach;?>
  </tbody>
</table>


<h2>Records</h2>
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
<?php foreach($records as $r) : ?>
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
<?php endforeach; ?>
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
      <?php foreach(array_reverse($counts) as $c) : ?>
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
