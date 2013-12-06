<?php

namespace Hypebeast\Bundle\WebBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class BowerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('store:bower:install')
            ->setDescription('Install all bower dependencies.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Installing bower dependencies.</info>');
        $output->writeln('');

        $rootDir = $this->getContainer()->get('kernel')->getRootDir();

        $process = new Process("cd $rootDir/../src/Hypebeast/Bundle/WebBundle/Resources/public && bower install");
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        $output->writeln('<info>Bower dependencies has been successfully installed.</info>');
    }
} 