<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-type" content="text/css; charset=utf-8">
<title></title>
</head>
<body>

<?php

class Human {
	private int $id;
	private string $name;
	private string $surname;
	private string $birthday;
	private int $gender;
	private string $city;
	
			
	public function __construct(int $id,string $name,string $surname,string $birthday,int $gender,string $city){
		if($id!=0){
			getHuman($id);	
		}
		else{	
			createHuman($name, $surname, $birthday, $gender, $city);
		}	
	}
	private function getHuman(int $id){
		require "dbjh.php";	
		$humanPoc = $mysqli->prepare("SELECT * FROM humans WHERE id =?");//запрос на наличие такого номера договора в базе
		if(!$humanPoc){
			throw new Exception('Ошибка создания запроса получения данных!');
		}
		try{	
			$humanPoc->bind_param("i",$id );
			if(!$humanPoc->execute()){
				throw new Exception('Ошибка выполнения запроса на поиск человека!');
			}
			$humans = $humanPoc->get_result();
				 
			if($row = $humans->fetch_assoc()) {
				$this->id=$id;
				$this->name=$row['name'];
				$this->surname=$row['surname'];
				$this->birthday=$row['birthday'];
				$this->gender=$row['gender'];
				$this->city=$row['city'];
			}
			else{				
				throw new Exception('Человек с данным id не найден!');
			}
		}
		finally{
			$humanPoc->close();
		}	
	}
	private function createHuman(string $name,string $surname,string $birthday,int $gender,string $city){
			$this->validateGender($gender);
			$this->validateName($name);
			$this->validateSurName($surname);
			$this->validateBirthDay($birthday);
			require "dbjh.php";	
			$query = "INSERT INTO humans (id, name , surname, birthday,	gender, city) VALUES (?, ?, ?, ?, ?, ?)";
			$procsave=$mysqli->query($query);
			if(!$procsave){				
				throw new Exception('Ошибка создания запроса сохранения данных!');
			}
			try{
				$id = mysqli_insert_id($mysqli);
				$procsave->bind_param("isssis", $id, $this->name, $this->surname, $this->birthday,
											$this->gender, $this->city);
				
				if($procsave->execute()){
					$this->id=$id;
					$this->name=$name;
					$this->surname=$surname;
					$this->birthday=$birthday;
					$this->gender=$gender;
					$this->city=$city;
					return;
				}
				else{				
					throw new Exception('Ошибка выполнения запроса сохранения данных!');
				}
			}
			finally{
				$procsave->close();				
			}
	}
	private function validateGender(int $gender){		
		if($gender!==0 && $gender!==1){
			throw new Exception('gender может быть только 0 или 1');
		}
	}
	private function validateName(string $name){
		$this->validateLetters($name, 'name');
	}	
	private function validateSurName(string $name){		
		$this->validateLetters($name, 'SurName');
	}
	private function validateBirthDay(string $birthday){		
		if(!strtotime($birthday)){
			throw new Exception('birthday может быть только датой');
		}
	}	
	private function validateLetters(string $word, string $parameterName){		
		if(!ctype_alpha($word)){
			throw new Exception($parameterName . ' может содержать только буквы');
		}
	}
	
	public function __setName(string $Name){
		$this->validateName($Name);
		$this->name= $Name;
	}
	public function __setSurname(string $Surname){
		$this->validateSurName($Surname);
		$this->surname= $Surname;
	}	
	public function __setGender(int $gender){
		$this->validateGender($gender);
		$this->gender=$gender;
	}
	public function __setBirthday(string $birthday){
		validateBirthDay($birthday);
		$this->birthday=$birthday;
	}
	public function __setCity(string $City){
		$this->city= $City;
	}
		
	public function __getId(){return $this->id;}
	public function __getName(){return $this->name;}
	public function __getSurname(){return $this->surname;}
	public function __getBirthday(){return $this->birthday;}
	public function __getGender(){return $this->gender;}
	public function __getCity(){return $this->city;}
	
	public function save(){
		require "dbjh.php";	
		$procUpdate = $mysqli->prepare("UPDATE humans SET  name=?, surname=?,
										birthday=?, gender=?, city=?
										WHERE id=?");//запрос на обновление полей в бд
		if(!$procUpdate){			
			throw new Exception('Ошибка создания запроса на обновление данных!');
		}
		try{
			$procUpdate->bind_param("sssisi", $this->name,$this->surname, $this->birthday,
										$this->gender, $this->city,$this->id);
				
			if($procUpdate->execute()){		
				return;
			}
			else{				
				throw new Exception('Ошибка выполнения запроса обновления данных!');
			}
		}
		finally {
			$procUpdate->close();		
		}
	}
	
	public function deleteHuman(){
		require "dbjh.php";	
		$procUpdate = $mysqli->prepare("DELETE from humans WHERE id=?");//запрос на удаление полей в бд
		if(!$procUpdate){			
			throw new Exception('Ошибка создания запроса удаления данных!');
		}
		try{
			$procUpdate->bind_param("i", $this->id);
				
			if($procUpdate->execute()){		
				return;
			}
			else{				
				throw new Exception('Ошибка выполнения запроса удаления данных!');
			}
		}
		finally {
			$procUpdate->close();		
		}
	}
	
	public function formatHuman(){
		$newobj = new stdClass();
		$newobj->id = $this->id;
		$newobj->name = $this->name;
		$newobj->surname=$this->surname;
		$newobj->city=$this->city;
		
		$newobj->birthday=$this->birthday;
		$newobj->gender=$this->gender;
	
		$newobj->age=Human::getAge($this);
		$newobj->genderString=Human::getGenderString($this);
		return $newobj;
	}
	
	public static function getAge(Human $human){
		$dateOfBirth  =  $human->__getBirthday(); 
		$today  =  date( "Ymd" ); 
		$diff  =  date_diff ( date_create ( $dateOfBirth ),  date_create ( $today )); 
		return $diff->format('%y');
	}	
	public static function getGenderString(Human $human){
		if ($human->gender===0){
			return 'муж';
		} else{
			return 'жен';
		}
	}
	
}

?>

</body>
</html>