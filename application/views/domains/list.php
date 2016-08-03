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
    </tr>
  </thead>
  <tbody>
  <?php foreach($domains as $d) : ?>
    <tr>
      <td><a href="/domains/detail/<?php echo $d->domain_full; ?>"><?php echo $d->domain_full; ?></a></td>
      <td><?php echo ui_produce_badge($d->last_spf_result); ?></td>
      <td><?php echo ui_produce_badge($d->last_dmarc_result); ?></td>
    </tr>
  <?php endforeach;?>
  </tbody>
</table>
