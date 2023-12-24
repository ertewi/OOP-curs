<?php
	namespace classes\banks;

	class Bank implements BankInterface
	{
		const NAME = "Bank";
		private $clients = array();
		private $requests = array();

		public function __construct(private $name, private $owner, private $country, private $money)
		{}

		public function getBankName()
		{
			return $this->name;
		}

		public function getBankMoney()
		{
			return $this->money;
		}

		public function getBankOwner()
		{
			return $this->owner;
		}

		public function getBankCountry()
		{
			return $this->country;
		}

		public function getBankRequests()
		{
			return $this->requests;
		}

		public function getBankClients()
		{
			return $this->clients;
		}

		public function creditApprove($name)
		{
			foreach($this->clients as $client => $summ) {
				if($name == $client) {
					$clients[$name] += $this->requests[$name];
					$this->money -= $this->requests[$name];
					$_SESSION['users']['Fiz'][$name]->giveMoney($this->requests[$name]);
					unset($this->requests[$name]);
					return;
				}
			}
			$this->money -= $this->requests[$name];
			$this->clients[$name] = $this->requests[$name];
			$_SESSION['users']['Fiz'][$name]->addBank($this->name);
			$_SESSION['users']['Fiz'][$name]->giveMoney($this->requests[$name]);
			unset($this->requests[$name]);
		}

		public function creditFail($name)
		{
			unset($this->requests[$name]);
		}

		public function creditIssuance($name, $summ)
		{
			$this->requests[$name] = $summ;
		}

		public function userDeposit($name, $summ)
		{
			$flag = 0;
			$this->clients[$name] -= $summ;
			$this->money += $summ;
			if($this->clients[$name] <= 0) {
				unset($this->clients[$name]);
				$flag = 1;
			}
			return $flag;
		}

		public function userDelete($name) {
			unset($this->requests[$name]);
			unset($this->clients[$name]);
		}
	}