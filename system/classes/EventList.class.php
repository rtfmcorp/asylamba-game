<?php
class EventList {
	# contient des ArrayList('date', 'eventType', 'eventId')
	# format de la date : 'AAAA-MM-JJ HH:MM:SS' - SQL FORMAT
	private $events = array(); 

	public function size() {
		return count($this->events);
	}

	public function get($index = 0) {
		if (isset($this->events[$index])) {
			return $this->events[$index];
		} else {
			return NULL;
		}
	}

	public function getPastEvents($date) {
		$past = new StackList();
		foreach($this->events AS $e) {
			if ($e->get('date') <= $date) {
				$past->append($e);
			}
		}
		return $past;
	}

	public function clearPastEvents($date) {
		$size = $this->size() - 1;
		for ($i = $size; $i >= 0; $i--) {
			if ($this->events[$i]->get('date') <= $date) {
				$this->remove($i);
				$i--;
			}
		}
	}

	public function add($date, $eventType, $eventId, $eventInfo = NULL) {
		$event = new ArrayList();
		$event->add('date', $date);
		$event->add('eventType', $eventType);
		$event->add('eventId', $eventId);
		$event->add('eventInfo', $eventInfo);

		$index = 0;
		if (self::size() == 0) {
			$this->events[$index] = $event;
		} else {
			$found = FALSE;
			foreach($this->events AS $e) {
				if ($e->get('date') > $date) {
					$found = TRUE;
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
				$this->events[self::size()] = $event;
			}
		}
	}

	public function remove($index) {
		if ($index < 0) {
			$index = count($this->events) + $index;
		}
		if (isset($this->events[$index])) {
			$begin = array_slice($this->events, 0, $index);
			$end = array_slice($this->events, $index+1);
			$this->events = array_merge($begin, $end);
		} else {
			return FALSE;
		}
	}

	public function clear() {
		$this->events = array();
	}
}
?>