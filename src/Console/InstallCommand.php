<?php

namespace Chaos\Majordomo\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'majordomo:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the commands necessary to prepare Majordomo for use';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @author moell<moel91@foxmail.com>
     */
    public function handle()
    {
        $this->call('vendor:publish', ['--provider' => 'Spatie\Permission\PermissionServiceProvider']);
        $this->call('vendor:publish', ['--provider' => 'Chaos\Majordomo\MajordomoServiceProvider']);
    }
}
