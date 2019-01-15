### Fibonacci sequence generator

This laravel application generate, store and provide some command to view and look up generated sequence.

Fibonacci sequence is generated using iteration, Worker run in parallel with main application and its decoupled in terms of execution.
In a real world scenario each container have their own application to run and won't share a code base, To keep it simple however i decided to keep everything in one repository.

Fibonacci numbers are persisted in a Redis list, Redis list are fast and provide a O(1) complexity to access and filter the data. That means we can always filter and sort the data in same timebase, no matter how big is the sequence.

Redis data is being persisted on the disk within `data/` directory, So the process can be resumed in case of a sudden of failure or restart.

To keep it simple and easy to set-up, high availibity and master-slave setup has been skiped, in a real scenario, worker will write to master and the application will only access read slaves.



### Requirements

1. Docker: https://docs.docker.com/install/linux/docker-ce/ubuntu/#install-docker-ce
or if you are using mac: https://docs.docker.com/docker-for-mac/install/
2. Docker Compose - https://docs.docker.com/compose/install/

### Setup

1. clone this repository
2. in terminal run `sudo docker-compose build` to build docker images, Then run `sudo docker-compose up -d` to bring up containers
3. as soon as containers are up and running and composer dependencies are installed
fibonacci generator worker `artisan fibonacci:generate` will start running in background.
4. run `sudo docker-compose exec app php artisan fibonacci:tail` to tail the fibonacci sequence as it is being generated.
5. run `sudo docker-compose exec app php artisan fibonacci:query` to look up and filter the numbers.
    - It acceps following optional parameters:
```
      --from[=FROM]     Starting index [default: "0"]
      --to[=TO]         Ending index [default: "20"]
      --sort[=SORT]     Order the output [asc/desc] [default: "asc"]
```
        
6. run the tests `sudo docker-compose app exec composer test` 
7. check the code coverage by running `sudo docker-compose app exec composer coverage` in `coverage\` directory.


### Configuration
In  `.env` file there are two variable that helps you tweak the execution of program:

`FIBONACCI_ELEMENTS_MAX=300`: Maximum elements F(n) to generate
`FIBONACCI_WORKER_SLEEP_NS=100000` Time in nano second to sleep between each round

This application is able to generate Fibonacci up to 4 billion sequences, given resources and time,
However it might be a good idea to limit it using `FIBONACCI_ELEMENTS_MAX` reasonable as numbers get huge very quickly.

By default `fibonacci:generate` command runs in a separator container, Its configured to throttle CPU and memory usage.
There is no need to call `usleep` in the worker, as scheduling and parallelism is handled by docker (check `docker-compose.yml`)
However because generation of numbers are fast, its a good idea to tweak `FIBONACCI_WORKER_SLEEP_NS` in order to visualise the sequence as its being generated.

Worker will stop automatically after reaching the `FIBONACCI_ELEMENTS_MAX` limit.

#### How to

 - You can find Dockerfile(s) in `etc/` directory.
 - To check the logs of running containers run `sudo docker-compose logs -f`
 - To ssh in to app container run `sudo docker-compose exec app bash`
 - To bring down the containers run `sudo docker-compose down`
 - To connect to redis cli:
    - `sudo docker-compose exec redis redis-cli`
 - And to monitor redis activity:
    - `sudo docker-compose exec redis redis-cli monitor`
 - To delete the list and start again:
    - `sudo docker-compose exec redis redis-cli del fibonacci:seq`    
