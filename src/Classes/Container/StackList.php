<?php

namespace App\Classes\Container;

class StackList {
		/** @var array **/
		private $elements = array();

		/**
		 * @return int
		 */
		public function size() {
				return count($this->elements);
		}

		/**
		 * @param int $index
		 * @return mixed
		 */
		public function get($index = 0) {
				if (isset($this->elements[$index])) {
					return $this->elements[$index];
				}
				return NULL;
		}

		/**
		 * @param int $index
		 * @return boolean
		 */
		public function exist($index) {
				return (isset($this->elements[$index]));
		}

		/**
		 * @param string $key
		 * @param mixed $value
		 */
		public function add($key, $value) {
				$this->elements[$key] = $value;
		}

		/**
		 * @param string $key
		 * @param mixed $value
		 */
		public function increase($key, $value) {
				$this->elements[$key] =
						(isset($this->elements[$key]))
						? $this->elements[$key] + $value
						: $value
				;
		}

		/**
		 * @param string $key
		 * @param mixed $value
		 */
		public function insert($key, $value) {
				if (count($this->elements) < $key) {
						$this->elements[$key] = $value;
				} else {
						// décalage des tous les index qui suivent
						$begin = array_slice($this->elements, 0, $key);
						$begin[] = $value;
						$end = array_slice($this->elements, $key);
						$this->elements = array_merge($begin, $end);
				}
		}

		/**
		 * @param mixed $element
		 */
		public function append($value) {
				$this->elements[] = $value;
		}

		/**
		 * @param mixed $value
		 */
		public function prepend($value) {
				array_unshift($this->elements, $value);
		}

		/**
		 * @param int $index
		 */
		public function remove($index) {
				if ($index < 0) {
						$index = count($this->elements) + $index;
				}
				if (!isset($this->elements[$index])) {
						return false;
				}
				$begin = array_slice($this->elements, 0, $index);
				$end = array_slice($this->elements, $index+1);
				$this->elements = array_merge($begin, $end);
		}

		public function clear() {
				$this->elements = array();
		}
}
