#!/usr/bin/perl -Tw
# Retrieves the iax user/peer entries from the database

use DBI;
################### BEGIN OF CONFIGURATION ####################

# the name of the extensions table
$table_name = "ab_voip_iax";
# the path to the extensions.conf file
# WARNING: this file will be substituted by the output of this program
$iax_conf = "/etc/asterisk/iax_agilevoice.conf.tmp";
# the name of the box the MySQL database is running on
$hostname = "%%AGILE_DB_HOST%%";
# the name of the database our tables are kept
$database = "%%AGILE_DB_DATABASE%%";
# username to connect to the database
$username = "%%AGILE_DB_USERNAME%%";
# password to connect to the database
$password = "%%AGILE_DB_PASSWORD%%";

################### END OF CONFIGURATION #######################

$additional = "";

open EXTEN, ">$iax_conf" or die "Cannot create/overwrite extensions file: $iax_conf\n";

$dbh = DBI->connect("dbi:mysql:dbname=$database;host=$hostname", "$username", "$password");
$statement = "SELECT keyword,data from $table_name where iax='0' and keyword <> 'account' and (flags <> 1 or flags is null)";
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
		$additional .= $result[0]."=".$result[1]."\n";
	}
}

# items with id like 9999999% get put at the top of the file
$statement = "SELECT keyword,data from $table_name where iax LIKE '9999999%' and keyword <> 'account' and (flags <> 1 or flags is null)";
$result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
  exit;
}
@resultSet = @{$result};
if ( $#resultSet > -1 ) {
	foreach $row (@{ $result }) {
		my @result = @{ $row };
		$top .= $result[0]."=".$result[1]."\n";
	}
	print EXTEN "$top\n";
}

$statement = "SELECT data,iax from $table_name where keyword='account' and (flags <> 1 or flags is null) group by data";
$result = $dbh->selectall_arrayref($statement);
unless ($result) {
  # check for errors after every single database call
  print "dbh->selectall_arrayref($statement) failed!\n";
  print "DBI::err=[$DBI::err]\n";
  print "DBI::errstr=[$DBI::errstr]\n";
}

@resultSet = @{$result};
if ( $#resultSet == -1 ) {
  exit;
}

foreach my $row ( @{ $result } ) {
	my $account = @{ $row }[0];
	my $id = @{ $row }[1];
	print EXTEN "[$account]\n";
	$statement = "SELECT keyword,data from $table_name where iax='$id' and keyword <> 'account' and (flags <> 1 or flags is null) order by keyword DESC";
	my $result = $dbh->selectall_arrayref($statement);
	unless ($result) {
		# check for errors after every single database call
		print "dbh->selectall_arrayref($statement) failed!\n";
		print "DBI::err=[$DBI::err]\n";
		print "DBI::errstr=[$DBI::errstr]\n";
		exit;
	}

	my @resSet = @{$result};
	if ( $#resSet == -1 ) {          
		print "no results\n";
		exit;
	}
	foreach my $row ( @{ $result } ) {
		my @result = @{ $row };
		print EXTEN "$result[0]=$result[1]\n";
	}                                         	
	print EXTEN "$additional\n";
}

exit 0;

