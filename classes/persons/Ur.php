<?php
	namespace classes\persons;

	class Ur extends Person
	{
		const NAME = "Ur";
		private $banks = array();

		public function getUserBanks()
		{
			return $this->banks;
		}

		public function addBank($name) {
			$this->banks[$name] = $name;
		}

		public function delBank($name) {
			unset($this->banks[$name]);
		}
	}