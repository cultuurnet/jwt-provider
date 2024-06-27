# JWTProvider with Docker

## Prerequisite
- Install Docker Desktop
- appconfig: you'll have to clone [appconfig](https://github.com/cultuurnet/appconfig) in the same folder as where you will clone [udb3-backend](https://github.com/cultuurnet/udb3-backend)

## Configure

```
$ make config
```

## Start

### Docker

Start the docker containers with the following command. Make sure to execute this inside the root of the project.
```
$ make up
```

### Composer packages

To install all composer packages & migrate the database, run the following command:
```
$ make init
```

### CI

To execute all CI tasks, run the following command:
```
$ make ci
```

### Debugging

For local debugging purposes, a sample `jwt-example.php` is included in the `web`-folder. 
To test it go to http://localhost:9999/jwt-example.php
