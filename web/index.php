<?php
include __DIR__ . '/../app/bootstrap.php';

use MyTimesheet\MyTimesheetDispatcher;

$dispatcher = new MyTimesheetDispatcher();
$dispatcher->predispatch();
$dispatcher->dispatch();
$dispatcher->postDispatch();
