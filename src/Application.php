<?php
namespace AwayDaemon;

use AwayDaemon\Timer\Timer;
use AwayDaemon\Timer\TimerManager;
use AwayDaemon\Event\EventManager;

class Application {
    
    private $name = 'AwayDaemon';
    private $version = '0.0.1';
    
    private $timerManager = null;
    private $eventManager = null;
    
    private $idleList = [];
    private $idleIndex = 0;
    
    private $runningStatus = 0; /* 0: No running, 1: Running, 2: Stoping, 3: Stoped */
    
    public function __construct($name, $version = '0.0.1') {
        $this->name = $name;
        $this->version = $version;
        
        $this->timerManager = new TimerManager();
        $this->eventManager = new EventManager();
    }
    
    public function addIdle(callable $callback) {
        $this->idleList[$this->idleIndex] = $callback;
        return $this->idleIndex ++;
    }
    
    public function removeIdle($id) {
        if (isset($this->idleList[$id])) {
            unset($this->idleList[$id]);
            return true;
        }
        return false;
    }
    
    public function getTimerManager() {
        return $this->timerManager;
    }
    
    public function getEventManager() {
        return $this->eventManager;
    }
    
    public function quit() {
        $this->runningStatus = 2;
    }
    
    public function run() {
        $this->runningStatus = 1;
        
        $this->eventManager->emit('init');
        $this->eventManager->emit('beforeLoop');
        
        while (1) {
            /* Running Idle */
            foreach($this->idleList as $id => $callback) {
                call_user_func($callback);
                $this->removeIdle($id);
            }
            
            /* Running Timeout and Interval*/
            $this->timerManager->run();
            
            if ($this->runningStatus === 2) {
                break;
            }
            
            if (!empty($this->idleList)) {
                continue;
            }
            
            usleep($this->timerManager->getMinNextTime() * 1000);
        }
        $this->eventManager->emit('afterLoop');
        $this->eventManager->emit('shutdown');
        $this->runningStatus = 3;
    }
}