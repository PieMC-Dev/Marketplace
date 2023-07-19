# PieMC_Marketplace

## Usage:

* Create database name PieMC
* Create users inside PieMC using:
```sql
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    github_user_id INT(11) NOT NULL,
    github_username VARCHAR(255) NOT NULL,
    access_token VARCHAR(255) NOT NULL
);
```
* Create plugins inside PieMC using:
```sql
CREATE TABLE plugins (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    version VARCHAR(50) NOT NULL,
    author VARCHAR(255) NOT NULL,
    license VARCHAR(100) NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    logo VARCHAR(255),
    repository_url VARCHAR(255) NOT NULL
);
```

