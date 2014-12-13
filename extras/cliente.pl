#!/usr/bin/perl

use URI::Escape;
use LWP::Simple qw(get);
use JSON::Any;

my $url = shift || die "Sin URL me duele!\n";

my $slugr = "http://sl.ugr.es/sluger.php?esp=JASON\&modo=new&url=".uri_escape( $url ) ;
my $json =  get($slugr) || die "Algun errorcillo";

my $j = JSON::Any->new;
my $obj = $j->Load($json);
print "URL obtenida: http://".$obj->{'url'}."\n";
