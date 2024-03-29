<!DOCTYPE html>
<?php
/*
*
*  Copyright 2013 Science & Technology Facilities Council
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*      http://www.apache.org/licenses/LICENSE-2.0
*
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
*
*/

$URI = $_SERVER['REQUEST_URI'];

?>
<html lang="en">
  <head>
    <title>Mirror Server</title>
    <link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.min.css">
    <style type="text/css">
      td {
        padding: 2px 6px 2px 6px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="row">
<?php require('breadcrumbs.inc.php'); ?>
      </div>
<?php
   if (strpos($URI, '/raw/') === 0) {
?>
      <div class="hero-unit">
        <h1>Raw Mirrors</h1>
        <div class="alert alert-danger" role="alert">
        <h4><b>Very Danger!</b></h4>
        <p>These are raw, unprocessed mirrors as taken from upstream and should not normally be used directly by clients.</p>
        <p>There is no guarantee that these are even usable, or if they are now, that they will remain that way in future.</p>
        </div>
      </div>
<?php
    };
?>
      <div class="row">
        <ul class="nav nav-tabs nav-justified">
<?php

    if (strpos($URI, '/snapshot/') === 0 || strpos($URI, '/raw/snapshot/') === 0) {
        $array_uri = explode('/', $URI);
        $uri_snapshot_pos = array_search('snapshot', $array_uri) + 1;
        foreach (['previous', 'current', 'next'] as $p) {
            $c = '';
            if (strpos($URI, "/snapshot/$p/") !== false) {
                $c = 'active';
            };
            $s = explode('/', readlink('/data/pointers/'.$p))[5];
            $ua = $array_uri;
            $ua[$uri_snapshot_pos] = $p;
            $ut = implode('/', $ua);
            echo "        <li role=\"presentation\" class=\"$c\"><a href=\"$ut\" title=\"$s\"><b>$p</b><br>$s</a></li>\n";
        };
    };
?>
        </ul>
      </div>
      <div class="row">
        <div class="well">
