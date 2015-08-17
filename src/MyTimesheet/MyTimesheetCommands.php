<?php
namespace MyTimesheet;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Adem\Sjb\Commands\ImportUsers;
use Adem\Sjb\Commands\ListUsers;
use Adem\Sjb\Commands\CreateUsers;
use Adem\Sjb\Commands\DeleteUsers;
use Adem\Sjb\Commands\UpdateUsers;
use Adem\Sjb\Commands\ListFields;
use Adem\Sjb\Commands\InitFields;

/**
 * @package Adem\Sjb
 */
class MyTimesheetCommands extends Application
{
	
	public function __construct()
	{
		parent::__construct('Adem/Sjb Commands', '0.1 (dev)');
	}
	
    /**
     * Récupère les commandes par défaut qui sont toujours disponibles.
     * @return array Un tableau d'instances de commandes par défaut
     */
    protected function getDefaultCommands()
    {
        // Conserve les commandes par défaut du noyau pour avoir la
        // commande HelpCommand en utilisant l'option --help
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new InitFields();
		
        return $defaultCommands;
    }
}