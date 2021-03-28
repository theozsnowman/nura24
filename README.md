<p align="center"><img src="https://nura24.com/assets/img/logo-github.png" alt="Nura24"></p>

### [Nura24](https://nura24.com) is a free suite for businesses, communities, teams, collaboration or personal websites. Create a free and professional website in minutes.

Forget about installing and updating tons of plugins. Forget paying hundred or even thousands USD for other suites with the same features.

### Modules:
- **CMS**: blog, posts, pages, downloads, blocks, slider...
- **eCommerce**: Sell downloadable products or services
- **Community Forum**: Create a community for your website
- **Help Desk**: Support Tickets, Knowledge Base, FAQ manager, Contact Page Manager
- **Email Marketing**: Create email campaigns and send bulk emails to customers
- many other modules in development (Live Chat, Bookings, CRM, Projects Management...)

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

5. **Setup your public folder:**
- Option 1: Your web server must directs all requests to your application's public/index.php file (located in your folder where you install the suite). This can be set if you have full access to your server configuration.
- Option 2: If your hosting do not permit access to server configuration, you can move folders and files inside "public" folder to your server public folder (for example: "public_html" if you use Apache Web Server).

6. **Give write access** (chmod 777) to this folders:
- Inside your application folder: "**/bootstrap/cache**" and all folders inside "**/storage**" folder.
- Inside your public folder: "**/uploads**" folder.

7. (optional). If you have modules that require cron jobs (eCommerce module for example), you must setup cron job in your hosting account to run every minute. More details:  [Setup Laravel Cron](https://laravel.com/docs/8.x/scheduling#running-the-scheduler).


**Note: If your hosting do not provide Composer / SSH access, you can use our high performance cloud hosting on our hosting servers: [Clevada Cloud Hosting](https://clevada.com/nura24-hosting). We can also install / setup your Nura24 Suite on your domain for FREE if you choose to host your website to us**. 


## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Gabriel Chimilevschi via [office@nura24.com](mailto:office@nura24.com). All security vulnerabilities will be promptly addressed.

## License
Nura24 Suite is open-sourced software licensed under the [GPL-3.0 License](https://opensource.org/licenses/GPL-3.0).
