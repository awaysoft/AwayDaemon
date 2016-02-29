<?php

require "../vendor/autoload.php";

use AwayDaemon\Application;
use AwayDaemon\Timer\Timer;
use AwayDaemon\Timer\TimerManager;
use AwayDaemon\Event\EventManager;

class App extends Application {
    private $timeout;
    private $interval;
    private $startEvent;
    private $endEvent;
    
    public function idleCallback() {
        echo "Call Idle\n";
    }
    
    public function timeoutCallback() {
        echo "Call Timeout\n";
        $this->addIdle(array($this, "idleCallback"));
        //var_dump($this->interval->getTimerManager());
        //var_dump($this->interval->getTimerManager()->getApplication());
    }
    
    public function intervalCallback() {
        static $count = 0;
        echo "Call Interval\n";
        if (++$count == 5) {
            $this->quit();
        }
    }
    
    private function init() {
        $this->timeout = new Timer(5000, array($this, "timeoutCallback"));
        $this->interval = new Timer(2000, array($this, "intervalCallback"));
        
        $this->getTimerManager()->addTimeout($this->timeout);
        $this->getTimerManager()->addInterval($this->interval);
        
        $beforeLoopCallback = function() {
            echo "Emit beforeLoop\n";
        };
        
        $afterLoopCallback = function() {
            echo "Emit afterLoop\n";
        };
        
        $this->getEventManager()->addListener('beforeLoop', $beforeLoopCallback);
        
        $this->getEventManager()->addListener('afterLoop', $afterLoopCallback);
    }
    
    public function run() {
        $this->init();
        parent::run();
    }
}


$app = new App('Test');
$app->run();