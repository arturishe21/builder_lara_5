<?php

namespace Vis\Builder;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Console\Command;

/**
 * Class GeneratePassword.
 */
class GeneratePassword extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:generatePassword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate password for admin';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $newPassword = $this->getNewPassword();

        $userAdmin = Sentinel::findById(1);
        Sentinel::update($userAdmin, ['password' => $newPassword]);

        $this->info('Access in cms: ');
        $this->info('Login: admin@vis-design.com');
        $this->info('Password: '.$newPassword);
    }

    /**
     * generate new password
     *
     * @return string
     */

    private function getNewPassword() : string
    {
        $letters = collect(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R',
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0' ]);

        return implode("", $letters->random(10)->toArray());
    }
}
