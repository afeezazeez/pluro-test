#!/bin/bash
set -e

GREEN=$(tput setaf 2)
PINK=$(tput setaf 5)

if [[ "$1" == "start" ]]; then

    echo "${PINK}Starting Docker containers ..."
    if ! docker-compose up -d; then
        echo "${PINK}Error: Failed to start Docker containers. Make sure Docker is installed and running."
        exit 1
    fi

    echo "${PINK}Building docker images ..."
        if ! docker-compose build; then
            echo "${PINK}Error: Failed to build Docker images."
            exit 1
        fi

    echo "${PINK}Installing composer ..."
    if ! composer install --quiet; then
        echo "${PINK}Error: Composer installation failed."
        exit 1
    fi

    echo "${PINK}Installing composer packages ..."
            if ! cp .env.example .env; then
                echo "${PINK}Error: Failed to install"
                exit 1
            fi


    echo "${PINK}Generating app key ..."
    if ! docker-compose exec app php artisan key:generate; then
        echo "${PINK}Error: Failed to generate app key."
        exit 1
    fi


    echo "${PINK}Generating swagger docs ..."
            if ! docker compose exec app php artisan l5-swagger:generate; then
                echo "${PINK}Error: Failed to generate swagger doc."
                exit 1
            fi

    # Install dependencies
    echo -e "${PINK}Installing dependencies ...${RESET}"
          if ! docker compose exec app npm install; then
              echo -e "${PINK}Error: Failed to install dependencies.${RESET}"
              exit 1
          fi

        # Build frontend assets
        if ! docker compose exec app npm run build; then
              echo -e "${PINK}Error: Failed to build frontend assets.${RESET}"
              exit 1
        fi

    echo "${GREEN}Setup completed successfully!"


elif [[ "$1" == "stop" ]]; then
    echo "${PINK}Stopping Docker containers ..."
    if ! docker-compose down; then
        echo "${PINK}Error: Failed to stop Docker containers."
        exit 1
    fi
    echo "${GREEN}Docker containers stopped successfully!"
else
    echo "${PINK}Usage: $0 [start|stop]"
    exit 1
fi
