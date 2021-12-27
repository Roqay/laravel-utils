@servers(['staging' => 'osimi', 'prod' => 'posimi'])

@setup
$type = $type ?: "staging";
$user = ($type === "staging") ? "osaimiautoroqay" : "alosaimiauto";
$rootDir = "cd /home/{$user}/public_html";

$teamsHook =
"https://thetrans4mers.webhook.office.com/webhookb2/3993c23a-4f47-4c2f-a4c7-469d46fd88e7@ff78c7d4-c804-4473-a943-a9c2c521a03b/IncomingWebhook/d6fb1f6d30ab4f66b1dfd64aae385c7d/94741466-66d9-4a7a-aac1-6294a364e1f6";
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
