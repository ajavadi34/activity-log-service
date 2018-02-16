<?php

require_once("../db_connect.php");
require_once("../Interface/iTask.php");
require_once("../Model/Task.php");
require_once("../Model/TaskType.php");

class TaskData implements iTask {
    private $db_conn;
    public $tasks;
    public $taskTypes;

    public function __construct() {
        //Open connection to database
        $this->db_conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $this->tasks = array();
        $this->taskTypes = array();
    }

    public function __destruct() {
        //Close connection to database
        mysqli_close($this->db_conn);
    }

    public function getAllTaskTypes(){

        $query = "CALL sp_TaskTypes_Get";
        $this->mapDataToTaskType($query);
        return $this->taskTypes;

    }

    public function getTask($taskId) {

        $query = "CALL sp_Task_Get(" . $taskId . ")";
        $this->mapDataToTasks($query);
        return $this->tasks;
        
    }

    public function getTaskType($taskTypeId) {

        $query = "CALL sp_TaskType_Get(" . $taskTypeId . ")";
        $this->mapDataToTasks($query);
        return $this->tasks;

    }

    public function getAllTasks() {

        $query = "CALL sp_TaskAll_Get";
        $this->mapDataToTasks($query);
        return $this->tasks;

    }

    public function insertTask($task){
        $task = $this->escapeTaskParams($task);

        $query = "CALL sp_Task_Insert('" . $task->Title . "','" . $task->Description . "','" . $task->Date . "','" . $task->TypeId. "')";
        
        if (!$result = mysqli_query($this->db_conn, $query))
        {
            echo json_encode("Error: " . mysqli_error($this->db_conn));
        }
        else
        {
            //Successful
        }
    }

    public function updateTask($task){
        $task = $this->escapeTaskParams($task);
        
        $query = "CALL sp_Task_Update(" . $task->Id . ",'" . $task->Title . "','" . $task->Description . "','" . $task->Date . "')";

        if (!$result = mysqli_query($this->db_conn, $query))
        {
            echo json_encode("Error: " . mysqli_error($this->db_conn));
        }
        else
        {
            //Successful
        }
    }

    public function deleteTask($taskId){
        $query = "CALL sp_Task_Delete(" . $taskId . ")";

        if (!$result = mysqli_query($this->db_conn, $query))
        {
            echo json_encode("Error: " . mysqli_error($this->db_conn));
        }
        else
        {
            //Successful
        }
    }

    //Maps data from mysql to Task object
    private function mapDataToTasks($query) {
        $response = mysqli_query($this->db_conn, $query);

        if (!$response){
            //Error
            return mysqli_error($this->db_conn);
        }
        else
        {
            //Return data
            $count = 0;

            while ($obj = mysqli_fetch_object($response, "Task"))
            {
                //Add each task object to $tasks array
                array_push($this->tasks, $obj);

                //Assign row number for grid
                $this->tasks[$count]->RowCount = $count + 1;
                $count++;
            }
            // Free result set
            mysqli_free_result($response);
        }
    }

    //Maps data from mysql to TaskType object
    private function mapDataToTaskType($query){
        $response = mysqli_query($this->db_conn, $query);

        if (!$response){
            //Error
            return mysqli_error($this->db_conn);
        }
        else
        {
            //Return data
            while ($obj = mysqli_fetch_object($response, "TaskType"))
            {
                //Add each task object to $tasks array
                array_push($this->taskTypes, $obj);
            }
            // Free result set
            mysqli_free_result($response);
        }
    }

    //Escapes task parameters before sending to mysql for security
    private function escapeTaskParams($task) {

        $task->Id = mysqli_real_escape_string($this->db_conn, $task->Id);
        $task->Title = mysqli_real_escape_string($this->db_conn, $task->Title);
        $task->Description = mysqli_real_escape_string($this->db_conn, $task->Description);
        $task->Date = mysqli_real_escape_string($this->db_conn, $task->Date);

        return $task;
    }
}

?>