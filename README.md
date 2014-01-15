MyAutoDump
==========

Making MySQL backups suck a little less.

by [Scott Smitelli](mailto:scott@smitelli.com)

Installation and Requirements
-----------------------------

MyAutoDump requires PHP 5 with the `mysql` library enabled. There should be a
working MySQL server and a local copy of `mysqldump` to perform the actual dump.
The SQL dump files are run through `gzip` to make their size more manageable, so
that should be installed and working too.

###To install:

1.  Throw all the files somewhere. It really doesn't matter where.

2.  `cp config.ini-sample config.ini`

3.  Edit `config.ini` to suit your fancy. You will need to provide a user name
    and a password for the MySQL server. You can also specify the location of
    the `mysqldump` and `gzip` binaries, as well as the output directory where
    the dump files should be stored.

4.  Lock down the permissions of `config.ini`, as well as the output directory
    where the dumps will be stored. Something along the lines of:

    `chown root:root config.ini /path/to/dumps`

    `chmod 600 config.ini`

    `chmod 700 /path/to/dumps`

5.  `./myautodump.sh`

Under normal circumstances, MyAutoDump will not display any output. This
prevents emails from piling up if the script is called from a cron job. A
user-level message will be sent via `syslog()` at the start and end of each run,
and after each database is dumped.
