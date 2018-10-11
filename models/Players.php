<?php
    class Players {
        private $id;
        private $name; 
        private $launching;
        private $age;
        private $heigth;
        private $weigth;

        public function getId() { return $this->id; }

        public function getName() { return $this->name; }
        public function setName($name) { $this->name = $name; }

        public function getPosition() { return $this->position; }
        public function setPosition($position) { $this->position = $position; }

        public function getAge() { return $this->age; }
        public function setAge($age) { $this->age = $age; }

        public function getHeigth() { return $this->heigth; }
        public function setHeigth($heigth) { $this->heigth = $heigth; }

        public function getWeigth() { return $this->weigth; }
        public function setWeigth($weigth) { $this->weigth = $weigth; }

        public function __construct() {
            if(func_num_args() == 0) {
                $this->id = 0;
                $this->name = "";
                $this->launching = "";
                $this->age = 0;
                $this->heigth = "";
                $this->weigth = "";
            }

            if(func_num_args() == 1) {
                $connection = MySqlConnection::getConnection();
                $query = 'select Id_Div, Name_Div from Division where Id_Div = ?';
                $command = $connection->prepare($query);
                $command-> bind_param('s', func_get_arg(0));
                $command->execute();
                $command->bind_result($id, $name, $launching, $age, $heigth, $weigth);
                if($command->fetch()) {
                    $this->id = $id;
                    $this->name = $name;
                    $this->launching = $launching;
                    $this->age = $age;
                    $this->heigth = $heigth;
                    $this->weigth = $weigth;
                } 
                else 
                    throw new RecordNotFoundException(func_get_arg(0));
            }

            if(func_num_args() == 6) {
                $this->id = func_get_arg(0);
                $this->name = func_get_arg(1);
                $this->launching = func_get_arg(2);
                $this->age = func_get_arg(3);
                $this->heigth = func_get_arg(4);
                $this->weigth = func_get_arg(5);
            }
        }

        public function toJson() {
            return json_encode(array(
                'id'=>$this->id,
                'name'=>$this->name,
                'launching'=>$this->launching,
                'age'=>$this->age,
                'heigth'=>$this->heigth,
                'weigth'=>$this->weigth
            ));
        }
    }


?>