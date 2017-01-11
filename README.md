## About MessageQueue

MessageQueue is a super light-weight and simple message queue PHP class which requires Redis. Each queue is defined and handled by creating a new PHP class extending MessageQueue, see ExampleQueue.

### Usage

1) Start the mq_worker.php and mq_worker_delayed.php scripts in a screen:

```
$ php mq_worker.php
2017-01-11 21:11:46 Waiting for jobs...

$ php mq_worker_delayed.php
2017-01-11 21:13:32 Waiting for delayed jobs...
```

2) Create a new PHP class based on ExampleQueue (or use it as it is..)

3) Create a new job and throw it into the ExampleQueue:

```PHP
$job = [
    'id' => 'uuid',
    'delay' => time() + 30, // 30 seconds delay before job is handled (optional)
    'data' => [
        'id' => 'id',
        'name' => 'Tobias',
        'company' => 'Freespee',
    ],
];

$qExample = new \Freespee\MessageQueue\ExampleQueue;
if ($qExample->addToQueue($job) === false) {
    // Something went wrong
}
```

4) Watch the job being handled by mq_worker or mq_worker_delay depending on if it should be delayed or not.

### Scaling up

Multiple mq_worker-processes can run at the same time for scalability.

