<?php
	namespace classes\persons;

	class Fiz extends Person
	{
		const NAME = "Fiz";
		private $money = 0;
		private $banks = array();

		public function deposit($summ, $bank, $flag)
		{
			$this->money -= $summ;
			if($flag) {
				unset($this->banks[$bank]);
			}
		}

		public function getUserMoney()
		{
			return $this->money;
		}

		public function getUserBanks()
		{
			return $this->banks;
		}

		public function addBank($name)
		{
			$this->banks[$name] = $name;
		}

		public function delBank($name)
		{
			unset($this->banks[$name]);
		}

		public function giveMoney($summ)
		{
			$this->money += $summ;
		}

		public function takeMoney($summ)
		{
			$this->money -= $summ;
		}
	}