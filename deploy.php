<?php
header("Content-Type: text/plain", true, 200);
echo "Deploying...\n";
$projectRoot = __DIR__ + '/../';
chdir($projectRoot);
// A user must be set up who can write the docroot 
if (empty(getenv('DEPLOY_USER'))) {
  echo 'no user set';
}
$deployUser = getenv('DEPLOY_USER');
// Run deploy
$cleanPull = '(sudo -u '. $deployUser . ' -S git reset --hard HEAD && sudo -u ' . $deployUser . ' -S git pull ) 2>&1';
exec($cleanPull, $out, $result);
// Show the results
echo "Exit code was: " . $result . "\n" . "Results:\n" . implode("\n", $out);
