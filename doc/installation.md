# <a id="installation"></a> Installation

The preferred way of installing Icinga Web 2 is to use the official package repositories depending on which operating
system and distribution you are running. But it is also possible to install Icinga Web 2 directly from source.

## <a id="installing-requirements"></a> Installing Requirements

* A web server, e.g. Apache or nginx
* PHP >= 5.3.0 w/ gettext, intl and OpenSSL support
* MySQL or PostgreSQL PHP libraries when using a database for authentication or for storing preferences into a database
* LDAP PHP library when using Active Directory or LDAP for authentication
* Icinga 1.x w/ Livestatus or IDO; Icinga 2.x w/ Livestatus or IDO feature enabled
* MySQL or PostgreSQL PHP libraries when using IDO

## <a id="installing-from-package"></a> Installing Icinga Web 2 from Package

Below is a list of official package repositories for installing Icinga Web 2 for various operating systems.

Distribution            | Repository
------------------------|---------------------------
Debian                  | [debmon](http://debmon.org/packages/debmon-wheezy/icingaweb2), [Icinga Repository](http://packages.icinga.org/debian/)
Ubuntu                  | [Icinga Repository](http://packages.icinga.org/ubuntu/)
RHEL/CentOS             | [Icinga Repository](http://packages.icinga.org/epel/)
openSUSE                | [Icinga Repository](http://packages.icinga.org/openSUSE/)
SLES                    | [Icinga Repository](http://packages.icinga.org/SUSE/)
Gentoo                  | -
FreeBSD                 | -
ArchLinux               | [Upstream](https://aur.archlinux.org/packages/icingaweb2)

Packages for distributions other than the ones listed above may also be available.
Please contact your distribution packagers.

### <a id="package-repositories"></a> Setting up Package Repositories

You need to add the Icinga repository to your package management configuration for installing Icinga Web 2.
Below is a list with examples for various distributions.

**Debian (debmon)**:
````
wget -O - http://debmon.org/debmon/repo.key 2>/dev/null | apt-key add -
echo 'deb http://debmon.org/debmon debmon-wheezy main' >/etc/apt/sources.list.d/debmon.list
apt-get update
````

**Ubuntu Trusty**:
````
wget -O - http://packages.icinga.org/icinga.key | apt-key add -
add-apt-repository 'deb http://packages.icinga.org/ubuntu icinga-trusty main'
apt-get update
````

For other Ubuntu versions just replace trusty with your distribution's code name.

**RHEL and CentOS**:
````
rpm --import http://packages.icinga.org/icinga.key
curl -o /etc/yum.repos.d/ICINGA-release.repo http://packages.icinga.org/epel/ICINGA-release.repo
yum makecache
````

**Fedora**:
````
rpm --import http://packages.icinga.org/icinga.key
curl -o /etc/yum.repos.d/ICINGA-release.repo http://packages.icinga.org/fedora/ICINGA-release.repo
yum makecache
````

**SLES 11**:
````
zypper ar http://packages.icinga.org/SUSE/ICINGA-release-11.repo
zypper ref
````

**SLES 12**:
````
zypper ar http://packages.icinga.org/SUSE/ICINGA-release.repo
zypper ref
````

**openSUSE**:
````
zypper ar http://packages.icinga.org/openSUSE/ICINGA-release.repo
zypper ref
````

#### <a id="package-repositories-rhel-notes"></a> RHEL/CentOS Notes

The packages for RHEL/CentOS depend on other packages which are distributed as part of the
[EPEL repository](http://fedoraproject.org/wiki/EPEL). Please make sure to enable this repository by following
[these instructions](http://fedoraproject.org/wiki/EPEL#How_can_I_use_these_extra_packages.3F).

> Please note that installing Icinga Web 2 on **RHEL/CentOS 5** is not supported due to EOL versions of PHP and
> PostgreSQL.

#### <a id="package-repositories-wheezy-notes"></a> Debian wheezy Notes

The packages for Debian wheezy depend on other packages which are distributed as part of the
[wheezy-packports](http://backports.debian.org/) repository. Please make sure to enable this repository by following
[these instructions](http://backports.debian.org/Instructions/).

### <a id="installing-from-package-example"></a> Installing Icinga Web 2

You can install Icinga Web 2 by using your distribution's package manager to install the `icingaweb2` package.
Below is a list with examples for various distributions. The additional package `icingacli` is necessary
for being able to follow further steps in this guide.

**Debian and Ubuntu**:
````
apt-get install icingaweb2 icingacli
````
For Debian wheezy please read the [package repositories notes](#package-repositories-wheezy-notes).

**RHEL, CentOS and Fedora**:
````
yum install icingaweb2 icingacli
````
For RHEL/CentOS please read the [package repositories notes](#package-repositories-rhel-notes).

**SLES and openSUSE**:
````
zypper install icingaweb2 icingacli
````

### <a id="preparing-web-setup-from-package"></a> Preparing Web Setup

You can set up Icinga Web 2 quickly and easily with the Icinga Web 2 setup wizard which is available the first time
you visit Icinga Web 2 in your browser. When using the web setup you are required to authenticate using a token.
In order to generate a token use the `icingacli`:
````
icingacli setup token create
````

In case you do not remember the token you can show it using the `icingacli`:
````
icingacli setup token show
````

Finally visit Icinga Web 2 in your browser to access the setup wizard and complete the installation:
`/icingaweb2/setup`.

## <a id="installing-from-source"></a> Installing Icinga Web 2 from Source

Although the preferred way of installing Icinga Web 2 is to use packages, it is also possible to install Icinga Web 2
directly from source.

### <a id="getting-the-source"></a> Getting the Source

First of all, you need to download the sources. Icinga Web 2 is available through a Git repository. You can clone this
repository either via git or http protocol using the following URLs:

  * git://git.icinga.org/icingaweb2.git
  * http://git.icinga.org/icingaweb2.git

There is also a browsable version available at
[git.icinga.org](https://git.icinga.org/?p=icingaweb2.git;a=summary "Icinga Web 2 Git Repository").
This version also offers snapshots for easy download which you can use if you do not have git present on your system.

````
git clone git://git.icinga.org/icingaweb2.git
````

### <a id="installing-from-source-example"></a> Installing Icinga Web 2

Choose a target directory and move Icinga Web 2 there.

````
mv icingaweb2 /usr/share/icingaweb2
````

### <a id="configuring-web-server"></a> Configuring the Web Server

Use `icingacli` to generate web server configuration for either Apache or nginx.

**Apache**:
````
./bin/icingacli setup config webserver apache --document-root /usr/share/icingaweb2/public
````

**nginx**:
````
./bin/icingacli setup config webserver nginx --document-root /usr/share/icingaweb2/public
````

Save the output as new file in your webserver's configuration directory.

Example for Apache on RHEL or CentOS:
````
./bin/icingacli setup config webserver apache --document-root /usr/share/icingaweb2/public > /etc/httpd/conf.d/icingaweb2.conf
````

### <a id="preparing-web-setup-from-source"></a> Preparing Web Setup

You can set up Icinga Web 2 quickly and easily with the Icinga Web 2 setup wizard which is available the first time
you visit Icinga Web 2 in your browser. Please follow the steps listed below for preparing the web setup.

Because both web and CLI must have access to configuration and logs, permissions will be managed using a special
system group. The web server user and CLI user have to be added to this system group.

Add the system group `icingaweb2` in the first place.

**Fedora, RHEL, CentOS, SLES and OpenSUSE**:
````
groupadd -r icingaweb2
````

**Debian and Ubuntu**:
````
addgroup --system icingaweb2
````

Add your web server's user to the system group `icingaweb2`:

**Fedora, RHEL and CentOS**:
````
usermod -a -G icingaweb2 apache
````

**SLES and OpenSUSE**:
````
usermod -A icingaweb2 wwwrun
````

**Debian and Ubuntu**:
````
usermod -a -G icingaweb2 www-data
````

Use `icingacli` to create the configuration directory which defaults to **/etc/icingaweb2**:
````
./bin/icingacli setup config directory
````


When using the web setup you are required to authenticate using a token. In order to generate a token use the
`icingacli`:
````
./bin/icingacli setup token create
````

In case you do not remember the token you can show it using the `icingacli`:
````
./bin/icingacli setup token show
````

Finally visit Icinga Web 2 in your browser to access the setup wizard and complete the installation:
`/icingaweb2/setup`.

## <a id="upgrading-to-beta2"></a> Upgrading to Icinga Web 2 Beta 2

Icinga Web 2 Beta 2 introduces access control based on roles for secured actions. If you've already set up Icinga Web 2,
you are required to create the file **roles.ini** beneath Icinga Web 2's configuration directory with the following
content:
````
[administrators]
users = "your_user_name, another_user_name"
permissions = "*"
````

After please log out from Icinga Web 2 and log in again for having all permissions granted.

If you delegated authentication to your web server using the `autologin` backend, you have to switch to the `external`
authentication backend to be able to log in again. The new name better reflects what’s going on. A similar change
affects environments that opted for not storing preferences, your new backend is `none`.

## <a id="upgrading-to-beta3"></a> Upgrading to Icinga Web 2 Beta 3

Because Icinga Web 2 Beta 3 does not introduce any backward incompatible change you don't have to change your
configuration files after upgrading to Icinga Web 2 Beta 3.

## <a id="upgrading-to-rc1"></a> Upgrading to Icinga Web 2 Release Candidate 1

The first release candidate of Icinga Web 2 introduces the following non-backward compatible changes:

* The database schema has been adjusted and the tables `icingaweb_group` and
  `icingaweb_group_membership` were altered to ensure referential integrity.
  Please use the upgrade script located in **etc/schema/** to update your
  database schema
* Users who are using PostgreSQL < v9.1 are required to upgrade their
  environment to v9.1+ as this is the new minimum required version
  for utilizing PostgreSQL as database backend
* The restrictions `monitoring/hosts/filter` and `monitoring/services/filter`
  provided by the monitoring module were merged together. The new
  restriction is called `monitoring/filter/objects` and supports only a
  predefined subset of filter columns. Please see the module's security
  related documentation for more details.

## <a id="upgrading-to-2.0.0"></a> Upgrading to Icinga Web 2 2.0.0

* Icinga Web 2 installations from package on RHEL/CentOS 7 now depend on `php-ZendFramework` which is available through
the [EPEL repository](http://fedoraproject.org/wiki/EPEL). Before, Zend was installed as Icinga Web 2 vendor library
through the package `icingaweb2-vendor-zend`. After upgrading, please make sure to remove the package
`icingaweb2-vendor-zend`.

* Icinga Web 2 version 2.0.0 requires permissions for accessing modules. Those permissions are automatically generated
for each installed module in the format `module/<moduleName>`. Administrators have to grant the module permissions to
users and/or user groups in the roles configuration for permitting access to specific modules.
In addition, restrictions provided by modules are now configurable for each installed module too. Before,
a module had to be enabled before having the possibility to configure restrictions.
