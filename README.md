# [ThinkPHP Basic Development Library of Zimeiti Contest Management System](https://sveil.com)

Zimeiti CMS was created by, and is maintained by [Sveil.com](https://sveil.com), and is a PHP CMS powered by [Thinkphp 5.1](https://github.com/top-think/thinkphp). Feel free to check out the [releases](https://github.com/sveil/zimeiti-cms/releases), [license](LICENSE), [screenshots](SCREENSHOTS.md), and [contribution guidelines](CONTRIBUTING.md).

## Installation

[PHP](https://php.net) 5.6+ or [HHVM](http://hhvm.com) 3.6+, a database server, and [Composer](https://getcomposer.org) are required.

1. There are 3 ways of grabbing the code:

-   Use GitHub: simply download the zip on the right of the readme
-   Use Git: `git clone git@github.com:sveil/zimeiti-cms.git`
-   Use Composer: `composer create-project sveil/zimeiti-cms --prefer-dist -s dev`

2. From a command line open in the folder, run `composer install --no-dev -o` and then `npm install`.
3. Enter your database details into `config/database.php`.
4. Run `php artisan app:install` followed by `gulp --production` to setup the application.
5. You will need to enter your mail server details into `config/mail.php`.

-   You can disable verification emails in `config/credentials.php`
-   Mail is still required for other functions like password resets and the contact form
-   You must set the contact email in `config/contact.php`
-   I'd recommend [queuing](#setting-up-queing) email sending for greater performance (see below)

6. Finally, setup an [Apache VirtualHost](http://httpd.apache.org/docs/current/vhosts/examples.html) to point to the "public" folder.

-   For development, you can simply run `php artisan serve`

## License

GNU AFFERO GENERAL PUBLIC LICENSE

Zimeiti CMS Is A PHP CMS Powered By Thinkphp 5.1

Copyright (C) 2019-2020 Richard

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
