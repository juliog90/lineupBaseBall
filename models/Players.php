<?php

require_once('connection.php');
require_once('Team.php');
require_once('exceptions/recordnotfoundexception.php');

    class Player {

        private $id;
        private $firstName; 
        private $lastName; 
        private $dateOfBirth;
        private $heigth;
        private $weigth;
        private $team;

        public function getId() { return $this->id; }
        public function getFirstName() { return $this->firstName; }
        public function setFirstName($firstName) { $this->firstName = $firstName; }

        public function getLastName() { return $this->lastName; }
        public function setLastName($lastName) { $this->lastName = $lastName; }

        public function getDateOfBirth() { return $this->dateOfBirth; }
        public function setDateOfBirth($dateOfBirth) { $this->dateOfBirth = $dateOfBirth; }

        public function getHeigth() { return $this->heigth; }
        public function setHeigth($heigth) { $this->heigth = $heigth; }

        public function getAge() { 
            $actualDate = new DateTime();
            $timeInterval = $actualDate->diff($this->dateOfBirth);
            return $timeInterval->format('%y');
        }

        public function getWeigth() { return $this->weigth; }
        public function setWeigth($weigth) { $this->weigth = $weigth; }

        public function getTeam() { return $this->team; }
        public function setTeam($team) { $this->team = $team; }

        public function __construct() {
            if(func_num_args() == 0) {
                $this->id = 0;
                $this->firstName = "";
                $this->lastName = "";
                $this->team = new Team();
                $this->dateOfBirth = new DateTime();
                $this->heigth = "";
                $this->weigth = "";
            }

            if(func_num_args() == 1) {
                $connection = MySqlConnection::getConnection();
                $query = 'select first_name, last_name, id_p, date_of_birth, Height, Weight_p, id_T from player where id_p = ?';
                $command = $connection->prepare($query);
                $id = func_get_arg(0);
                $command->bind_param('s', $id);
                $command->execute();
                $command->bind_result($firstName, $lastName, $idP, $dateOfBirth, $heigth, $weigth, $idT);
                if($command->fetch()) {
                    $this->id = $idP;
                    $this->firstName = $firstName;
                    $this->lastName = $lastName;
                    $this->team = new Team($idT);
                    $this->dateOfBirth = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
                    $this->heigth = $heigth;
                    $this->weigth = $weigth;
                } 
                else 
                    throw new RecordNotFoundException(func_get_arg(0));

                mysqli_stmt_close($command);
                $connection->close();
            }

            if(func_num_args() == 6) {
                $this->id = func_get_arg(0);
                $this->firstName = func_get_arg(1);
                $this->lastName = func_get_arg(2);
                $this->team = new Team(func_get_arg(3));
                $this->dateOfBirth = new DateTime::createFromFormat('Y-m-d', func_get_arg(4));
                $this->heigth = func_get_arg(5);
                $this->weigth = func_get_arg(6);
            }
        }

        public static function getAll()
        {
            $players = new array();
            $connection = MySqlServerConnection::getConnection();
            $query = 'getAllPlayers()';
            $command = $connecion->prepare($query);
            $command->execute();  
            $command->bind_result($id, $firstName, $lastName, $team, $dateOfBirth, $height, $weight);
            while($command->fetch())
            {
                array_push($players, new Player($id, $firstName, $lastName, $team, $dateOfBirth, $height, $weight);
            }

            mysqli_stmt_close($command);
            $connection->close();

            return $players;
        }
            
        public function add()
        {
            $connection = MySqlServerConnection::getConnection();
            $statement = 'addPlayer(?, ?, ?, ?, ?, ?)';
            $id = $this->id;
            $firstName = $this->firstName;
            $lastName = $this->lastName;
            $team = $this->team;
            $dateOfBirth = $this->dateOfBirth;
            $height = $this->height;
            $weight = $this->weight;
            $command->bind_param('ississs',
            $command = $connection->prepare($statement);
            $result = $command->execute();
            mysqli_stmt_close($command);
            $connection->close();

            return $result;
        }

        /* public function edit() */
        /* { */
        /*     $connection = MySqlServerConnection::getConnection(); */
        /*     $statement = 'editPlayer(?, ?, ?, ?, ?, ?)'; */
        /*     $ */


        public function toJson() {
            return json_encode(array(
                'id'=>$this->id,
                'firstName'=>$this->firstName,
                'lastName'=>$this->lastName,
                'team'=> json_decode($this->team->toJson()),
                'dateOfBirth'=>$this->dateOfBirth->format('Y-m-d'),
                'age' => $this->getAge(),
                'heigth'=>$this->heigth,
                'weigth'=>$this->weigth
            ));
        }
    }


?>
