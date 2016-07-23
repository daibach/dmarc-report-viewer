<ol class="breadcrumb">
  <li><a href="/">Home</a></li>
  <li><a href="/reports">Reports</a></li>
  <li><a href="/reports/dmarc-aggregate-reports/<?php echo $record->domain; ?>">Aggregate Reports</a></li>
  <li><a href="/reports/dmarc-aggregate-report/<?php echo $record->report_id; ?>"><?php echo($record->domain); ?> (#<?php echo $record->report_id; ?>)</a></li>
  <li>Record #<?php echo $record->id; ?></li>
</ol>

<h1>Record: <span><?php echo $record->domain_full; ?></span> / <span><?php echo $record->source_ip; ?></span> (#<?php echo $record->id;?>)</h1>

<p class="info"><strong>From report:</strong> <a href="/reports/dmarc-aggregate-report/<?php echo $record->report_id; ?>"><?php echo($record->domain); ?> (#<?php echo $record->report_id; ?>)</a>
  <br/><strong>By:</strong> <?php echo $record->reporter; ?>
  <br/><strong>On:</strong> <?php echo date('Y-m-d', mysql_to_unix($record->report_date)); ?>
  <br/><strong>Count:</strong> <?php echo $record->count; ?></p>

<?php
$doc = json_decode($record->full_record);
?>

<h2>Policy evaluated</h2>
<samp>
  <?php echo json_encode($doc->row->policy_evaluated); ?>
</samp>

<h2>Identifiers</h2>
<samp>
  <?php echo json_encode($doc->identifiers); ?>
</samp>

<h2>Auth results</h2>
<samp>
  <?php echo json_encode($doc->auth_results); ?>
</samp>

<h2>Full record</h2>
<samp>
<?php echo $record->full_record; ?>
</samp>
