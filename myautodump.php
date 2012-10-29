<?php

  openlog('myautodump', LOG_PID, LOG_USER);
  syslog(LOG_INFO, '========== MyAutoDump is starting ==========');
  
  // Load and parse the configuration file
  $config = @parse_ini_file(dirname(__FILE__) . '/config.ini', TRUE);
  if (empty($config)) {
    die("The file config.ini is missing or malformed.\n\n");
  }

  // Connect to the MySQL server; ask for the list of databases
  $dbh = mysql_connect(
    $config['mysql']['server'],
    $config['mysql']['username'],
    $config['mysql']['password']
  );
  $result = mysql_query('SHOW DATABASES', $dbh);

  // Loop through the result set and extract each database name
  $databases = array();
  while (FALSE !== ($row = mysql_fetch_assoc($result))) {
    $databases = array_merge($databases, array_values($row));
  }
  
  mysql_close($dbh);

  // Loop over each discovered database
  foreach ($databases as $db) {
    $out_file = $config['output']['path'] . '/' . $db . '.sql.gz';
    
    // Build the command that will dump the current DB
    $command  = escapeshellcmd($config['mysql']['mysqldump']);
    $command .= ' --user='     . escapeshellarg($config['mysql']['username']);
    $command .= ' --password=' . escapeshellarg($config['mysql']['password']);
    if (isset($config['db_options'][$db])) {
      // User has some custom args to give mysqldump
      $command .= ' ' . escapeshellcmd($config['db_options'][$db]);
    }
    $command .= ' '   . escapeshellarg($db);
    $command .= ' | ' . escapeshellcmd($config['output']['gzip']);
    $command .= ' > ' . escapeshellcmd($out_file);
    
    // Run the dump; and time how long it took
    $start = microtime(TRUE);
    exec($command);
    chmod($out_file, 0600);
    $duration = round(microtime(TRUE) - $start, 2);
    
    syslog(LOG_INFO, "Dumped `$db` to $out_file ($duration s)");
  }
  
  // Not going to need the log anymore
  syslog(LOG_INFO, '========== MyAutoDump is finished ==========');
  closelog();

?>