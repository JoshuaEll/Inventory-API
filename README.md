# Inventory-API
This repository contains the API and also the Web application version that pre-dates the api. This is a small backend project that I made as part of my Advanced Software Engineering class using php. It should be noted that all of this is could of course also be written in python. <br>
This is a small inventory API residing on a free tier AWS ec2 instance (Dreamweaver, Ubuntu, NGINX, Self-certified SSL, 1 GB of ram 30 GB of storage). I also set up a my sql database containing 5 million records.

# Web Applicatiction
This is the Web Application version that allows the user to interact with the 5 million record database. <br>
Link: https://ec2-54-144-131-180.compute-1.amazonaws.com/ (not in use anymore) <br>

![index](img/WebIndex.PNG) <br><br>
As seen in the image above it allows the user to search by: Device type, manufacturer, Serial Number and Status, with type and manufacturer being required.
This gives the user the ability to view the data returned in table format(see picture below). <br>
![list](img/ListDevices.PNG) <br><br>
From there the user can choose to view the full information of a device and either view the files associated with it or upload file to the device. <br>
![upload](img/ViewFile.PNG) <br><br>
Or, the user can choose to update the device and also create new types or insert new devices into the tables if they want to. <br>
![create](img/Create.PNG) <br><br>

# API
This is the swagger page, showing all the endpoints, that can be accessed.<br>

Link: https://ec2-54-144-131-180.compute-1.amazonaws.com/swagger/#/ (not in use anymore) <br>

![swagger](img/swagger.PNG) <br>
