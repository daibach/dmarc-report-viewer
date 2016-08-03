<?php

function ui_produce_badge($result) {
  if(is_null($result)) {
    return "<span class=\"label label-default\">Unknown</span>";
  } elseif($result) {
    return "<span class=\"label label-success\">Pass</span>";
  } else {
    return "<span class=\"label label-danger\">Fail</span>";
  }
}

function ui_produce_dmarc_badge($result) {
  if(is_null($result)) {
    return "<span class=\"label label-default\">Unknown</span>";
  } else {

    switch($result) {
      case 'EMPTY':
        return "<span class=\"label label-danger\">None</span>";
        break;
      case 'NONE':
        return "<span class=\"label label-warning\">p=none</span>";
        break;
      case 'QUARANTINE':
        return "<span class=\"label label-warning\">p=quarantine</span>";
        break;
      case 'REJECT':
        return "<span class=\"label label-success\">p=reject</span>";
        break;
      default:
        return "<span class=\"label label-info\">other</span>";
    }
  }
}
