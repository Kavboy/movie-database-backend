## Movie Database Backend

This project was made to handle a large collection of Bluray and DVD movies, for a student project.
It is based on Laravel and a MSSQL database.

## The docker environment
Requirements:
* Docker with docker-compose.

Copy the .env.example to a .env file and make the needed changes you want.

Then execute 
```
docker-compose up -d
```
in the root directory. After successfully execution the REST API is available at http://localhost:8080/api/v1.


### Fresh install
Since docker does not wait for container setup, there needed to be a separate routine to setup the environment.
So to completely reinstall the docker environment, you need to delete the following folders and files:
* .installed
* 'docker/mssql/data

You need to also remove the docker containers and images.
