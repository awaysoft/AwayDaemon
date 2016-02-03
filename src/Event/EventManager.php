<?php

namespace AwayDaemon\Event;

use AwayDaemon\Event\Event;

class EventManager {
    private $eventList = [];
    private $eventIndex = 0;
    
    public function addListener(string $eventName, callable $callback) {
        $this->eventList[$this->eventIndex] = [$eventName, $callback];
        return $this->eventIndex ++;
    }
    
    public function removeListener(&$event) {
        if (isset($this->eventList[$id])) {
            unset($this->eventList[$id]);
            return true;
        }
        return false;
    }
    
    public function emit($eventName, $params = null) {
        foreach ($this->eventList as $event) {
            if ($event[0] === $eventName) {
                $event[1]($params);
            }
        }
    }
}