pipeline {
    agent any
    
    environment {
        // Define any environment variables here
        APP_ENV = 'testing'
        COMPOSER_CACHE_DIR = '/tmp/composer-cache'  // Optional: speeds up Composer installations
    }
    
    stages {
        stage('Checkout') {
            steps {
                // Clone the repository
                git branch: 'main', url: 'https://github.com/AltafAhmedGeek/task_maganement_api.git'
            }
        }

        stage('Install PHP Dependencies') {
            steps {
                // Install composer dependencies
                sh 'composer install --prefer-dist --no-ansi --no-interaction --no-progress'
            }
        }

        stage('Deploy') {
            steps {
                // Deployment steps can be adjusted to your specific environment
                // For example, rsync files to the server
                sh 'rsync -avz --exclude=.git --exclude=node_modules --exclude=vendor . your-server:/path/to/deploy'
            }
        }
    }

    post {
        always {
            // Clean up the workspace after build
            cleanWs()
        }
        success {
            echo 'Build and deployment succeeded!'
        }
        failure {
            echo 'Build or deployment failed.'
        }
    }
}
