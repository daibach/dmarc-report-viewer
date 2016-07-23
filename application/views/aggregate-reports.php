<ol class="breadcrumb">
  <li><a href="/">Home</a></li>
  <li><a href="/reports">Reports</a></li>
  <li>Aggregate Reports</li>
</ol>

<h1>DMARC aggregate reports for <span><?php echo $domain; ?></span></h1>
<table width="100%">
  <thead>
    <tr><th>Date</th><th>Reporter</th><th>Pass</th><th>Fail</th><th>p</th><th>sp</th><th>%</th></tr>
  </thead>
  <tbody>
    <?php foreach($reports as $r) : ?>
      <tr>
        <td><a href="/reports/dmarc-aggregate-report/<?php echo $r->id; ?>"><?php echo date('d M Y',mysql_to_unix($r->report_date));?></a></td>
        <td><?php echo $r->reporter; ?></td>
        <td><?php echo $r->total_pass; ?></td>
        <td><?php echo $r->total_fail; ?></td>
        <td><?php echo $r->policy_p; ?></td>
        <td><?php echo $r->policy_sp; ?></td>
        <td><?php echo $r->policy_pct; ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
