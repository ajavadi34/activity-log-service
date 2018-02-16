<?php

    interface iTask {

        //Create
        public function insertTask($task);

        //Read
        public function getTask($taskId);
        public function getTaskType($taskType);
        public function getAllTasks();
        public function getAllTaskTypes();

        //Update
        public function updateTask($task);

        //Delete
        public function deleteTask($taskId);

    }
    
?>