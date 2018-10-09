<?php

class League
{

private $idDiv;
private $nameDiv;

public function getIdDiv() { return $this->idDiv; }
public function getNameDiv() { return $this->nameDiv; }
public function setNameDiv($nameDiv) { $this->nameDiv = $nameDiv; }

public function __construct() {
    if(func_num_args() == 0 ) {
    $this->idDiv = 0;
    $this->nameDiv = "";
    }

    if(func_num_args() == 1) {
        $connection = MySqlConnection::getConnection();
        $query = 'select idDiv, nameDiv from Division where idDiv = ?';
        $command = $connection->prepare($query);
        $command-> bind_param('s', func_get_arg(0));
        $command->execute();
        $command->bind_result($idDiv, $nameDiv);

    if($command->fetch()) {
        $this->idDiv = $idDiv;
        $this->nameDiv = $nameDiv;
    } 
    else 
        throw new RecordNotFoundException(func_get_arg(0));
    }

    if(func_num_args() == 2) {
        $this->idDiv = func_get_arg(0);
        $this->nameDiv = func_get_arg(1);
    }
}

public function toJson() {
    return json_encode (array(
    'idDiv'=>$this->idDiv,
    'nameDiv'=>$this->nameDiv
));
}

}
?>
