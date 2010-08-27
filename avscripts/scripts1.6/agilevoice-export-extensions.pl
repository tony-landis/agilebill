#!/usr/bin/perl -T
# Retrieves the extensions entries from the database

use DBI;
################### BEGIN OF CONFIGURATION ####################

# the name of the extensions table
$table_name = "ab_voip_did";
# the path to the extensions.conf file
# WARNING: this file will be substituted by the output of this program
$ext_conf = "/etc/asterisk/extensions_agilevoice.conf.tmp";
# the name of the box the MySQL database is running on
$hostname = "%%AGILE_DB_HOST%%";
# the name of the database our tables are kept
$database = "%%AGILE_DB_DATABASE%%";
# username to connect to the database
$username = "%%AGILE_DB_USERNAME%%";
# password to connect to the database
$password = "%%AGILE_DB_PASSWORD%%";

################### END OF CONFIGURATION #######################

my $dial = "";
my $dialarg = "";

open EXTEN, ">$ext_conf" or die "Cannot create/overwrite file: $ext_conf\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");


$statement = "SELECT did, channel, channelarg from $table_name WHERE active=1 ORDER BY did";
my $result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}
my @resultSet = @{$result};
if ( $#resultSet > -1 ) {
	foreach $row (@{ $result }) {
		my @result = @{ $row };
			$dial = $result[1];
			$dialarg = $result[2];
			if (0 == length($dialarg)) {
				$dialarg = $result[0];
			}
			if ($dial =~ /IAX/) {
				$dial = "IAX2/".$dialarg."/".$result[1];
			} else {
				$dial = "SIP/".$dialarg;
			}
			print EXTEN "exten => ".$result[0].",1,Dial(".$dial.")\n";
			print EXTEN "exten => ".$result[0].",2,Macro(exten-vm,".$result[0].",".$dial.")\n";
			print EXTEN "exten => ".$result[0].",3,Hangup\n";
	}
}
exit 0;