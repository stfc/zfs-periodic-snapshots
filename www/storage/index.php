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

  function human_filesize($bytes, $decimals = 1) {
    $sz = 'BKMGTPZ';
    $factor = max(0, floor((strlen($bytes) - 1) / 3));
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor] . 'iB';
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Mirror Server</title>
    <link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.min.css">
  </head>
  <body>
    <div class="container">
<?php require('../breadcrumbs.inc.php'); ?>
<?php
  $stats = false;
  exec("/usr/sbin/zfs-list-snapshots-json", $stats);
  if ($stats) {
    $stats = json_decode($stats[0], true);

    echo "      <h2>Storage Pool</h2>\n";
    foreach ($stats['pool'] as $name => $details) {
      $size = $details['avail'] + $details['used'];
      $perc_used = $details['perc'];
      echo "      <div class='row'>\n";

      echo "      <p>";
      echo "<span class='col-md-2'><span class='glyphicon glyphicon-hdd'></span> <b>$name</b></span>";
      echo "<span class='col-md-2 text-danger'>Used ".human_filesize($details['used'])."</span>";
      echo "<span class='col-md-2 text-muted'>Available ".human_filesize($details['avail'])."</span>";
      echo "</p>\n";

      echo "      </div>\n";
      echo "      <div class='progress' title='Capacity profile of filesystem &quot;$name&quot;'>";
      echo "<div class='progress-bar progress-bar-danger' role='progressbar' style='width: $perc_used;'></div>";
      echo "</div>\n";

    }

    echo "      <h2>Filesystem</h2>\n";

    $filesystems = array_keys($stats['filesystems']);
    sort($filesystems);

    foreach ($filesystems as $name) {
      $details = $stats['filesystems'][$name];
      $name = explode('/', $name);
      $name = array_pop($name);
      $size = $details['avail'] + $details['used'];
      $perc_exposed = (int)(($details['refer'] / $size) * 100);
      $perc_used = (int)(($details['used'] / $size) * 100) - $perc_exposed;
      echo "      <div class='row'>\n";

      echo "      <p>";
      echo "<span class='col-md-2'><span class='glyphicon glyphicon-folder-close'></span> <b>$name</b></span>";
      echo "<span class='col-md-2 text-danger'>Used ".human_filesize($details['used'])."</span>";
      echo "<span class='col-md-2 text-info'>Exposes ".human_filesize($details['refer'])."</span>";
      echo "<span class='col-md-2 text-muted'>Available ".human_filesize($details['avail'])."</span>";
      echo "</p>\n";

      echo "      </div>\n";
      echo "      <div class='progress' title='Capacity profile of filesystem &quot;$name&quot;'>";
      echo "<div class='progress-bar progress-bar' role='progressbar' style='width: $perc_exposed%;'></div>";
      echo "<div class='progress-bar progress-bar-danger' role='progressbar' style='width: $perc_used%;'></div>";
      echo "</div>\n";

    }


    echo "      <h2>Snapshots</h2>\n";

    //Sort keys to ensure date ordering
    $snapshots = array_keys($stats['snapshots']);
    rsort($snapshots);

    foreach ($snapshots as $name) {
      $details = $stats['snapshots'][$name];
      $fs = explode('@', $name, 2)[0];
      $snap = explode('@', $name, 2)[1];
      $size = $stats['filesystems'][$fs]['avail'] + $stats['filesystems'][$fs]['used'];
      $refer = $details['refer'];
      $used = $details['used'];
      $perc_used = (int)(($used / $size) * 100);
      $perc_exposed = (int)(($refer / $size) * 100) - $perc_used;
      echo "      <div class='row'>\n";

      echo "      <p>";
      echo "<span class='col-md-2'><span class='glyphicon glyphicon-camera'></span> <b><a href='/snapshot/$snap/'>$snap</a></b></span>";
      echo "<span class='col-md-2 text-warning'>Delta ".human_filesize($details['used'])."</span>";
      echo "<span class='col-md-2 text-info'>Exposes ".human_filesize($details['refer'])."</span>";
      echo "</p>\n";

      echo "      </div>\n";
      echo "      <div class='progress' title='Storage profile of snapshot &quot;$snap&quot;'>";
      echo "<div class='progress-bar progress-bar-warning' style='width: $perc_used%;'></div>";
      echo "<div class='progress-bar' style='width: $perc_exposed%;'></div>";
      echo "</div>\n";
    }
  }
?>
    </div>
  </body>
</html>
