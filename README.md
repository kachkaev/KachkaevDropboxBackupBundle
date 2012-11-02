KachkaevDropboxBackupBundle
===========================

This bundle allows you backing up your mysql database to dropbox using symfony console command.

Uses modified version of [DropboxUploader by hakre](https://github.com/hakre/DropboxUploader).

Installation
------------

### Composer

Add the following dependency to your project’s composer.json file:

```js
    "require": {
        // ...
        "kachkaev/dropbox-backup-bundle": "dev-master"
        // ...
    }
```
Now tell composer to download the bundle by running the command:

```bash
$ php composer.phar update kachkaev/dropbox-backup-bundle
```

Composer will install the bundle to `vendor/kachkaev` directory.

### Adding bundle to your application kernel

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Kachkaev\DropboxBackupBundle\KachkaevDropboxBackupBundle(),
        // ...
    );
}
```

Configuration
-------------

Here is the default configuration for the bundle:

```yml
kachkaev_dropbox_backup:  
    database:             

        # Only pdo_mysql is supported at the moment
        driver:               pdo_mysql 
        host:                 localhost 
        port:                 ~ 
        dbname:               ~ # Required
        user:                 ~ # Required
        password:             ~ # Required

        # Directory relative to Dropbox root
        output_directory:     

        # Output filename consists of prefix and timestamp
        output_fileprefix:    

        # Set to true to compress .sql to .tgz
        output_compression:   false 

    # Dropbox account credentials (use parameters in config.yml and store real values in prameters.yml)
    dropbox:              
        user:                 ~ # Required
        password:             ~ # Required
```

It is recommended to keep real values for logins and passwords in your parameters.yml file, e.g.:

```yml
# app/config/config.yml
kachkaev_dropbox_backup:
    database:
        driver:   %database_driver%
        host:     %database_host%
        port:     %database_port%
        dbname:   %database_name%
        user:     %database_user%
        password: %database_password%
        
        output_directory: db-backup/
        output_fileprefix: db_
        output_compression: true
    dropbox:
        user: %dropbox_user%
        password: %dropbox_password%
```

```yml
# app/config/parameters.yml
	# ...
    database_driver: pdo_mysql
    database_host: localhost
    database_port: null
    database_name: myDatabase
    database_user: myLogin
    database_password: myDatabasePassword
    # ...
    dropbox_user: email@example.com
    dropbox_password: myDropboxPassword
	# ...
```


Usage
-----

The bundle adds one command to symfony console: ``app/console dropbox_backup:database`` which you execute periodically as a cron job.
For example the following cron command dumps your database every 3 hours on a machine with FreeBSD:
```
# /var/cron/tabs/root
0       */03    *       *       *       cd /path/to/symfony_project/root_dir && su -m symfony_user -c 'app/console dropbox_backup:database --env=prod'
```

At the moment the bundle only works with a single mysql database, but more database types can be added if there is a demand. In addition, the bundle can be extended to backup content. not only the database.