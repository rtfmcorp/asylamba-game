<?php

namespace App\Classes\Worker;

class ManagerSession {
		/** @var int **/
		private $id;
		/** @var string **/
		private $type;
		/** @var string **/
		private $uMode;

		/**
		 * @param int $id
		 * @param string $type
		 * @param string $uMode
		 */
		public function __construct($id, $type, $uMode) {
				$this->id = $id;
				$this->type = $type;
				$this->uMode = $uMode;
		}

		/**
		 * @return int
		 */
		public function getId() {
				return $this->id;
		}

		/**
		 * @return string
		 */
		public function getType()	{
				return $this->type;
		}

		/**
		 * @return string
		 */
		public function getUMode()	{
				return $this->uMode;
		}

		/**
		 * @return string
		 */
		public function toString() 	{
				return
						'id = ' . $this->id . ', type = ' . $this->type .
						', uMode = ' . $this->uMode . ' '
				;
		}
}
