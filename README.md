\# FitZone Fitness Center



A PHP and MySQL fitness center web application built for XAMPP. It supports user registration, login, role-based dashboards, customer queries, membership requests, and class bookings.



\## Features



\- Customer registration and login

\- Role-based dashboards for customer, management, and admin

\- Customer query and reply system

\- Membership plan requests

\- Class booking and cancellation

\- Admin user management

\- Management/admin membership approval

\- MySQL database export included



\## Requirements



\- XAMPP

\- PHP 8+

\- MySQL/MariaDB

\- Web browser



\## Setup



1\. Copy the project folder to:



&#x20;  `C:\\xampp\\htdocs\\FitzoneTest`



2\. Start Apache and MySQL in XAMPP.



3\. Open phpMyAdmin:



&#x20;  `http://localhost/phpmyadmin`



4\. Create a database named:



&#x20;  `fitzone`



5\. Import:



&#x20;  `fitzone.sql`



6\. Open the project:



&#x20;  `http://localhost/FitzoneTest/`



\## Database Configuration



Default database settings are in `db.php`:



\- Host: `localhost`

\- User: `root`

\- Password: empty

\- Database: `fitzone`



You can also override these using environment variables:



\- `FITZONE\_DB\_HOST`

\- `FITZONE\_DB\_USER`

\- `FITZONE\_DB\_PASS`

\- `FITZONE\_DB\_NAME`



\## Main Pages



\- Home: `index.php`

\- Membership: `membership.php`

\- Blog: `blog.php`

\- Register: `register.php`

\- Login: `login.php`

\- Dashboard: `dashboard.php`



\## Notes



This project is intended for local development and academic/demo use.



