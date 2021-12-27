@servers(['staging' => 'server ip', 'prod' => 'server ip'])

@setup
$type = $type ?: "staging";
$user = ($type === "staging") ? "staging user" : "prod user";
$rootDir = "cd /home/{$user}/public_html";

$teamsHook = "put MS Teams here";
@endsetup

@story('deploy')
@if ($type === "staging")
sdeploy
@elseif($type === "prod")
pdeploy
@endif
reset-server
@endstory

@task('sdeploy', ['on' => 'staging'])
echo $rootDir
echo "+------------------------------------------------------------------------------------------------------------+"
echo "| Root Dir: $(pwd)"
echo "+------------------------------------------------------------------------------------------------------------+"
echo "| Pulling new updates..."
echo "| $(git pull origin develop)"
echo "+------------------------------------------------------------------------------------------------------------+"
echo "| $(composer install --optimize-autoloader)"
@endtask

@task('pdeploy', ['on' => 'prod'])
echo $rootDir
echo "+------------------------------------------------------------------------------------------------------------+"
echo "| Root Dir: $(pwd)"
echo "+------------------------------------------------------------------------------------------------------------+"
echo "| Pulling new updates..."
echo "+------------------------------------------------------------------------------------------------------------+"
git pull origin master
echo "+------------------------------------------------------------------------------------------------------------+"
composer install --no-dev --optimize-autoloader
@endtask

@task('reset-server', ['on' => $type])
echo $rootDir
echo "+------------------------------------------------------------------------------------------------------------+"
echo "| $(php artisan migrate --force)"
echo "+------------------------------------------------------------------------------------------------------------+"
echo "| $(php artisan optimize:clear)"
echo "+------------------------------------------------------------------------------------------------------------+"
echo "| $(composer dump-autoload -o)"
echo "+------------------------------------------------------------------------------------------------------------+"
echo "| $(php artisan queue:restart)"
echo "+------------------------------------------------------------------------------------------------------------+"
@endtask

@success
@microsoftTeams($teamsHook, "Fresh code has been deployed to Osimi {$type} server");
@endsuccess

@error
@microsoftTeams($teamsHook, "Something wrong has been happened on deployment to Osimi {$type} server!", 'error');
@enderror
