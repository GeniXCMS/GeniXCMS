# Installation
Installing GeniXCMS is easy. Below are some step to follow to install GeniXCMS from zero.

## Requirements

Before starting installation, please make sure your webserver is meet this requirements.

* Using PHP minimum 5.3
* Using MySQL Server at least version 4

That's the minimum requirements we need. But don't forget to install the webserver already. Since this application will run on the webserver. You can use many kinds webserver as you want. 

### Recommendation

* [Nginx Server](http://www.nginx.org) + PHP-FPM
* MariaDB Server


## Uploading Files

Before we install the CMS, we had to put the files into the webserver. Depends on the hosting you are using, this is basically the same proccess to all hosting provider.

You can upload it via FTP, or File Managers on the Control Panel. Please Ask your hosting provider about how to do this. 

### File Permission
The next step is setting up the file permission so it can be write during the installation proccess. Please set the permission of these files to **777** (***writable***).

- inc/config
- assets/images
- assets/images/uploads
- assets/images/uploads/thumbs


## Preparing Database
Installation cannot be run before the database is set. So, go to the Database manager at your Hosting Control Panel and create new database, and assign the user to the database. 

Save the database username, database name and database the password. We will need this for the next step.

## Run the installation
To run the installer is simple. Just open your site at the browser. An installation wizard will shows up. Just fill in the field and follow the wizard until finish. 


## Login Dashboard

After the installation is done. Now follow the link at the end of the installation and login to dashboard.

Login with Username and Password just You submit.