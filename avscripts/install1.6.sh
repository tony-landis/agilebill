#!/bin/bash

doinstall() {
	perl -pe "s|%%AGILE_DB_HOST%%|${db_host}|g" $1 | \
	perl -pe "s|%%AGILE_DB_DATABASE%%|${db_name}|g" | \
	perl -pe "s|%%AGILE_DB_USERNAME%%|${db_user}|g" | \
	perl -pe "s|%%AGILE_DB_PASSWORD%%|${db_pass}|g" > $2 
	echo "Installing to: $2"
}

echo -n "Enter the database IP address: "
read db_host
echo -n "Enter the database name: "
read db_name
echo -n "Enter the database username: "
read db_user
echo -n "Enter the database password: "
read db_pass
# echo
# echo "Installing templated Asterisk configuration files..."

# cd conf
# for FILE in *; do
#   doinstall ${FILE} /etc/asterisk/${FILE}
# done
# cd ..
cd scripts1.6
for FILE in *; do
	doinstall ${FILE} /usr/sbin/${FILE}
	chmod 755 /usr/sbin/${FILE}
done
cd ..

echo "Installing Cron entries..."
echo "*/15 * * * * asterisk /usr/sbin/agilevoice-export-extensions" >>/etc/crontab
echo "*/15 * * * * asterisk /usr/sbin/agilevoice-export-sip" >>/etc/crontab
echo "*/15 * * * * asterisk /usr/sbin/agilevoice-export-iax" >>/etc/crontab
echo "*/15 * * * * asterisk /usr/sbin/agilevoice-export-vm" >>/etc/crontab
echo "*/30 * * * * asterisk /usr/sbin/cdr_import_agilevoice" >>/etc/crontab

perl -MCPAN -e "install Text::CSV"

echo "Installing MySQL tables..."
echo "Installing av-tables.sql"
mysql -u ${db_user} -p${db_pass} ${db_name} < av-tables.sql


echo "Complete."