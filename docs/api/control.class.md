# Control Class

```
@filename: Control.class.php
@location: inc/lib/
@package: GeniXCMS
@since: 0.0.1
@author: Puguh Wijayanto (www.metalgenix.com)
@copyright: 2014-2015 Puguh Wijayanto
@license: MIT License
```

> This Documentation is still need improvement.

This class is to load the controller at `inc/lib/Control` directories. Controllers are needed to proccess the procedure and view the result at themes. 

This is the hierarchy of MVC, as GeniXCMS is using MVC structure. 

The controller are divided into some parts.

- Frontend
- Backend
- Error
- Install

This controller is flexible. You can create and add it at the **Control Class** to load it. 

## Handler Function

> This function is deprecated and will be removed on future updates

Usage: `Control::handler((string) $vars)`

This function is to load the controller types.

Example: 

We want to call the **Frontend Controller**, so use this to load it. 

`Control::handler('frontend');`

This will load the Frontend Controller.

## Frontend File Inclusion Function

Usage: `Control::incFront((string) $vars);`

This function will load the file at the Frontend directory if the file is exist. If not it will load the 404 not found page. 

This function is needed by `Control::frontend()` function. 



## Backend File Inclusion Function

Usage: `Control::incBack((string) $vars);`

This function will load the file at the FBackend directory if the file is exist. If not it will load the 404 not found page. 

This function is needed by `Control::backend()` function. 


## Frontend Function

Usage: `Control::frontend();`

This will handle the controller which file will be included at the Frontend controller.

This function will call the file using `self::incFront((string) $vars)`

If the controller is not found, the 404 error will loaded.

And Default controller is `default.control.php`


### How to load your own controller

This is simple. If you want to create your own controller and want to load it at the frontpage. Just create your controller at the Frontend directory. 

After the file is ready, open the `Control.class.php` file, and go to the `public static function frontend()` function. 

There is a variable with arrays as the value; 
`$arr = array ('post','page', 'cat', 'mod', 'sitemap', 'rss');`

Just add your controller name on it. If your file name is `store.control.php`. So the arrays become like this :

`$arr = array ('post','page', 'cat', 'mod', 'sitemap', 'rss', 'store');`


## Backend Function

Usage: `Control::backend();`

This will handle the controller which file will be included at the Backend controller.

This function will call the file using `self::incBack((string) $vars)`

If the controller is not found, the 404 error will loaded.

And Default controller is `default.control.php`



## ERROR Function

Usage: `Control::error((string) $vars='',(string) $val'');`


This function is to load the Error handler. The default is 404 not found. 

There are some error page already built. Especially for the system error, eg: `404`, `400`, `403`, `500`

Those error page had specific header so when it loaded it will read by the system as it.

Anothere error pages are : 

- Database Error `db`
- Unknown Error `unknown`
- No Access error `noaccess`

### How to use Error Handler

Using error handle is simple. Below are some examples how to use it.

#### File Inclusion
```
$file = "/path/to/file.php";
if ( file_exists($file) ) {
    include($file);
}else{
    Control::error('404');
}
```


#### No Access / Restricted Access

```
if(User::access(2)){
   echo "You are ready to go.";
}else{
   Control::error('noaccess');
}
```


#### Database Error 

```
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
$sql = 'SELECT * FROM `table` WHERE `id` = '{$id}'';
$db = $mysqli->query($sql);
if(!$db){
   Control::error('db', $mysqli->error);
}
```


### Creating Your own Error Page

If yo want to use your error handler, just create a file at Error directory inside the Control directory.

And load it when there is an error with your desired error pages.

