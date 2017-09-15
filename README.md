# GeniXCMS
> Update Notice: Please Update to version v1.1.4.

[![Build Status](https://travis-ci.org/semplon/GeniXCMS.svg?branch=master)](https://travis-ci.org/semplon/GeniXCMS)
[![CircleCI](https://circleci.com/gh/semplon/GeniXCMS.svg?style=shield&circle-token=c2ef105b7d61e90dadd066ad0e25e3f53d97c6c1)](https://circleci.com/gh/semplon/GeniXCMS)
[![wercker status](https://app.wercker.com/status/69ad23cfc66fab2f4155d69bf4d47d0d/s "wercker status")](https://app.wercker.com/project/bykey/69ad23cfc66fab2f4155d69bf4d47d0d)
[![Codeship](https://codeship.com/projects/64d60110-3e1c-0133-6054-5a0949beaeb8/status?branch=master)](https://codeship.com/projects/102695)

[![Latest Stable Version](https://poser.pugx.org/genix/cms/v/stable)](https://packagist.org/packages/genix/cms) [![Total Downloads](https://poser.pugx.org/genix/cms/downloads)](https://packagist.org/packages/genix/cms) [![Latest Unstable Version](https://poser.pugx.org/genix/cms/v/unstable)](https://packagist.org/packages/genix/cms) [![License](https://poser.pugx.org/genix/cms/license)](https://packagist.org/packages/genix/cms)

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/semplon/GeniXCMS?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Documentation Status](https://readthedocs.org/projects/genixcms/badge/?version=latest)](http://genixcms.readthedocs.org/en/latest/?badge=latest)


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
* PHP >=5.5.9
    - PHP-GD
    - PHP-cURL
    - PHP-OpenSSL
* MySQL 4

### Recommended
* MariaDB Server

### Installation

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


After upload is done. Open your site at the browser. eg: http://yoursite.com

The installation wizard will appear, just follow all the instructions.

#### Using Composer

We are now ready for composer installation. Run this command at your server.

`php composer.phar create-project genix/cms`

more detail about composer, please read the documentation at http://getcomposer.org

more details of installation :
http://docs.genix.id/user-guide/installation/

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
- [http://demo.genix.id](http://demo.genix.id)

### License
**GeniXCMS** License : [**MIT License**](LICENSE)

### Website
Link : [http://genix.id](http://genix.id)

### Donate
Contact us for Donation. 

### Developer
Developed by : Puguh Wijayanto - [metalgenix.com](http://metalgenix.com)
