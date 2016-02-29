<?php

namespace AwayDaemon\Timer;

class Timer {
    private $time;
    private $callback;
    private $nextTime;
    private $timerManager;
    
    private function millisecond() {
        return round(microtime(true) * 1000);
    }
    
    public function __construct(int $time, callable $callback) {
        $this->time = $time;
        $this->callback = $callback;
        $this->nextTime = $this->millisecond() + $time;
    }
    
    public function setTime(int $time) {
        $this->time = $time;
        $this->nextTime = $this->millisecond() + $time;
    }
    
    public function getTime($time) {
        return $this->time;
    }
    
    public function setCallback(callable $callback) {
        $this->callback = $callback;
    }
    
    public function getCallback() {
        return $this->callback;
    }
    
    public function getNextTime() {
        return $this->nextTime;
    }
    
    public function setTimerManager(TimerManager $timerManager) {
        $this->timerManager = $timerManager;
    }
    
    public function getTimerManager() {
        return $this->timerManager;
    }
    
    public function getRemainingTime() {
        $remainingTime = $this->nextTime - $this->millisecond();
        return $remainingTime > 0 ? $remainingTime : 0;
    }
    
    public function test() {
        if (!$this->getRemainingTime()) {
            return true;
        } else {
            return false;
        }
    }
    
    public function run() {
        if ($this->test()) {
            call_user_func($this->callback);
            $this->nextTime = $this->millisecond() + $this->time;
            return true;
        }
        return false;
    }
}