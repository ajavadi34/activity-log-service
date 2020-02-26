<?php
/* PHP WEB SERVICE */
header("Content-Type: application/json");
require_once("../Data/TaskData.php");

//Helps captures script execution time in seconds
$start_time = microtime(true);

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $taskData = new TaskData();
    $taskTypes = $taskData->getAllTaskTypes();
    echo json_encode($taskTypes);
}
else if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    //Gets json post and converts to array
    $data = json_decode(file_get_contents('php://input'), true);

    //Array of required post fields
    $required = array('TypeId', 'Name');

    if ($result = Table::isArgsValid($required, $data))
    {
        echo json_encode("Invalid Put"); //error message
        exit(); //stops script
    }
    else
    {
        $task = new TaskType();
        $task->TypeId = $data['TypeId'];
        $task->Name = trim($data['Name']);

        $taskData = new TaskData();
        $taskData->updateTaskType($task);
        echo json_encode("Update Successful");
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    //Gets json post and converts to array
    $data = json_decode(file_get_contents('php://input'), true);

    //Array of required post fields
    $required = array('Name');

    if (Table::isArgsValid($required, $data))
    {
        echo json_encode("Invalid Post"); //error message
        exit(); //stops script
    }
    else
    {
        $taskName = trim($data['Name']);

        $taskData = new TaskData();
        $newType = $taskData->insertTaskType($taskName); 
        echo json_encode($newType);
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
        $taskData->deleteTaskType($data['Id']);
        echo json_encode("Delete Successful");
    }
}

class Table {
    public $types; //types of logs
    public $rows; //array of tasks
    public $exec_time;
    public $totalRows;
    public $pageSize;
    public $pageNumber;

    public function __construct() {
        $task = new TaskData();
        $this->types = $task->getAllTaskTypes();
        $this->pageSize = 50;
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