#!/usr/bin/perl
use strict;
use warnings;
use DBI;

my $dbh = DBI->connect("DBI:mysql:lrs",'root','');

die "failed to connect to MySQL database:DBI->errstr()" unless($dbh);

# prepare SQL statement
my $sth = $dbh->prepare("SELECT lastname, firstname, extension FROM employees")
                   or die "prepare statement failed: $dbh->errstr()";

$sth->execute() or die "execution failed: $dbh->errstr()"; 

my($lname,$fname,$ext);

# loop through each row of the result set, and print it
while(($lname,$fname,$ext) = $sth->fetchrow()){
   print("$lname, $fname\t$ext\n");                   
}

$sth->finish();
$dbh->disconnect();