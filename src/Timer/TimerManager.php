<?php

namespace AwayDaemon\Timer;

class TimerManager {
    private $application;
    private $timeoutList = [];
    private $intervalList = [];
    
    public function __construct(\AwayDaemon\Application $application = null) {
        $this->application = $application;
    }
    
    public function getApplication() {
        return $this->application;
    }
    
    public function addTimeout(Timer $timer) {
        $this->timeoutList[] = $timer;
        $timer->setTimerManager($this);
        return $timer;
    }
    
    public function removeTimeout(Timer $timer) {
        $key = array_search($timer, $this->timeoutList, TRUE);
        if ($key !== FALSE) {
            unset($this->timeoutList[$key]);
            return true;
        }
        return false;
    }
    
    public function addInterval(Timer $timer) {
        $this->intervalList[] = $timer;
        $timer->setTimerManager($this);
        return $timer;
    }
    
    public function removeInterval(Timer $timer) {
        $key = array_search($timer, $this->intervalList, TRUE);
        if ($key !== FALSE) {
            unset($this->intervalList[$key]);
            return true;
        }
        return false;
    }
    
    public function getMinNextTime() {
        $minNextTime = 5000;
        foreach($this->timeoutList as $timeout) {
            $minNextTime = min($timeout->getRemainingTime(), $minNextTime);
        }
        
        foreach($this->intervalList as $interval) {
            $minNextTime = min($interval->getRemainingTime(), $minNextTime);
        }
        return $minNextTime;
    }
    
    public function run() {
        foreach($this->timeoutList as $key => $timeout) {
            if ($timeout->run()) {
                unset($this->timeoutList[$key]);
            }
        }
        
        foreach($this->intervalList as $key => $interval) {
            $interval->run();
        }
    }
}