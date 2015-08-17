<?php
namespace MysTimesheet\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;

/**
 * @package MysTimesheet\Commands
 */
class ExportTimesheet extends Command
{
	protected function configure()
	{
		$this
		->setName('timesheet:export')
		->setDescription('Exporter un timesheet')
		->addArgument('timesheetName');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		
	}
}