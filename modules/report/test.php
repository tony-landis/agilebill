<?php
/*
AgileReports
(C) 2005-2006 Thralling Penguin LLC in association with Agileco LLC. All rights reserved.

*/

include "../../config.inc.php";
include PATH_INCLUDES."adodb/adodb.inc.php";
include PATH_MODULES."core/database.inc.php";

/*
	There are features still missing! But it's not a shabby start.
*/


# these three files make up the reporting system
include 'class.Report.php';
include 'class.Level.php';
include 'class.ReportParser.php';

/* Good idea, reports can be a beast */
set_time_limit(0);

/* Uncomment one of the output formater lines below */
#$f = new TXT_ReportFormatter;
$f = new HTML_ReportFormatter;
#$f = new PDF_ReportFormatter;

# Tell the formatter where to save the output
$dir = tempnam(PATH_FILES, "s");
@unlink($dir);
mkdir($dir, 0775);
$f->setOutputDirectory($dir);

# This creates the report class, specifying the ReportFormatter to use and whether or not to paginate the title
$r = new Reporting($f, true);
# This creates the report XML parser, specify the report class object to use in building the report
$p = new ReportParser($r);
# This sets the XML report definition file
#$result = $p->setInputFile(PATH_MODULES.'report/year_month_sales_by_sku.xml');
$result = $p->setInputFile(PATH_AGILE.'reports/invoice/sales_report.xml');
# set criteria
$p->setUserCriteria('yearmonth','>=',mktime(0,0,0,1,1,2005));
# Parse that puppy!
$result = $p->parse();

/* COULD INSERT CODE TO DO SMARTY JUNK HERE - then skip the display call */
/* COULD ALSO call back into $p to assign some SQL statement criteria changes from the UI/Smarty's POST/GET */

# Render my report, now!
$r->display();

echo $f->getOutput();

?>