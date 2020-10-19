<?php

function LethePager($count,$per_page,$current = null){
  $pages = max(1,ceil($count/$per_page));
  $current = min(max(1,(int) $current),$pages);
  $offset = ($current - 1) * $per_page;
  return array(
    "pages"     => (int) $pages,
    "total"     => (int) $count,
    "current"   => (int) $current,
    "previous"  => ($current > 1 ? $current - 1 : false),
    "next"      => ($current < $pages ? $current + 1 : false),
    "offset"    => $offset,
    "limit"     => $per_page,
    "start"     => $count ? $offset + 1 : 0,
    "end"       => ($offset + $per_page <= $count ? $offset + $per_page : $count)
  );
}

?>