<ol class="breadcrumb">
  <li><a href="/">Home</a></li>
  <li><a href="/domains">Domains</a></li>
</ol>

<h1>Domains</h1>

<table width="100%">
  <thead>
    <tr>
      <th>Domain</th>
      <th>SPF</th>
      <th>DMARC</th>
      <th>Avg Sent</th>
      <th>This Wk</th>
      <th>This Wk Pass</th>
      <th>Last Wk</th>
      <th>Last Wk Pass</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($domains as $d) : ?>
    <tr>
      <td><a href="/domains/detail/<?php echo $d->domain_full; ?>"><?php echo $d->domain_full; ?></a></td>
      <td><?php echo ui_produce_badge($d->last_spf_result); ?></td>
      <td><?php echo ui_produce_dmarc_badge($d->last_dmarc_result); ?></td>
      <?php if($d->received_dmarc_reports) : ?>
      <td><?php echo $d->avg_weekly_sent; ?></td>
      <td><?php echo $d->this_week_sent; ?></td>
      <td><?php echo $d->this_week_pass_pct;?>%</td>
      <td><?php echo $d->last_week_sent; ?></td>
      <td><?php echo $d->last_week_pass_pct;?>%</td>
      <?php else : ?>
      <td></td><td></td><td></td><td></td><td></td>
      <?php endif; ?>
    </tr>
  <?php endforeach;?>
  </tbody>
</table>
