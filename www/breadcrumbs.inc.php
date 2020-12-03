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
?>
      <ol class="breadcrumb">
        <li><a href='/'><span class="glyphicon glyphicon-home"></span></a></li>
<?php
  $path = explode("?", $_SERVER['REQUEST_URI']);
  $path = explode("/", trim($path[0], "/"));
  $pp = '';
  foreach ($path as $i => $p) {
    $pp .= '/'.$p;
    if ($i < sizeof($path) - 1) {
      echo "        <li><a href='$pp/'>$p</a></li>\n";
    } else {
      $p = str_replace('.php', '', $p);
      echo "        <li class='active'>$p</li>\n";
    }
  }
?>
      </ol>
