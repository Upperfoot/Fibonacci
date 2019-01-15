<?php

return [
    'worker_sleep_time_ns'     => getenv('FIBONACCI_WORKER_SLEEP_NS'),
    'max_fibonacci_elements'   => getenv('FIBONACCI_ELEMENTS_MAX'),
];