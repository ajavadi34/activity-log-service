<?php
/* PHP WEB SERVICE */
header("Content-Type: application/json");
require_once("../Data/TaskData.php");

//Helps captures script execution time in seconds
$start_time = microtime(true);

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $table = new Table();

    if(!empty($_GET['Id']))
    {
        //return single task
        $taskData = new TaskData();
        $table->rows = $taskData->getTask($_GET['Id']);
    }
    else if (!empty($_GET['Type']))
    {
        //return specific task type
        $taskData = new TaskData();
        $table->rows = $taskData->getTaskType($_GET['Type']); //passes typeid to get specific type
    }
    else
    {
        //return all tasks
        $taskData = new TaskData();
        $table->rows = $taskData->getAllTasks();
    }

    $table->exec_time = round(microtime(true) - $start_time, 4);

    echo json_encode($table);
}
else if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    //Gets json post and converts to array
    $data = json_decode(file_get_contents('php://input'), true);

    //Array of required post fields
    $required = array('Id', 'Title', 'Description', 'Date');

    if ($result = Table::isArgsValid($required, $data))
    {
        echo json_encode("Invalid Put"); //error message
        exit(); //stops script
    }
    else
    {
        $task = new Task();
        $task->Id = $data['Id'];
        $task->Title = trim($data['Title']);
        $task->Description = trim($data['Description']);
        $task->Date = date('Y/m/d', strtotime($data['Date']));

        $taskData = new TaskData();
        $taskData->updateTask($task);
        echo json_encode("Update Successful");
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    //Gets json post and converts to array
    $data = json_decode(file_get_contents('php://input'), true);

    //Array of required post fields
    $required = array('TypeId', 'Title', 'Description', 'Date');

    if (Table::isArgsValid($required, $data))
    {
        echo json_encode("Invalid Post"); //error message
        exit(); //stops script
    }
    else
    {
        $task = new Task();
        $task->TypeId = $data['TypeId'];
        $task->Title = trim($data['Title']);
        $task->Description = trim($data['Description']);
        $task->Date = date('Y/m/d', strtotime($data['Date']));

        $taskData = new TaskData();
        $taskData->insertTask($task); 
        echo json_encode("Insert Successful");
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
    //Gets json post and converts to array
    $data = json_decode(file_get_contents('php://input'), true);

    //Array of required post fields
    $required = array('Id');

    if ($result = Table::isArgsValid($required, $data))
    {
        echo json_encode("Invalid Delete"); //error message
        exit(); //stops script
    }
    else
    {
        $taskData = new TaskData();
        $taskData->deleteTask($data['Id']);
        echo json_encode("Delete Successful");
    }
}

class Table {
    public $types; //types of logs
    public $headers; //grid column names
    public $rows; //array of tasks
    public $exec_time;

    public function __construct() {
        $task = new TaskData();
        $this->types = $task->getAllTaskTypes();
        $this->headers = [" ", "Type", "Title", "Description", "Date", ""];
    }

    public static function isArgsValid($required, $data) {
        //Checks to confirm that all required fields were received by web service
        $error = false;
        foreach ($required as $field) {
            if (empty($data[$field])){
                $error = true;
            }
        }
        return $error;
    }
}
?>