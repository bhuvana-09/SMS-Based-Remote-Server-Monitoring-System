SMS-Based Remote Server Monitoring System
Overview

This project is a web-based system that monitors server performance (CPU, Memory, Disk usage).
If any value exceeds the set threshold, the system automatically sends an SMS alert to the administrator.

Technologies Used

PHP

MySQL

HTML, CSS

Cron Jobs

SMS API

Database Tables

administrators – Stores admin details

servers – Stores server information

metrics – Stores CPU, memory, disk usage

alerts – Stores alert records

thresholds – Stores limit values

How It Works

Server metrics are collected.

Values are compared with thresholds.

If exceeded, an alert is created.

SMS notification is sent to admin.

Features

Real-time monitoring

Automatic SMS alerts

SQL queries (joins, subqueries, aggregates)

Database normalized up to 3NF

Conclusion

This project demonstrates practical implementation of DBMS concepts, PHP-MySQL integration, and automated server monitoring with SMS notifications.
