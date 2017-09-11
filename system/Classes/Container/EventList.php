<?php

namespace Asylamba\Classes\Container;

class EventList
{
    # contient des ArrayList('date', 'eventType', 'eventId')
    # format de la date : 'AAAA-MM-JJ HH:MM:SS' - SQL FORMAT
    private $events = array();

    /**
     * @return int
     */
    public function size()
    {
        return count($this->events);
    }

    /**
     * @param int $index
     * @return ArrayList
     */
    public function get($index = 0)
    {
        if (isset($this->events[$index])) {
            return $this->events[$index];
        }
        return null;
    }

    /**
     * @param string $date
     * @return \Asylamba\Classes\Container\StackList
     */
    public function getPastEvents($date)
    {
        $past = new StackList();
        foreach ($this->events as $e) {
            if ($e->get('date') <= $date) {
                $past->append($e);
            }
        }
        return $past;
    }

    /**
     * @param string $date
     */
    public function clearPastEvents($date)
    {
        $size = $this->size() - 1;
        for ($i = $size; $i >= 0; $i--) {
            if ($this->events[$i]->get('date') <= $date) {
                $this->remove($i);
                $i--;
            }
        }
    }

    /**
     * @param string $date
     * @param string $eventType
     * @param string $eventId
     * @param string $eventInfo
     */
    public function add($date, $eventType, $eventId, $eventInfo = null)
    {
        $event = new ArrayList();
        $event->add('date', $date);
        $event->add('eventType', $eventType);
        $event->add('eventId', $eventId);
        $event->add('eventInfo', $eventInfo);

        $index = 0;
        $found = false;
        foreach ($this->events as $e) {
            if ($e->get('date') > $date) {
                $found = true;
                break;
            }
            $index++;
        }
        if ($found) {
            $begin = array_slice($this->events, 0, $index);
            $begin[] = $event;
            $end = array_slice($this->events, $index);
            $this->events = array_merge($begin, $end);
        } else {
            $this->events[] = $event;
        }
    }

    /**
     * @param int $index
     * @return boolean
     */
    public function remove($index)
    {
        if ($index < 0) {
            $index = count($this->events) + $index;
        }
        if (!isset($this->events[$index])) {
            return false;
        }
        $begin = array_slice($this->events, 0, $index);
        $end = array_slice($this->events, $index+1);
        $this->events = array_merge($begin, $end);
    }

    public function clear()
    {
        $this->events = array();
    }
}
