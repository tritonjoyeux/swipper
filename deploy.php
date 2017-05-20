<?php
namespace Deployer;
require 'recipe/common.php';

// Configurationœ
set('ssh_type', 'native');
//set('ssh_multiplexing', true);

set('repository', 'git@gitlab.fashiongroup.com:fashiongroup/swiper.git');
set('shared_files', ['config/parameters.yml']);
set('shared_dirs', ['data', 'logs']);
set('writable_dirs', ['data', 'logs', 'cache']);

// Servers
server('preprod', '46.105.110.134')
    ->user('root')
    ->forwardAgent()
    ->identityFile()
    ->set('deploy_path', '/home/swiper');

server('cron', '178.33.237.58')
    ->user('root')
    ->forwardAgent()
    ->identityFile()
    ->set('deploy_path', '/home/swiper');

// Tasks
desc('Deploy your project');
task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    //'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
