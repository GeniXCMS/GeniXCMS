# Configuration
After You logged in to the Administrator area. Now it's time to Setting Up your site. 

Click on the Setting Menu on the left side. 
A new page will shows up .

## General Settings

![General Settings](img/general-settings.png)

There are some input fields appear. Fill in the necessary field. Especially :

- Website Slogan
- Website Keywords
- Website Description

Those are important. Especially for SEO.

After those are filled up, go to the next tab.

## E-Mail

![EMail Settings](img/email-settings.png)

This page is for email sending features. There are some options to be picked. 

### PHP Mail 

If you want to send email just using your mail program just pick **Mail** at the **Mail/SMTP** options. And the other field is not necessary. 

### SMTP Server

If You want to use outgoing email using SMTP so pick **SMTP** at the **Mail/SMTP** options. And fill in other input. 

Choose whether the SMTP is using Plain Authentification or Use SSL. 

Fill the SMTP Mailserver, SMTP Username, and SMTP Password you had. 

## Social 
This is Optional. Depends on the Themes you are using. If no options of using this so left it blank. 

## Logo

![Logo Settings](img/logo-settings.png)

### Logo
Now it's time to personalize your site. Upload Your own Logo. **Choose a small images** please, so it won't make your site bloating. You can choose wheter Upload it by your self or just use already online Logo. 

If you want to use already uploaded logo at the internet, just thick the checkbox button at the **Use Image URL** and paste the image address. 

It automatically will use the image url rather than the Uploaded Logo. 

### Favicon
Insert your favicon url. Full url if i may say. Since it won't trouble when the **SMART_URL** is activated. 

## Library
![Library Settings](img/library-settings.png)

These are the dependencies we had to load for website to work well. We need :

- jQuery
- Fontawesome
- Bootstrap
- Summernote Editor
- Bootstrap Validator

jQuery and Fontawesome are from CDN and others are at assets directory.

For jQuery, You can input which version you wanto to use. Just fill in the version number. Others are still not supported yet. 

## Posts

![Posts Settings](img/posts-settings.png)

**Post Per Page** is the Options how many posts are appear at the front page. 

**Pinger** is an address of Pinger for search engine. We already insert some. Just add it if you want.

**Pagination** type, there are two options. First is Number, and second is Pager. **Number** means the page will shows as numbers. and pager the page will shows as **Prev** and **Next**.

## Payment

![Payment Settings](img/payment-settings.png)

Currently We are still in developing the Payment Class. Especially the **PayPal Class**. 

Even that, this field is functional. And can be run, see our sample store [SerieShop2](http://serieshop2.gxapp.top) 

