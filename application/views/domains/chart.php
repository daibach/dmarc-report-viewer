$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

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
