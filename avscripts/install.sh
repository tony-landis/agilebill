#!/bin/bash

doinstall() {
	perl -pe "s|%%AGILE_DB_HOST%%|${DB_HOST}|g" $1 | \
	perl -pe "s|%%AGILE_DB_DATABASE%%|${DB_NAME}|g" | \
	perl -pe "s|%%AGILE_DB_USERNAME%%|${DB_USER}|g" | \
	perl -pe "s|%%AGILE_DB_PASSWORD%%|${DB_PASS}|g" > $2 
	echo "Installing to: $2"
}

echo -n "Enter the database IP address: "
read DB_HOST
echo -n "Enter the database name: "
read DB_NAME
echo -n "Enter the database username: "
read DB_USER
echo -n "Enter the database password: "
read DB_PASS
echo
echo "Installing templated Asterisk configuration files..."

cd conf
for FILE in *; do
	doinstall ${FILE} /etc/asterisk/${FILE}
done
cd ..
cd scripts
for FILE in *; do
	doinstall ${FILE} /usr/sbin/${FILE}
	chmod 755 /usr/sbin/${FILE}
done
cd ..
echo pwd
echo "Installing Cron entries..."
echo "*/15 * * * * root /usr/sbin/agilevoice-export-extensions" >>/etc/crontab
echo "*/15 * * * * root /usr/sbin/agilevoice-export-sip" >>/etc/crontab
echo "*/15 * * * * root /usr/sbin/agilevoice-export-iax" >>/etc/crontab
echo "*/15 * * * * root /usr/sbin/agilevoice-export-vm" >>/etc/crontab
echo "*/30 * * * * root /usr/sbin/cdr_import_agilevoice" >>/etc/crontab

perl -MCPAN -e "install Text::CSV"

echo "Installing MySQL tables..."
echo "Installing av-tables.sql"
mysql -u ${DB_USER} -p${DB_PASS} ${DB_NAME} < av-tables.sql


echo "Complete."

