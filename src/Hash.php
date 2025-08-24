<?php
	namespace dackou;

	use yzh52521\hash\Hash as HashHelper;

	class Hash{
		private static $type = 'bcrypt';
		private static $rounds = 12;
		private static $memory = 1024;
		private static $time = 2;
		private static $threads = 2;

		public static function make($password,$roundmemory = 0,$time = 0,$threads = 0){
			switch(strtolower(self::$type)){
				case '':
				case 'facade':
					$pass = HashHelper::make($password);
					break;
				case 'bcrypt':
					$roundmemory = $roundmemory ?: self::$rounds;
					$pass = HashHelper::make($password,['rounds' => $roundmemory]);
					break;
				case 'argon2':
					$roundmemory = $roundmemory ?: self::$rounds;
					$time = $time ?: self::$time;
					$threads = $threads ?: self::$threads;

					$pass = HashHelper::make($password,[
						'memory' => $roundmemory,
						'time' => $time,
						'threads' => $threads
					]);
				default:
					$pass = HashHelper::make($password);
			}
			return $pass;
		}

		public static function check($password,$checkpwd){
			if(HashHelper::check($password,$checkpwd)){
				return true;
			}
			return false;
		}

		public static function needsRehash($password){
			if(HashHelper::needsRehash($password)){
				return true;
			}
			return false;
		}
	}
?>
