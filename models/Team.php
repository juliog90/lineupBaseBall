<?php

require_once('connection.php');
require_once('exceptions/recordnotfoundexception.php');

    class Team {

        private $id;
        private $name; 
        private $league; 

        public function getId() { return $this->id; }
        public function getName() { return $this->name; }
        public function setName($name) { $this->name = $name; }

        public function getLeague() { return $this->league; }
        public function setLeague($league) { $this->league = $league; }

        public function __construct() {
            if(func_num_args() == 0) {
                $this->id = 0;
                $this->name = "";
                $this->league = "";
            }

            if(func_num_args() == 1) {
                $connection = MySqlConnection::getConnection();
                $query = 'select id_T, name, id_L from team where league = ?';
                $command = $connection->prepare($query);
                $id = func_get_arg(0);
                $command->bind_param('s', $id);
                $command->execute();
                $command->bind_result($idTeam, $name, $league);
                if($command->fetch()) {
                    $this->id = $idTeam;
                    $this->name = $name;
                    $this->league = $league;
                } 
                else 
                    throw new RecordNotFoundException(func_get_arg(0));
            }

            if(func_num_args() == 6) {
                $this->id = func_get_arg(0);
                $this->name = func_get_arg(1);
                $this->league = func_get_arg(2);
            }
        }

        public function toJson() {
            return json_encode(array(
                'id'=>$this->id,
                'name'=>$this->name,
                'league'=>$this->league,
            ));
        }
    }


?>
