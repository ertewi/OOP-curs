<?php
	namespace classes\persons;

	abstract class Person
	{
		private $user_login;
		private $user_password;

		public function __construct($login, $password)
		{
			$this->user_login = $login;
			$this->user_password = $password;
		}

		public function getUserLogin()
		{
			return $this->user_login;
		}

		public function signIn($login, $password)
		{
			if($login === $this->user_login && $password === $this->user_password) {
				return 1;
			} else return 0;
		}
	}