/usr/bin/mysqld_safe --skip-syslog &
mysqladmin --silent --wait=30 ping || exit 1
echo "GRANT ALL ON *.* TO root@'%' IDENTIFIED BY 'root' WITH GRANT OPTION; FLUSH PRIVILEGES" | mysql
killall mysqld
