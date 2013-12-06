<?php

namespace Hypebeast\Bundle\WebBundle\Composer;

use Composer\Script\CommandEvent;
use Symfony\Component\Process\Process;

class ScriptHandler 
{
    public static function installBower(CommandEvent $event)
    {
        $process = new Process("cd src/Hypebeast/Bundle/WebBundle/Resources/public && bower install");
        $process->run(function ($type, $buffer) { echo $buffer; });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('An error occurred when installing Bower dependencies.');
        }
    }
} 