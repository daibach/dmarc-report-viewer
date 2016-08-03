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
