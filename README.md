presh
=====

Presh aims to be the Prestashop Shell, inspired by Drush, the Drush Shell for Drupal, with the goal of ease the automation of many of the tasks that by default have to be done by a human with the web interface of Prestashop, and as a consequence, reduce the time needed to setup or maintain an e-commerce web site based on Prestashop.

Presh will have a command line interface based mainly on PHP and will work on a Prestashop installation dealing directly with its underlying model objects and its configuration.

Many iterations will be needed to evolve Presh to the point where it can achieve this goal, so every suggestion, improvement and contribution will be very welcomed!

Envisioned features
===================

- Have a roadmap to guide the development
- Define some kind of convention that Prestashop modules could follow to be discovered and managed by presh
- Allow the definition of a full Prestashop site within a single plain text file; something similar to Drush Make
- Allow modules and/or themes to de downloaded from a given URL or from some kind of repository (like Subversion, Git or Mercurial)
- Provide site and/or database backing up and/or restoring functionalities
- Distribute it via some kind of PHP component distribution, like [PEAR](http://pear.php.net/)

Usage
=====

- Download presh to some directory, for example ~/presh
- Optionally update the `PATH` environment variable to contain the presh directory, for example: `export PATH=$PATH:$HOME/presh`
- Go to a directory where a Prestashop is installed
- Execute presh with any of its available methods. The general invocation is like this:

    $ presh <command> [argument1] [argument2] ... [argumentN]
    
- Some examples of using it are:

    $ presh help

    $ presh version

    $ presh update_modules
    
    $ presh update_global_value PS_SHOP_EMAIL new_address@mydomain.com

    $ presh toggle_maintenance_status

    $ presh update_maintenance_status 0

    $ presh update_maintenance_status 1
