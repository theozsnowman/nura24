
<p align="center">
    <img src="https://img.shields.io/github/stars/nuradev/nura24" alt="forks">
    <img src="https://img.shields.io/badge/downloads-1.2k-brightgreen" alt="downloads">
    <img src="https://img.shields.io/github/license/nuradev/nura24" alt="license">
</p>
    
<p align="center"><img src="https://nura24.com/assets/img/logo-github.png" alt="Nura24"></p>

### [Nura24](https://nura24.com) is a free suite for businesses, communities, teams, collaboration or personal websites. Create a free and professional website in minutes.

<div align="center"><a target="_blank" href="https://nura24.com"><img src="https://nura24.com/assets/img/nura24.com_1280x640.png" alt="Nura24"></a></div>

### Forget about installing and updating tons of plugins. With Nura24 Free Suite you have everything you need to build professional websites, from simple / personal websites to complex portals or business websites.

### Modules:
- **CMS**: blog, posts, pages, downloads, blocks, slider...
- **eCommerce**: Sell downloadable products or services
- **Community Forum**: Create a community for your website
- **Help Desk**: Support Tickets, Knowledge Base, FAQ manager, Contact Page Manager
- **Email Marketing**: Create email campaigns and send bulk emails to customers
- many other modules in development (Live Chat, Bookings, CRM, Projects Management, Intranet...)

Nura24 follows the best development practices, code is optimised for SEO, security and performance.

With Nura24 Suite you can create a **full multilingual website** (make translations and add different content for each language).

## Framework

Nura24 Suite is build using latest version of [**Laravel Framework**](https://laravel.com/docs), making it the best alternative for developers. Responsive templates are build using [**Bootstrap Framework**](https://getbootstrap.com).

## Requirements

Web server: Apache with mod_rewrite enabled (.htaccess files) or Nginx.

[Laravel Framework requirements](https://laravel.com/docs/8.x/deployment#server-requirements)

## :star: **[Clevada](https://clevada.com/nura24-hosting) - Fastest Cloud Hosting for Nura24 Suite**

We are very good to security and server management so we create a Cloud Hosting Service to host your websites. 

**Clevada Cloud Hosting Advantages:**
- **We install your Nura24 Suite for FREE and you have 3 days free hosting to test our servers performance.**
- Full cPanel  and FTP access
- Developer friendly: Composer available, SSH access, latest PHP version, free SSL certificate
- Superior Performance: Our scalable servers provides 3x faster performance and reliability compared to major hosting companies.
- Fastest DNS: We provide high speed, low latency and DDOS protected nameservers with 100% uptime.
- We are NOT resellers. We have high performance servers, located in the major cloud providers datacenters.

## Installation
Your hosting must have Composer and give you shell access (SSH) and ftp access outside your "public" folder.

1. ``composer create-project nuradev/nura24 nura24``

This will **download latest version** of Nura24 suite on your server.
The last argument ("nura24") is the root folder where application will be installed. You can use any folder name (must not exists).
**It is strongly recommended to install the suite in a folder outside your public folder**.

2. Go to "nura24' (or folder name where you download thge suite). **Edit '.env' file and set your app name, app url and database credentials**.

3. ``cd nura24``

(change directorty to your folder where you download the suite)

4. ``php artisan install``

This will **install and setup** your suite: create tables, add core data into tables, create administrator account.
You will be prompted to set administrator credentials (name, email and password).

5. **Move folders and files inside "public" folder to your server public folder** (for example: "public_html" if you use Apache Web Server).

*Note: If you have access to server configuration, you can keep "public" folder inside your application folder but you must configure your web server to directs all requests to your application's public/index.php file.*

6. **Give write access** (chmod 777) to this folders:
- Inside your application folder: "**/bootstrap/cache**" and all folders inside "**/storage**" folder.
- Inside your public folder: "**/uploads**" folder.

7. (optional). If you have modules that require cron jobs (eCommerce module for example), you must setup cron job in your hosting account to run every minute. More details:  [Setup Laravel Cron](https://laravel.com/docs/8.x/scheduling#running-the-scheduler).


**Note: If your hosting do not provide Composer / SSH access, you can use our high performance cloud hosting on our hosting servers: [Clevada Cloud Hosting](https://clevada.com/nura24-hosting). We can also install / setup your Nura24 Suite on your domain for FREE if you choose to host your website to us**. 


## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Gabriel Chimilevschi via [office@nura24.com](mailto:office@nura24.com). All security vulnerabilities will be promptly addressed.

## License
Nura24 Suite is open-sourced software licensed under the [GPL-3.0 License](https://opensource.org/licenses/GPL-3.0).
