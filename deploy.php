<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'recipe/provision.php';



// Config

set('repository', 'git@github.com:Thiritin/open-signage.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('10.0.1.110')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/opensignage');

task('npm:build', function () {
    cd('{{release_path}}');
    run('/home/deployer/.nvm/versions/node/v22.14.0/bin/npm install');
    run('/home/deployer/.nvm/versions/node/v22.14.0/bin/npm run build');
});

// Hooks

after('deploy:failed', 'deploy:unlock');
after('deploy', 'npm:build');

