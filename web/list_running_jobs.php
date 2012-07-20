<?php

require_once("./config.default.php");
# If there are any overrides include them now
if ( ! is_readable('./config.php') ) {
    echo("<H2>WARNING: Configuration file config.php does not exist. Please
         notify your system administrator.</H2>");
} else
    include_once('./config.php');

require_once 'lib/couch.php';
require_once 'lib/couchClient.php';
require_once 'lib/couchDocument.php';

// set a new connector to the CouchDB server
$client = new couchClient ('http://' . $couchdb_server . ':' . $couchdb_port, $couchdb_database );

// view fetching, using the view option limit
try {
#    $view = $client->limit(100)->asArray()->getView('cronview','by_host');
    $client->key(-1);
    $view = $client->limit(100)->asArray()->getView('cronview','by_job_duration');
} catch (Exception $e) {
    echo "ERROR while getting view contents: ".$e->getMessage()."<BR>\n";
}


##############################################################
# Did we get any response for the key we were looking for
##############################################################
if ( sizeof($view["rows"]) > 0 ) {

  print "<h2>Currently Running Jobs</h2><p>
  <table cellspacing=1 class=tablesorter border=1>
  <thead>
  <tr><th>Start time</th><th>Has been running so far (secs)</th><th>Username</th><th>Hostname</th><th>Command</th></tr>
  </thead>
  <tbody>";

  foreach ( $view["rows"] as $key => $row ) {
  
    $job_time = time() - $row["value"]["time"];
  
    print "<tr><td>" . $row["value"]["time"] . "</td>" .
    "<td align=center>" . $job_time . "</td>" .    
    "<td>" . $row["value"]["username"] . "</td>" .
    "<td>" . $row["value"]["hostname"] . "</td>" .
    "<td>" . $row["value"]["command_line"] . "</td>" .
    "</tr>\n";
  }

  print "</tbody></table>";

} else {

  print "No jobs running";

}

?>