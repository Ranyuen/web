<?php
/**
 * CUI admin account manager.
 */
require 'vendor/autoload.php';

use Ranyuen\App;
use Ranyuen\Model\Admin;

function doHelp()
{
    echo "* manage_admin.php *
Ctrl^C to quit.

help                      \t:Print this help.
list                      \t:List admin usernames.
add USERNAME PASSWORD     \t:Add a new user.
rm USERNAME               \t:Delete the user.
password USERNAME PASSWORD\t:Change password.
exit                      \t:Quit.
quit                      \t:Quit.
";
}

function doList()
{
    $admins = Admin::all();
    foreach ($admins as $admin) {
        echo "$admin->username\n";
    }
}

function doAdd($option)
{
    $matches = [];
    if (!preg_match('/^\s*([-_.A-Za-z0-9]{4,100}) ([ -~]{8,1024})$/', $option, $matches)) {
        echo 'Invalid USERNAME or PASSWORD.';

        return;
    }
    $username = $matches[1];
    $rawPassword = $matches[2];
    $admin = new Admin();
    $admin->username = $username;
    $admin->setPassword($rawPassword);
    $admin->save();
    echo 'ok.';
}

function doRm($option)
{
    $username = trim(explode(' ', trim($option))[0]);
    if (!$username) {
        echo 'ok.';

        return;
    }
    $admin = Admin::where('username', $username)->first();
    if ($admin) {
        $admin->delete();
    }
    echo 'ok.';
}

function doPassword($option)
{
    $matches = [];
    if (!preg_match('/^\s*(\S+) ([ -~]{8,1024})$/', $option, $matches)) {
        echo 'Invalid PASSWORD.';

        return;
    }
    $username = $matches[1];
    $rawPassword = $matches[2];
    $admin = Admin::where('username', $username)->first();
    if ($admin) {
        $admin->setPassword($rawPassword);
        $admin->save();
    }
    echo 'ok.';
}

if (isset($_SERVER['REMOTE_ADDR'])) {
    header('Location: /');
    exit();
}
$c = (new App())->getContainer();
$c['db'];
doHelp();
while (true) {
    echo '> ';
    $command = fgets(STDIN);
    $command = trim($command);
    if (in_array($command, ['help', 'h'])) {
        doHelp();
    } elseif (in_array($command, ['list', 'l'])) {
        doList();
    } elseif (0 === strpos($command, 'add ')) {
        doAdd(mb_substr($command, 4));
    } elseif (0 === strpos($command, 'rm ')) {
        doRm(mb_substr($command, 3));
    } elseif (0 === strpos($command, 'password ')) {
        doPassword(mb_substr($command, 9));
    } elseif (0 === strpos($command, 'pw ')) {
        doPassword(mb_substr($command, 3));
    } elseif (in_array($command, ['exit', 'quit', 'q'])) {
        exit();
    } else {
        doHelp();
    }
}
