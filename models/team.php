<?php

require_once('category.php');
require_once('coach.php');
require_once('connection.php');
require_once('exceptions/recordnotfoundexception.php');

class Team {

    private $id;
    private $name; 
    private $category; 
    private $coach; 
    private $status;

    public function getId() { return $this->id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getCategory() { return $this->category; }
    public function setCategory($category) { $this->category = $category; }

    public function getCoach() { return $this->coach; }
    public function setCoach($coach) { $this->coach = $coach; }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }

    public function __construct() {
        if(func_num_args() == 0) {
            $this->id = 0;
            $this->name = "";
            $this->category = new Category();
            $this->coach = new Coach(3);
            $this->status = 1;
        }

        if(func_num_args() == 1) {
            $connection = MySqlConnection::getConnection();
            $query = 'select teaId, staId, teaName, catId, coaId from teams where teaId = ?';
            $command = $connection->prepare($query);
            $idTemp = func_get_arg(0);
            $command->bind_param('i', $idTemp);
            $command->execute();
            $command->bind_result($id, $status, $name, $category, $coach);
            if($command->fetch()) {
                $this->id = $id;
                $this->name = $name;
                $this->category = new Category($category);
                $this->coach = new Coach($coach);
                $this->status = $status;
            } 
            else 
            {
                throw new RecordNotFoundException(func_get_arg(0));
            }
            
            mysqli_stmt_close($command);
            $connection->close();
        }

        if(func_num_args() == 5) {
            $this->id = func_get_arg(0);
            $this->status = func_get_arg(1);
            $this->name = func_get_arg(2);
            $this->category = func_get_arg(3);
            $this->coach = func_get_arg(4);
        }
    }

    public function getPlayers()
    {
        $teamPlayers = array();
        $connection = MySqlConnection::getConnection();
        $query = 'select plaId, staId, teaId, plaNickname, perFirstName, perLastName, plaBirthdate,plaDebut, plaImage, plaNumber from players inner join persons on players.perId = persons.perId where teaId = ?';
        $command = $connection->prepare($query);
        $teamId = $this->id;
        $command->bind_param('i', $teamId);
        $command->execute();
        $command->bind_result($id, $status, $team, $nickname, $firstName, $lastName, $birthdate, $debut, $image, $number);
        while($command->fetch())
        {
            array_push($teamPlayers, new Player($id, $status, $team, $nickname, $firstName, $lastName, $birthdate, $debut, $image, $number));
        }

        mysqli_stmt_close($command);
        $connection->close();

        return $teamPlayers;
    }

    public function getAll()
    {
        $allTeams = array();
        $connection = MySqlConnection::getConnection();
        $query = 'select teaId, staId, teaName, catId, coaId from teams';
        $command = $connection->prepare($query);
        $command->execute();
        // placeholder database fetching
        $command->bind_result($id, $status, $name, $category, $coach);
        while($command->fetch())
        {
            array_push($allTeams, new Team($id, $status, $name, new Category($category), new Coach($coach)));
        }

        mysqli_stmt_close($command);
        $connection->close();

        return $allTeams;
    }

    public static function getAllToJson()
    {
        $teamsJson = array();
        $teams = self::getAll();

        foreach ($teams as $value) {
            array_push($teamsJson, json_decode($value->toJson()));    
        }

        return json_encode(array(
            'teams' => $teamsJson
        ));

	return $teamsJson;
    }

    public function getPlayersToJson()
    {
        $playersJson = array();

        foreach(self::getPlayers() as $player)
        {
            array_push($playersJson, json_decode($player->toJson()));
        }

        return json_encode($playersJson);
    }

    /* public static function getFullToJson() */
    /* { */
        /* $players = array(); */
        /* $jsonTemp = $this->getPlayersToJson(); */

        /* foreach($jsonTemp as $player) */
        /* { */
            /* array_push($players, json_decode($player->toJson())); */
        /* } */

/*         return json_encode( */
            /* array( */
            /* 'id' => $this->id, */
            /* 'name' => $this->name, */
            /* /1* 'category' => json_decode($this->category->toJson()), *1/ */
            /* 'coach' => json_decode($this->coach->toJson()), */
            /* 'players' => json_decode($this->getTeamPlayersToJson()))); */
    /* /1* } *1/ */

    public function toJson() {
        return json_encode(array(
            'id'=>$this->id,
            'status' => $this->status,
            'name'=>$this->name,
            'category'=>json_decode($this->category->toJson()),
            'coach' => json_decode($this->coach->toJson())
        ));
    }

    public function remove()
    {
        // delete category 
        $connection = MySqlConnection::getConnection(); 
        $statement = 'delete from teams where teaId = ?';    
        $command = $connection->prepare($statement);
        $id = $this->id;
        $command->bind_param('i', $id);
        $result = $command->execute();

        mysqli_stmt_close($command);
        $connection->close();

        return $result;
    }

    public function edit()
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'update teams set staId = ?, teaName = ?, catId = ?, coaId = ? where teaId = ?';
        $command = $connection->prepare($statement);
        $id = $this->id;
        $status = $this->status;
        $name = $this->name;
        $categoryId = $this->category->getId();
        $coachId = $this->coach->getId();
        $command->bind_param('isiii',$status, $name, $categoryId, $coachId, $id);
        $result = $command->execute();
        mysqli_stmt_close($command);
        $connection->close();

        return $result;
    }

    public function add()
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'insert into teams(staId, teaName, catId, coaId) values(?, ?, ?, ?)';
        $command = $connection->prepare($statement);
        $status = $this->status;
        $name = $this->name;
        $categoryId = $this->category->getId();
        $coachId = $this->coach->getId();

        $command->bind_param('isii', $status, $name, $categoryId, $coachId);

        $result = $command->execute();

        mysqli_stmt_close($command);
        $connection->close();

        return $result;
    }
}
?>
