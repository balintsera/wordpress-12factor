PROJECT_ROOT=.
WPCLI=$PROJECT_ROOT/vendor/bin/wp
composer install --no-dev -d $PROJECT_ROOT

# plugin deactivation

PLUGINS_TO_DEACTIVATE='
wordpress-seo
'

for curr_plugin in $PLUGINS_TO_DEACTIVATE
do
  $WPCLI plugin deactivate $curr_plugin
done;

# plugin activation

PLUGINS_TO_ACTIVATE='
#w3-total-cache
'
for curr_plugin in $PLUGINS_TO_ACTIVATE
do
  $WPCLI plugin activate $curr_plugin
done;

# css version
$WPCLI eval "\$cssVersion = shell_exec('git log --pretty=format:\'%h\' -n 1'); if(get_option('aion_css_version') !== \$cssVersion) { update_option('pass_css_version', \$cssVersion); }"

# totalcache bug: won't clear cache so we empty cache dir
$WPCLI cache flush

php -r 'if(function_exists("opcache_reset")) opcache_reset(); else echo "no opcache found";'

echo "all postmerge task ran successfully"