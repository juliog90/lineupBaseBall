<?php

require_once('models/League');
require_once('exception/recordnotfoundexception.php');

class League
{

    private $id;
    private $name;
    private $season;

    public function getId() { return $this->id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getSeason() { return $this->season; }
    public function setSeason($season) { $this->season = $season; }


    public function __construct() {
        if(func_num_args() == 0 ) {
        $this->id = 0;
        $this->name = "";
        $this->season = "";
        }

        if(func_num_args() == 1) {
            $connection = MySqlConnection::getConnection();
            $query = 'select id_L, name, id_S from league where id_L = ?';
            $command = $connection->prepare($query);
            $id = func_get_arg(0);
            $command->bind_param('s', $id);
            $command->execute();
            $command->bind_result($id, $name, $season);

        if($command->fetch()) {
            $this->id = $id;
            $this->name = $name;
            $this->season = new Season($season);
        } 
        else 
            throw new RecordNotFoundException(func_get_arg(0));
        }

        if(func_num_args() == 3) {
            $this->id = func_get_arg(0);
            $this->name = func_get_arg(1);
            $this->season = func_get_arg(2);
        }
    }

    public function toJson() {
        return json_encode (array(
        'id'=>$this->id,
        'name'=>$this->name,
        'season' => json_decode($this->season->toJson())
    ));
    }

}
?>
