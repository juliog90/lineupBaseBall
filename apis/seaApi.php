<?php 
require_once('../models/season.php');
require_once('../models/exceptions/recordnotfoundexception.php');

// get
if($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if(isset($_GET['idSeason']))
    {
        try
        {
            $c = new Season($_GET['idSeason']);

            echo json_encode(array(
                'status' => 0,
                'season' => json_decode($c->toJson())
            ));
        }       
        catch(RecordNotFoundException $ex)
        {
            echo json_encode(array(
                'status' => 2,
                'errorMessage' => 'Invalid season id',
                'details' => $ex->getMessage()
            ));
        }

    }
    else
    {
        echo json_encode(array(
            'status' => 0,
            'seasons' => json_decode(Season::getAllToJson())
        ));
    }
}

// post
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $parametersOk = false;

   if(isset($_POST['nameSeason']))
   {
       $parametersOk = true;

       $c = new Season();

       $c->setName($_POST['nameSeason']);

       if($c->add())
       {       
           echo json_encode(array(
               'status' => 0,
               'message' => 'Season added successfully'
           ));
       }       
       else
       {   
           echo json_encode(array(
               'status' => 2,
               'errorMessage' => 'Could not add season'
           ));
       }   
   }

   if(!$parametersOk)
   {
       echo json_encode(array(
           'status' => 1,
           'errorMessage' => 'Missing parameters'
       ));
   }

}   

if($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $parametersOk = false;

    parse_str(file_get_contents("php://input"), $jsonData);
    $post_vars = json_decode($jsonData['data'], true);

    if(isset($post_vars['idSeason']) && isset($post_vars['nameSeason'])) 
    {
        $parametersOk = true;

	$right = true;

        try
        {
	    $c = new Season($post_vars['idSeason']);
	    $c->setName($post_vars['nameSeason']);
        }       
        catch(RecordNotFoundException $ex)
        {
	    $right = false;
            echo json_encode(array(
                'status' => 2,
                'errorMessage' => 'Invalid season id',
                'details' => $ex->getMessage()
            ));
        }


        if($right)
        {
            
	    if($c->edit())
	    {       
		echo json_encode(array(
		    'status' => 0,
		    'message' => 'Season updated successfully'
		));
	    }       
	    else
	    {   
		echo json_encode(array(
		    'status' => 2,
		    'errorMessage' => 'Could not update season'
		));
	    }   
        }
    }

   if(!$parametersOk)
   {
       echo json_encode(array(
           'status' => 1,
           'errorMessage' => 'Missing parameters'
       ));
   }
}

if($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
    parse_str(file_get_contents("php://input"), $jsonData);
    $post_vars = json_decode($jsonData['data'], true);

    $parametersOk = false;

    if(isset($post_vars['idSeason']))
    {
        $parametersOk = true;
	$right = true;

	try
	{
	    $s = new Season($post_vars['idSeason']);
	}	
	catch(RecordNotFoundException $ex)
	{
	    $right = false;
            echo json_encode(array(
                'status' => 2,
                'errorMessage' => 'Invalid season id',
                'details' => $ex->getMessage()
            ));
	}

	if($right)
	{		
	    try
	    {       
                if($s->delete())
                {
                    echo json_encode(array(
                        'status' => 0,
                        'message' => 'Season deleted successfully'
                    ));
                }
                else
                {
                    echo json_encode(array(
                        'status' => 2,
                        'errorMessage' => 'Could not delete season'
                    ));

                }
	    }       
	    catch(mysqli_sql_exception $ex)
	    {   
                $error = $ex->getCode();
                if($error == 1451)
                {
                    echo json_encode(array(
                        'status' => 999,
                        'errorMessage' => 'Delete Season Child Tables',
                    ));
                }
                else
                {
                    echo json_encode(array(
                        'status' => 3,
                        'errorMessage' => $ex->getMessage(),
                    ));
                }
	    }   
	}
    }
    
    if(!$parametersOk)
    {
        echo json_encode(array(
            'status' => 1,
            'errorMessage' => 'Missing parameters'
        ));
    }
}
?>
