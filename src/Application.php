<?php
namespace AwayDaemon;

use AwayDaemon\Timer\Timer;
use AwayDaemon\Timer\TimerManager;
use AwayDaemon\Event\EventManager;

class Application {
    const NO_RUNNING = 0;
    const RUNNING = 1;
    const STOPPING = 2;
    const STOPPED = 3;
    
    private $name = 'AwayDaemon';
    private $version = '0.0.1';
    
    private $timerManager = null;
    private $eventManager = null;
    
    private $idleList = [];
    private $idleIndex = 0;
    
    private $runningStatus = Application::NO_RUNNING;
    
    public function __construct($name, $version = '0.0.1') {
        $this->name = $name;
        $this->version = $version;
        
        $this->timerManager = new TimerManager($this);
        $this->eventManager = new EventManager($this);
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
        $this->runningStatus = Application::STOPPING;
    }
    
    public function run() {
        $this->runningStatus = Application::RUNNING;
        
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
            
            if ($this->runningStatus === Application::STOPPING) {
                break;
            }
            
            if (!empty($this->idleList)) {
                continue;
            }
            
            usleep($this->timerManager->getMinNextTime() * 1000);
        }
        $this->eventManager->emit('afterLoop');
        $this->eventManager->emit('shutdown');
        $this->runningStatus = Application::STOPPED;
    }
}