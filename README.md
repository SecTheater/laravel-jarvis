[![StyleCI](https://styleci.io/repos/119122531/shield?branch=master)](https://styleci.io/repos/119122531)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/aad0fe4a-4ddc-4357-807e-71a2c931375f/big.png)](https://insight.sensiolabs.com/projects/aad0fe4a-4ddc-4357-807e-71a2c931375f)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sectheater/laravel-jarvis.svg?style=flat-square)](https://packagist.org/packages/sectheater/laravel-jarvis)

[![Total Downloads](https://img.shields.io/packagist/dt/sectheater/laravel-jarvis.svg?style=flat-square)](https://packagist.org/packages/sectheater/laravel-jarvis)

<p align="center"> Made with ❤️ by  SecTheater Foundation:  http://www.sectheater.org</p>
<p align="center">Package Documentation : http://www.sectheater.org/documentation
</p>

> the Jarvis CheatSheet will be availble soon 


<hr>

## Jarvis provides you the following :
### 1. Authentication System
Login, Register, Reminders,Security Questions for reseting passwords and Activations.
### 2. Comment System & Reply System
### 3. Like/Dislike System
You could always like,dislike anything such as comments,replies and posts. Also you could reset this like so the user's like or dislike is removed
### 4. Upgrading & Downgrading Users
### 5. Approving Anything
You could approve anything just by providing the model and the ID of the record you wish to approve
### 6.Tag
Assign Tags to whatever on your application
> You can Assign one tag or multiple tags within one text input and you can set the separator dynamically

### 7. Authorizing Users & Managing Roles.

## Installation Steps

### 1. Require the Package

After creating your new Laravel application you can include the Jarvis package with the following command: 

```bash
composer require sectheater/laravel-jarvis
```

### 2. Add the DB Credentials & APP_URL

Next make sure to create a new database and add your database credentials to your .env file:

```
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

You will also want to update your website URL inside of the `APP_URL` variable inside the .env file:

```
APP_URL=http://localhost:8000
```

> Add The Service Provider To your config/app.php under the Providers section

```bash
	SecTheater\Jarvis\Providers\JarvisServiceProvider::class
```

>  Add Aliases To your config/app.php

```bash
    SecTheater\Jarvis\Providers\JarvisServiceProvider::class,

```
### 3. Getting Your Environment Ready.

#### Just Run The following command.


```bash
	php artisan sectheater:install
```

##### Command Interpretation

1- It sets up your config file by registering whatever you confirm within the survey, in case you use *Everything* option that command supplies, the default configuration will be used. If you ever want to change anything , go to your config/jarvis.php and set whatever you need.
2- It will provide you a survey so the package sets up which features have to be enabled.
3- It also sets up the authentication routes.
> Jarvis doesn't depend on models at all, Everything runs through the repositories to provide you the best quality.
![Installation Preview](http://sectheater.org/assets/images/doc/installation.png)
### 4. Sample Usage
##### 4.1 Registering A User 
Whenever you try to register a user, just supply Jarivs with the data and the slug of the role which you want to assign to this user
```bash
	Jarvis::registerWithRole($data,'user')
```
#### 4.2 Login A User

Logging a user is also an easy thing to do, just pass the data the user tries to attempt with , It can be username/email and password or whatever. Then if you want to remember the user , pass the second argument with true.
```bash
	Jarvis::registerWithRole($data,true)
```

#### 4.3 Checking For Roles.
<b> Let's assume I want to check whether the current logged in user has any permissions to create a post or not. It's worth mentioning that whenever you pass many elements to check on and the user has any of them, The method will return you a boolean value </b>
```bash
	Jarvis::User()->hasAnyRole(['*.posts.create']) 
```
We are just checking here if the user has either creating permission under the user/moderator permissions.
```bash
	Jarvis::User()->hasAnyRole(['user.tags.create','moderator.tags.create'])
```
Sometimes, you want to check if a specific user has all of the roles , not just any of them.
Well That's also included.
```bash
	Jarvis::User()->hasAllRole(['admin.posts.create','admin.posts.edit'])
```
If you want to check on only one permission, you can pass that to the HasAnyRole within an array, Or just do it like the following.
```bash
	Jarvis::User()->hasRole('*.posts.create')
```
<b> There is much more details within the <a href="http://www.sectheater.org/documentation">documentation</a>.</b>
