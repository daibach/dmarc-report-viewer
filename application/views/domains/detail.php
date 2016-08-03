<ol class="breadcrumb">
  <li><a href="/">Home</a></li>
  <li><a href="/domains">Domains</a></li>
  <li><?php echo $domain_info->domain_full; ?></li>
</ol>

<h1>Domain: <span><?php echo $domain_info->domain_full; ?></h1>

<p class="info"><strong>Last  check:</strong> <?php echo $domain_info->last_checked; ?>
<br/><strong>SPF:</strong> <?php echo ui_produce_badge($domain_info->last_spf_result); ?>
<br/><strong>DMARC:</strong> <?php echo ui_produce_badge($domain_info->last_dmarc_result); ?></p>

<h2>SPF records</h2>

<table width="100%">
  <thead>
    <tr>
      <th>Date</th>
      <th>Record</th>
    </tr>
  </thead>
  <tbody>
    <?php if($spf_records) : ?>
    <?php foreach($spf_records as $record) : ?>
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


<h2>DMARC records</h2>

<table width="100%">
  <thead>
    <tr>
      <th>Date</th>
      <th>Record</th>
    </tr>
  </thead>
  <tbody>
    <?php if($dmarc_records) : ?>
    <?php foreach($dmarc_records as $record) : ?>
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
