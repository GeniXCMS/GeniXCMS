# GeniXCMS
> Update Notice: Please Update to version v1.1.11.

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FGeniXCMS%2FGeniXCMS.svg?type=shield&issueType=license)](https://app.fossa.com/projects/git%2Bgithub.com%2FGeniXCMS%2FGeniXCMS?ref=badge_shield&issueType=license) [![Latest Stable Version](https://poser.pugx.org/genix/cms/v/stable)](https://packagist.org/packages/genix/cms) [![Total Downloads](https://poser.pugx.org/genix/cms/downloads)](https://packagist.org/packages/genix/cms) [![Latest Unstable Version](https://poser.pugx.org/genix/cms/v/unstable)](https://packagist.org/packages/genix/cms) [![License](https://poser.pugx.org/genix/cms/license)](https://packagist.org/packages/genix/cms) [![Documentation Status](https://readthedocs.org/projects/genixcms/badge/?version=latest)](http://genixcms.readthedocs.org/en/latest/?badge=latest)


**GeniXCMS** is a PHP Based Content Management System and Framework (*CMSF*). It's a simple and lightweight of CMSF. Very suitable for **Intermediate PHP developer** to **Advanced Developer**. Some manual configurations are needed to make this application to work.

### Why GeniXCMS

This CMSF is a starter point to build your own online applications. With already build User manager, Content manager (Post, Pages), Menu manager, etc made you easy to add your own code and build your own custom web applications.

### Credits

**GeniXCMS** is using some of **FOSS** (free and opensource software) like :
- Twitter **Bootstrap**,
- **Summernote** Text Editor,
- **JQuery**,
- **PHP**,
- **MySQL**,
- **AdminLTE**,
- **elFinder** File Manager
- etc.

### Requirements

* Webserver - Apache/Nginx
* PHP >=8 
    - PHP-GD
    - PHP-cURL
    - PHP-OpenSSL
    - PHP-imagick
    - PHP-intl
    - PHP-mysqli
    - PHP-XML
* MySQL 4

### Recommended

* Nginx Server - for webserver
* MariaDB Server - for database
* PngQuant - for image compression

### Installation

GeniXCMS can be installed on Custom Server like VPS/Dedicated Server or on Shared Hosting.


#### Manual Upload

Upload all files to your site.

Set this directory permission to **777** (writable) :

- inc/config
- inc/themes
- inc/mods
- assets/images
- assets/images/uploads
- assets/images/uploads/thumbs
- assets/cache
- assets/cache/thumbs
- assets/cache/pages


After upload is done. Open your site at the browser. eg: http://yoursite.com

The installation wizard will appear, just follow all the instructions.


#### Using Composer

We are now ready for composer installation. Run this command at your server.

`php composer.phar create-project genix/cms`

more detail about composer, please read the documentation at http://getcomposer.org

more details of installation :
http://docs.genixcms.my.id/user-guide/installation/


### Upgrading

- Upload all files, except `inc/config/config.php`.
- edit your site's config.php, 
- add this new configuration if not exist
```php
define('SITE_ID', 'type-random-chars');
define('ADMIN_DIR', 'gxadmin');
define('USE_MEMCACHED', false);
```

- rename `SECURITY` become `SECURITY_KEY`
- Run at your browser `http://yourwebsite.com/upgrade.php`.
- Choose the previous version of your GeniXCMS version.

- Don't forget to create Cache directory if want to use Cache System


### Showcase

Showcase URL :
- [https://demo.genixcms.my.id/](https://demo.genixcms.my.id/)


### License

**GeniXCMS** License : [**MIT License**](LICENSE)

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fsemplon%2FGeniXCMS.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fsemplon%2FGeniXCMS?ref=badge_large)

### Website

Link : [https://genixcms.my.id/](https://genixcms.my.id/)


### Donate

Contact us for Donation. 


### Developer

Developed by : GeniXCMS - [https://github.com/GeniXCMS](https://github.com/GeniXCMS)

### Supported By 

