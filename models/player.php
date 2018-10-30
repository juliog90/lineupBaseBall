<?php
    
require_once('connection.php');
require_once('team.php');
require_once 'models/playerstats.php';
require_once('exceptions/recordnotfoundexception.php');

class Player {

    private $id;
    private $team;
    private $nickname;
    private $firstName; 
    private $lastName; 
    private $birthdate;
    private $debut;
    private $image;
    private $number;

    public function getId() { return $this->id; }

    public function getTeam() { return $this->team; }
    public function setTeam($team) { $this->team = $team; }

    public function getNickname() { return $this->nickname; }
    public function setNickname($nickname) { $this->nickname = $nickname; }

    public function getFirstName() { return $this->firstName; }
    public function setFirstName($firstName) { $this->firstName = $firstName; }

    public function getLastName() { return $this->lastName; }
    public function setLastName($lastName) { $this->lastName = $lastName; }

    public function getBirthdate() { return $this->birthdate; }
    public function setBirthdate($birthdate) { $this->birthdate = $birthdate; }

    public function getDebut() { return $this->debut; }
    public function setDebut($debut) { $this->debut = $debut; }

    public function getImage() { return $this->image; }
    public function setImage($image) { $this->image = $image; }

    public function getNumber() { return $this->number; }
    public function setNumber($number) { $this->number = $number; }

    public function getAge() { 
        $actualDate = new DateTime();
        $timeInterval = $actualDate->diff($this->birthdate);
        return $timeInterval->format('%y');
    }

    public function __construct() {
        if(func_num_args() == 0) {
            $this->id = 0;
            $this->team = new Team();
            $this->nickname = "";
            $this->firstName = "";
            $this->lastName = "";
            $this->birthdate = new DateTime();
            $this->debut = new DateTime();
            $this->image = "";
            $this->number = 0;
            }

        if(func_num_args() == 1) {
            $connection = MySqlConnection::getConnection();
            $query = 'select pl.plaId, pl.teaId, plaNickname, p.perFirstName, p.perLastName, plaBirthdate, plaDebut, plaImage, plaNumber 
                      from persons as p inner join players as pl on p.perId = pl.perId where pl.plaId = ?';
            $command = $connection->prepare($query);
            $idTemp = func_get_arg(0);
            $command->bind_param('i', $idTemp);
            $command->execute();
            $command->bind_result($id, $team, $nickname, $firstName, $lastName, $birthdate, $debut, $image, $number);
            if($command->fetch()) {
                $this->id = $id;
                $this->team = new Team($team);
                $this->nickname = $nickname;
                $this->firstName = $firstName;
                $this->lastName = $lastName;
                $this->birthdate = DateTime::createFromFormat('Y-m-d', $birthdate);
                $this->debut = DateTime::createFromFormat('Y-m-d', $debut);
                $this->image = $image;
                $this->number = $number;
            } 
                else 
                    throw new RecordNotFoundException(func_get_arg(0));

                mysqli_stmt_close($command);
                $connection->close();

        }



        if(func_num_args() == 9) {
            $this->id = func_get_arg(0);
            $this->team = new Team(func_get_arg(1));
            $this->nickname = func_get_arg(2);
            $this->firstName = func_get_arg(3);
            $this->lastName = func_get_arg(4);
            $this->birthdate = func_get_arg(5);
            $this->debut = func_get_arg(6);
            $this->image = func_get_arg(7);
            $this->number = func_get_arg(8);
        }
    }

    public static function getAll()
    {
        $players = array();
        $connection = MySqlServerConnection::getConnection();
        $query = 'getAllPlayers()';
        $command = $connecion->prepare($query);
        $command->execute();  
        $command->bind_result($id, $team, $nickname, $firstName, $lastName, $birthdate, $debut, $image, $number);
        while($command->fetch())
        {
            array_push($players, new Player($id, $team, $nickname, $firstName, $lastName, $birthdate, $debut, $image, $number));
        }

        mysqli_stmt_close($command);
        $connection->close();

        return $players;
    }

    public function remove()
    {
        $connection = MySqlConnection::getConnection(); 
        $statement = 'rmPlayer(?)';    
        $command = $connection->prepare($statement);
        $id = $this->id;
        $command->bind_param('i', $id);
        $result = $command->execute();

        mysqli_stmt_close($command);
        $connection->close();
    }

    public function edit()
    {
        $connection = MySqlConnection::getConnection(); 
        $statement = 'editCategory(?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $command = $connection->prepare($statement);
        $id = $this->id;
        $team = $this->team->id;
        $nickname = $this->nickname;
        $firstName = $this->firstName;
        $lastName = $this->lastName;
        $birthdate = $createFromFormat('Y-m-d', $this->birthdate);
        $debut = $createFromFormat('Y-m-d', $this->debut);
        $image = $this->image;
        $number = $this->number;

        $command->bind_param('i', $id);
        $result = $command->execute();

        mysqli_stmt_close($command);
        $connection->close();
    }
        
    public function add()
    {
        $connection = MySqlServerConnection::getConnection();
        $statement = 'addPlayer(?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $id = $this->id;
        $team = $this->team->id;
        $nickname = $this->nickname;
        $firstName = $this->firstName;
        $lastName = $this->lastName;
        $birthdate = $createFromFormat('Y-m-d', $this->birthdate);
        $debut = $createFromFormat('Y-m-d', $this->debut);
        $image = $this->image;
        $number = $this->number;
        $command->bind_param('issssssii',  $team, $nickname, $firstName, $lastName, $birthdate, $debut, $image, $number);
        $command = $connection->prepare($statement);
        $result = $command->execute();
        mysqli_stmt_close($command);
        $connection->close();

        return $result;
    }

    public static function getAllToJson()
    {
        $playersJson = array();
        $players = self::getAll();

        foreach ($players as $value) {
            array_push($playersJson, json_decode($value->toJson()));    
        }

            return json_encode(array(
                'players' => json_decode($playersJson)
            ));
        }

        public function toJson() {
            return json_encode(array(
            'id'=>$this->id,
            'team'=> json_decode($this->team->toJson()),
            'nickname'=>$this->nickname,
            'firstName'=>$this->firstName,
            'lastName'=>$this->lastName,
            'birthdate'=>$this->birthdate->format('Y-m-d'),
            'debut'=>$this->debut->format('Y-m-d'),
            'image'=>$this->image,
            'number'=>$this->number,
            'age' => $this->getAge()
        ));
    }
}
?>
