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
  exec("sudo /usr/local/sbin/zfs-list-snapshots-json", $stats);
  if ($stats) {
    $stats = json_decode($stats[0], true);


    echo "      <h2>Storage Pool</h2>\n";

    $name = 'data';
    $details = $stats['pool'][$name];
    $poolsize = $details['size'];
    $poolused = $details['used'];
    $poolperc = $details['perc'];
    echo "      <div class='row'>\n";

    echo "      <p>";
    echo "<span class='col-md-2'><span class='glyphicon glyphicon-hdd'></span> <b>$name</b></span>";
    echo "<span class='col-md-2 text-error'>Used ".human_filesize($details['used'])."</span>";
    echo "<span class='col-md-2 text-success'>Free ".human_filesize($details['avail'])."</span>";
    echo "</p>\n";

    echo "      </div>\n";
    echo "      <div class='progress' title='Capacity profile of storage pool &quot;$name&quot;'><div class='progress-bar progress-bar-danger' role='progressbar' style='width: $poolperc;'></div></div>\n";


    echo "      <h2>Snapshots</h2>\n";

    //Sort keys to ensure date ordering
    $snapshots = array_keys($stats['snapshots']);
    rsort($snapshots);

    foreach ($snapshots as $name) {
      $details = $stats['snapshots'][$name];
      $name = str_replace("data/mirrors@", "", $name);
      $eff = (int)(($details['used'] / $details['refer']) * 100);
      echo "      <div class='row'>\n";

      echo "      <p>";
      echo "<span class='col-md-2'><span class='glyphicon glyphicon-camera'></span> <b><a href='/snapshot/$name/'>$name</a></b></span>";
      echo "<span class='col-md-2 text-error'>Occupies ".human_filesize($details['used'])."</span>";
      echo "<span class='col-md-2 text-info'>Exposes ".human_filesize($details['refer'])."</span>";
      echo "</p>\n";

      echo "      </div>\n";
      echo "      <div class='progress' title='Storage profile of snapshot &quot;$name&quot;'><div class='progress-bar progress-bar-danger' style='width: $eff%;'></div><div class='progress-bar' style='width: ".(100-$eff)."%;'></div></div>\n";
    }
  }
?>
    </div>
  </body>
</html>
