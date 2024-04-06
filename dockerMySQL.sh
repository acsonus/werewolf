#!/bin/bash
#!/bin/bash

# Define the name of the Docker container and the name of the initial database
CONTAINER_NAME="mysql_werewolf_container"
DATABASE_NAME="WereWolfDB"

# Function to start the Docker container
start_container() {
    echo "Starting the Docker container..."
    docker run --name $CONTAINER_NAME -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=$DATABASE_NAME -p 3306:3306 -d mysql:latest
    echo "Docker container started."
}

# Function to stop the Docker container
stop_container() {
    echo "Stopping the Docker container..."
    docker stop $CONTAINER_NAME
    docker rm $CONTAINER_NAME
    echo "Docker container stopped."
}

# Function to import the initial database   
import_database() {
    echo "Importing the initial database..."
    cat $DATABASE_NAME.sql | docker exec -i $CONTAINER_NAME /usr/bin/mysql -u root --password=root $DATABASE_NAME
    echo "Initial database imported."
}

# Check the command line arguments
if [ "$1" == "start" ]; then
    start_container
elif [ "$1" == "stop" ]; then
    stop_container
elif [ "$1" == "import" ]; then
    import_database
else
    echo "Invalid argument. Please use 'start', 'stop', or 'import'."
fi