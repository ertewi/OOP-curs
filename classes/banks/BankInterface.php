<?php
	namespace classes\banks;

	interface BankInterface
	{
		public function getBankName();
		public function getBankMoney();
		public function getBankOwner();
		public function getBankRequests();
		public function getBankClients();
		public function creditIssuance($name, $summ);
		public function userDeposit($name, $summ);
	}