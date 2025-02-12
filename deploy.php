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
    ->set('remote_user', 'signage')
    ->set('deploy_path', '~/opensignage');

// Hooks

after('deploy:failed', 'deploy:unlock');
