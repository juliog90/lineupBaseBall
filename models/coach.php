<?php
    require_once("person.php");
    require_once("connection.php");
    class Coach{
        #attributes
        private $id;
        private $person;

        #getters&setters
        public function getId(){return $this->id;}
        public function setId($id){$this->id = $id;}
        public function getPerson(){return $this->person;}
        public function setPerson($person){$this->person = $person;}
        #constructor
        public function __construct(){
            if(func_num_args() == 0){
                $this->id = "";
                $this->person = new Person();
            }
            if(func_num_args() == 1){
                #get connection
                $connection = MySqlConnection::getConnection();
                #query
                $query = 'select * from coaches c where coaId = ?';
                #prepare statement
                $command = $connection->prepare($query);
                #params
                $command->bind_param('i',func_get_arg(0));
                #execute
                $command->execute();
                #bind params
                $command->bind_result($id, $person);
                //no funciona todavía!!!------------------------
                if($command->fetch()){
                    $this->id = $id;
                    $this->person = new Person($person);
                
                    }
                
                }
            
            if(func_num_args() == 2){
                $this->id = func_get_arg(0);
                $this->person = func_get_arg(1);
            }
        }
        #methods
        public function add($personId){
            $connection=MySqlConnection::getConnection();
			#query
			$statement='insert into coaches (perId) VALUES (?)';
			#prepare statement
			$command=$connection->prepare($statement);
			#parameter
            $command->bind_param('i', $personId
            );
			#execute
			$result=$command->execute();
			#close command
			mysqli_stmt_close($command);
			$connection->close();
			return $result;
        }
        //No puse edit porque sólo es person y coa y no debería de poder cambiar ninguno de esos.
        public function delete(){
            $connection=MySqlConnection::getConnection();
			#query
			$statement='delete from coaches where coaId = ?';
			#prepare statement
			$command=$connection->prepare($statement);
			#parameter
            $command->bind_param('i', $this->id
            );
			#execute
			$result=$command->execute();
			#close command
			mysqli_stmt_close($command);
			$connection->close();
			return $result;
        }
        public static function getAll(){
                $list = array();
                #get connection
                $connection = MySqlConnection::getConnection();
                #query
                $query = 'select c.coaId, c.perId, p.perFirstName, p.perLastName from coaches c, persons p where p.perId = c.perId';
                #prepare statement
                $command = $connection->prepare($query);
                #execute
                $command->execute();
                #bind results
                $command->bind_result($id, $personId, $firstName, $lastName);
                #fetch data
                while ($command->fetch()){
                    #add contact to list
                    array_push($list, new Coach($id, new Person($personId)));
                }
                return $list;
        }
        public function toJson(){
            return json_encode(array(
                'id' => $this->id,
                'person'=>json_decode($this->person->toJson())
            ));
        }
        public static function getAllToJson(){
            $jsonArray=array();
                foreach(self::getAll() as $item){
                    array_push($jsonArray,json_decode($item->toJson()));
                }
                return json_encode($jsonArray);
        }
    }
?>
