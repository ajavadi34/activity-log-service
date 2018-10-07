<?php
/* PHP WEB SERVICE */
header("Content-Type: application/json");
require_once("../Data/TaskData.php");

//Helps captures script execution time in seconds
$start_time = microtime(true);

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    $table = new Table();
    $taskData = new TaskData();

    if(!empty($_GET['Id']))
    {
        //return single task
        $table->rows = $taskData->getTask($_GET['Id']);
    }
    else if (!empty($_GET['Type']))
    {
        //return specific task type
        $table->rows = $taskData->getTaskType($_GET['Type']); //passes typeid to get specific type
    }
    else
    {
        //return all tasks
        $table->rows = $taskData->getAllTasks();
    }

    //set results details
    $table->totalRows = count($table->rows);
    //filter to page
    $table->pageNumber = !empty($_GET['PageNumber']) ? (int)$_GET['PageNumber'] : 1;
    $table->rows = array_slice($table->rows, ($table->pageSize * ($table->pageNumber - 1)), $table->pageSize);
    $table->exec_time = round(microtime(true) - $start_time, 4);

    echo json_encode($table);
}
else if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    //Gets json post and converts to array
    $data = json_decode(file_get_contents('php://input'), true);

    //Array of required post fields
    $required = array('Id', 'Title', 'Date');

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
        $task->Link = trim($data['Link']);
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
    $required = array('TypeId', 'Title', 'Date');

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
        $task->Link = trim($data['Link']);
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
    public $totalRows;
    public $pageSize;
    public $pageNumber;

    public function __construct() {
        $task = new TaskData();
        $this->types = $task->getAllTaskTypes();
        $this->headers = [" ", "Type", "Title", "Description", "Link", "Date", ""];
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