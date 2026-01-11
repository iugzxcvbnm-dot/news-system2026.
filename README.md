# News System

A lightweight news management system built with plain PHP and Apache. This application provides a simple interface to display news articles and includes a login page for administrative access. Designed for educational purposes and containerized for reliable, reproducible deployment.

## Tech Stack
- **Language**: PHP 8.2
- **Web Server**: Apache 2.4 (via official `php:8.2-apache` Docker image)
- **Containerization**: Docker & Docker Compose
- **Frontend**: Vanilla HTML/CSS (no frameworks)

## How to Build and Run Using Docker

Follow these steps to run the project in under 2 minutes:

1. **Clone the repository**:
   ```bash
   git clone https://github.com/iugzxcvbnm-dot/news-system2026..git
   cd news-system

  2.  Build and start the application:
   docker-compose up -d --build

  3. Verify it’s running:
   docker-compose ps

  4. How to Stop the Container and Clean Up?

   To stop and remove the container:
   docker-compose down

   To also remove persisted uploads:
   docker-compose down -v

    5.Access the application:
   Open your browser and go to:
   http://localhost:8080
   
   6.How to Test the Project:

Open the login page in the browser

Enter valid credentials stored in the database

Successful login confirms database connectivity and application functionality


Deployment Steps:


1-Open the repository in GitHub Codespaces


2-Verify Git and Docker are installed:


git --version


docker --version


3-If Docker is not installed, install it:


sudo apt update && sudo apt install -y docker.io docker-compose


sudo usermod -aG docker $USER && newgrp docker


4-Clone the repository (if not auto-cloned):


git clone https://github.com/iugzxcvbnm-dot/news-system2026..git


cd news-system2026.


5-docker-compose up -d --build


6-docker-compose ps


7-Access the app via the Ports panel in Codespaces


https://refactored-space-rotary-phone-5g74x4prwqprfv66w-8080.app.github.dev/



