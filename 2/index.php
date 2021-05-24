<?php
    class sampleA {
        private $name = 'Test Case 2';

        public function getMessage() {
            return $this->name;
        }

        public function processMessage($message) {
            return 'message: ' . $message .  ' ' . $this->name;
        }
    }

    function doBackgroundQueue ($job) {
        $objA = new sampleA();
        $workload = $job->workload();

        $message = $objA->getMessage();

        if ($message != '') {
            $objA->processMessage($workload);

        }

        return true;
    }

$client= new GearmanClient();

$client->addServer();

 $client->doBackground("updateMessage", "Some message for test case 2");

$worker = new GearmanWorker();
$worker->addServer();

$worker->addFunction('updateMessage', 'doBackgroundQueue');


while(1) {
    $worker->work();
    if ($worker->returnCode() != GEARMAN_SUCCESS) {
        break;
    }
}
?>