#!/usr/bin/perl -T
# Retrieves the voicemail entries from the database

use DBI;
################### BEGIN OF CONFIGURATION ####################

# the name of the extensions table
$table_name = "ab_voip_did";
# the name of the blacklist table
$btable_name = "ab_voip_blacklist";
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


$statement = "SELECT did, channel, channelarg, cnam, callforwardingenabled, busycallforwardingenabled, voicemailafter, cfringfor, cfnumber, bcfnumber, rxfax, conf, failover, failovernumber, remotecallforward, remotecallforwardnumber, blacklist, active, id from $table_name ORDER BY did";
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
		
		$did = $result[0];
		$dial = $result[1];
		$dialarg = $result[2];
		$cnam = $result[3]; # ME
		$cfe = $result[4];
		$bcfe = $result[5];
		$timelimit = $result[6];
		$cfringfor = $result[7];
		$cfnumber = $result[8];
		$bcfnumber = $result[9];
		$rxfax = $result[10];
		$conf = $result[11];
		$failover = $result[12];
		$failovernumber = $result[13];
		$rcf = $result[14];
		$rcfnumber = $result[15];
		$blacklist = $result[16]; #ME
		$active = $result[17];
		$did_id = $result[18];

		if (0 == length($dialarg)) {
			$dialarg = $result[0];
		}
		if ($dial =~ /IAX/) {
			$dial = "IAX2/".$dialarg."/".$did;
		} else {
			$dial = "SIP/".$dialarg;
		}

		if ($active == 0) {
			gen_out_of_service($did);
		} else {
			if ($rxfax == 1) {
				gen_fax($did);
			} elsif ($conf == 1) {
				gen_conf($did);
			} elsif ($rcf == 1) {
				gen_rcf($did, $rcfnumber);
			} else {
				$d = "Macro(agilevoice,";
				$d = $d.$dial.",";
				$d = $d.$did.",";
				if ($cfe == 1) {
					$d = $d.$cfnumber.",";
				} else {
					$d = $d.",";
				}
				$d = $d.$cfringfor.",";
				$d = $d.$timelimit.",";
				if ($bcfe == 1) {
					$d = $d.$bcfnumber.",";
				} else {
					$d = $d.",";
				}
				if ($failover == 1) {
					$d = $d.$failovernumber.",";
				}
				$d = $d.")";
				$pri = 1;
				if ($cnam == 1) {
					print EXTEN "exten => ".$did.",".$pri.",AGI(cnam)\n";
					$pri++;
				}
				print EXTEN "exten => ".$did.",".$pri.",".$d."\n";
				print EXTEN "exten => ".$result[0].",".($pri + 1).",Hangup\n";
				if ($blacklist == 1) {
					gen_blacklist($did_id,$did);
				}
			}
		}
	}
}

exit 0;

sub gen_blacklist
{
	my ($did_id, $did) = @_;

	$statement = "SELECT src, dst from $btable_name WHERE voip_did_id=$did_id ORDER BY src";
	my $result = $dbh->selectall_arrayref($statement);
	unless ($result) {
		print "dbh->selectall_arrayref($statement) failed!\n";
		print "DBI::err=[$DBI::err]\n";
		print "DBI::errstr=[$DBI::errstr]\n";
		exit;
	}
	my @resultset = @{$result};
	if ($#resultset > -1 ) {
		foreach $row (@{ $result }) {
			my @result = @{ $row };

			$src = $result[0];
			$dst = $result[1];
			$dst =~ s/ /(/;
			$dst =~ s/$/)/;
			print EXTEN "exten => ".$did."/".$src.",1,".$dst."\n";
			print EXTEN "exten => ".$did."/".$src.",2,Hangup\n";
		}
	}
}

sub gen_fax
{
	my ($did) = @_;

	print EXTEN "exten => ".$did.",1,SetVar(LOCALSTATIONID=\${CALLERIDNUM})\n";
	print EXTEN "exten => ".$did.",2,RxFax(/tmp/fax-%d.tif)\n";
	print EXTEN "exten => ".$did.",3,Hangup\n";
}

sub gen_conf
{
	my ($did) = @_;

	print EXTEN "exten => ".$did.",1,Meetme(".$did."|q)\n";
	print EXTEN "exten => ".$did.",2,Hangup\n";
}

sub gen_rcf
{
	my ($did, $rcfnumber) = @_;

	print EXTEN "exten => ".$did.",1,Dial(Local/".$rcfnumber."\@international/n)\n";
	print EXTEN "exten => ".$did.",2,Hangup\n";
}

sub gen_out_of_service
{
	my ($did) = @_;

	print EXTEN "exten => ".$did.",1,Playback(discon-or-out-of-service)\n";
	print EXTEN "exten => ".$did.",2,Hangup\n";
}

