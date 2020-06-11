<?php
namespace Queue\Queue\Pgq;

use pgq\PGQConsumer;

//define("CONFIGURATION", __DIR__."/testq.conf");

class Queue extends PGQConsumer
{
  public function config( )
  {
    unset($Config);
    if( $this->log !== null )
      $this->log->notice("Reloading configuration (HUP) from '%s'", CONFIGURATION);

    global $Config;
    require(CONFIGURATION);

    $this->loglevel = $Config["LOGLEVEL"];
    $this->logfile  = $Config["LOGFILE"];
    $this->delay    = $Config["DELAY"];
  }

  public function process_event(&$event)
  {
      /*
    /* Just log about it */
    $event_id	= $event->id;
    $id		= $event->data["id"];
    $data	= $event->data["data"];

    $this->log->notice("processing event %d [%d - %s]",
		       $event_id, $id, $data);
    */
    return PGQ_EVENT_OK;
  }
}

//$myTestq = new Testq("testq", "libphp", $argc, $argv, "host=localhost");
?>
