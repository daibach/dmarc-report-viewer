<ol class="breadcrumb">
  <li><a href="/">Home</a></li>
  <li><a href="/reports">Reports</a></li>
  <li><a href="/reports/dmarc-aggregate-reports">Aggregate Reports</a></li>
  <li>Aggregate report #<?php echo $report->id.' for '.$report->domain; ?></li>
</ol>

<h1>Aggregate report for <span><?php echo $report->domain; ?></span> (#<?php echo $report->id;?>)</h1>
<p class="info"><strong>Report from:</strong> <?php echo $report->reporter; ?> (<?php echo $report->report_id; ?>)
<br/><strong>On:</strong> <?php echo date('Y-m-d', mysql_to_unix($report->report_date)); ?>
<br/><strong>Covering:</strong> <?php echo date('Y-m-d H:i:s', mysql_to_unix($report->report_start)); ?> to <?php echo date('Y-m-d H:i:s', mysql_to_unix($report->report_end)); ?>
<br/><strong>Total pass:</strong> <?php echo $report->total_pass; ?>
<br/><strong>Total fail:</strong> <?php echo $report->total_fail; ?></p>

<h2>Policy</h2>
<pre>p=<?php echo $report->policy_p; ?>; sp=<?php echo $report->policy_sp; ?>; adkim=<?php echo $report->policy_adkim; ?>; aspf=<?php echo $report->policy_aspf; ?>; pct=<?php echo $report->policy_pct; ?>;</pre>

<h2>Report metadata</h2>
<samp>
  <?php echo $report->raw_metadata; ?>
</samp>

<h2>Policy published</h2>
<samp>
  <?php echo $report->raw_policy; ?>
</samp>

<h2>Records</h2>
<table width="100%" class="records">
  <thead>
    <tr>
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
      <td><?php echo $r->domain_full; ?></td>
      <td><?php echo $r->source_ip; ?></td>
      <td><span title="<?php echo str_replace('.',' .',$r->ptr); ?>" data-toggle="tooltip" data-placement="top"><?php echo ellipsize($r->ptr,30,.1); ?></span></td>
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
