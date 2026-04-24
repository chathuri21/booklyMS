CREATE DATABASE IF NOT EXISTS user_service_db;
CREATE DATABASE IF NOT EXISTS appointment_service_db;
GRANT ALL PRIVILEGES ON user_service_db.* TO 'microservice_user'@'%';
GRANT ALL PRIVILEGES ON appointment_service_db.* TO 'microservice_user'@'%';
FLUSH PRIVILEGES;