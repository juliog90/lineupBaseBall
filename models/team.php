<?php

require_once('category.php');
require_once('coach.php');
require_once('teamstats.php');
require_once('connection.php');
require_once('exceptions/recordnotfoundexception.php');

class Team {

    private $id;
    private $name; 
    private $category; 
    private $coach; 
    private $stats; 

    public function getId() { return $this->id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getCategory() { return $this->category; }
    public function setCategory($category) { $this->category = $category; }

    public function getCoach() { return $this->coach; }
    public function setCoach($coach) { $this->coach = $coach; }

    public function getStats() { return $this->stats; }
    public function setStats($stats) { $this->stats = $stats; }

    public function __construct() {
        if(func_num_args() == 0) {
            $this->id = 0;
            $this->name = "";
            $this->category = new Category();
            $this->coach = new Coach();
            $this->stats = new Stats();
        }

        if(func_num_args() == 1) {
            $connection = MySqlConnection::getConnection();
            $query = 'getTeam(?)';
            $command = $connection->prepare($query);
            $idTemp = func_get_arg(0);
            $command->bind_param('i', $idTemp);
            $command->execute();
            $command->bind_result($id, $name, $category, $coach, $stats);
            if($command->fetch()) {
                $this->id = $id;
                $this->name = $name;
                $this->category = new Category($category);
                $this->coach = new Coach($coach);
                $this->stats = new Stats($stats);
            } 
            else 
            {
                throw new RecordNotFoundException(func_get_arg(0));
            }

            mysqli_stmt_close($command);
            $connection->close();
        }

        if(func_num_args() == 3) {
            $this->id = func_get_arg(0);
            $this->name = func_get_arg(1);
            $this->category = func_get_arg(2);
            $this->coach = func_get_arg(3);
            $this->stats = func_get_arg(4);
        }
    }

    public function getTeamPlayers()
    {
        $teamPlayers = array();
        $connection = MySqlConnection::getConnection();
        $query = 'getTeamPlayers(?)';
        $command = $connection->prepare($query);
        $teamId = $this->id;
        $command->bind_param('i', $teamId);
        $command->execute();
        $command->bind_result($id, $team, $nickname, $firstName, $lastName, $birthdate, $debut, $image, $number, $stats);
        while($command->fetch())
        {
            array_push($teamPlayers, new Player($id, $team, $nickname, $firstName, $lastName, $birthdate, $debut, $image, $number, $stats));
        }

        mysqli_stmt_close($command);
        $connection->close();
    }

    public function getAllTeams()
    {
        $allTeams = array();
        $connection = MySqlConnection::getConnection();
        $query = 'getAllTeams()';
        $command->prepare($query);
        $command->execute();
        // placeholder database fetching
        $command->bind_result($id, $name, $category, $coach, $stats);
        while($command->fetch())
        {
            array_push($allTeams, new Team($id, $name, $category, $coach, $stats));
        }

        mysqli_stmt_close($command);
        $connection->close();

        return $allTeams;
    }

    public function getTeamPlayersToJson()
    {
        $playersJson = array();

        foreach(self::getPlayers() as $player)
        {
            array_push($playersJson, $player->toJson());
        }

        return $playersJson;
    }

    public static function getFullToJson()
    {
        $players = array();

        foreach($this->getTeamPlayersToJson() as $player)
        {
            array_push($players, json_decode($player->toJson()));
        }

        return json_encode(
            array(
            'id' => $this->id,
            'name' => $this->name,
            'category' => json_decode($this->category->toJson()),
            'coach' => json_decode($this->coach->toJson()),
            'stats' => json_decode($this->stats->toJson()),
            'players' => json_decode($this->getTeamPlayersToJson())));
    }

    public function toJson() {
        return json_encode(array(
            'id'=>$this->id,
            'name'=>$this->name,
            'category'=>json_decode($this->category->toJson()),
            'coach' => json_decode($this->coach->toJson()),
            'stats' => json_decode($this->stats->toJson()),
        ));
    }

    public function remove()
    {
        $connection = MySqlConnection::getConnection();
        $statement = 'removeTeam(?)';
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
        $statement = 'editTeam(?, ?, ?, ?, ?)';
        $command = $connection->prepare($statement);
        $id = $this->id;
        $name = $this->name;
        $categoryId = $this->category->id;
        $coachId = $this->coach->id;
        $statsId = $this->stats->id;
        $command->bind_param('isiii', $id, $name, $categoryId, $coachId, $statsId);
        $result = $command->execute();
        mysqli_stmt_close($command);
        $connection->close();

        return $result;
    }
}
?>
