<?php
private $Id_Div;
private $Name_Div;

public function getId_Div() { return $this->Id_Div; }
public function getName_Div() { return $this->Name_Div: }
public function setName_Div($Name_Div) { $this->Name_Div = $Name_Div; }

public function __construct() {
if(func_num_args() == 0 ) {
$this->Id_Div = 0;
$this->Name_Div = "";
}

if(func_num_args() == 1) {
$connection = MySqlConnection::getConnection();
$query = 'select Id_Div, Name_Div from Division where Id_Div = ?';
$command = $connection->prepare($query);
$command-> bind_param('s', func_get_arg(0));
$command->execute();
$command->bind_result($Id_Div, $Name_Div);
if($command->fetch()) {
$this->Id_Div = $Id_Div;
$this->Name_Div = $Name_Div;
} 
else 
throw new RecordNotFoundException(func_get_arg(0));
}

if(func_num_args() == 2) {
$this->Id_Div = func_get_arg(0);
$this->Name_Div = func_get_arg(1);
}
}

public function toJson() {
return json_encode (array(
'Id_Div'=>$this->Id_Div,
'Name_Div'=>$this->Name_Div
));
}

?>
