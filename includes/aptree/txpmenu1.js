var tabsolute = 1;
var tLeft = 10;
var tTop = 10;

//******************************************************************************
// ------ Apycom.com Tree-menu Data --------------------------------------------
//******************************************************************************
var tblankImage      = "includes/aptree/img/blank.gif";
var tmenuWidth       = 230;
var tmenuHeight      = 400;

var tabsolute        = 1;
var tleft            = 20;
var ttop             = 120;

var tfloatable       = 1;
var tfloatIterations = 6;
var tmoveable        = 0;
var tmoveImage       = "includes/aptree/img/movepic.gif";
var tmoveImageHeight = 12;

var tfontStyle       = "normal 8pt Tahoma";
var tfontColor       = ["#215DC6","#428EFF"];
var tfontDecoration  = ["none","underline"];

var titemBackColor   = ["#D6DFF7","#D6DFF7"];
var titemAlign       = "left";
var titemBackImage   = ["",""];
var titemCursor      = "hand";
var titemHeight      = 22;

var titemTarget      = "_blank";
var ticonWidth       = 16;
var ticonHeight      = 16;
var ticonAlign       = "left";

var tmenuBackImage   = "";
var tmenuBackColor   = "";
var tmenuBorderColor = "#FFFFFF";
var tmenuBorderStyle = "solid";
var tmenuBorderWidth = 0;

var texpandBtn       =["includes/aptree/img/expandbtn2.gif","includes/aptree/img/expandbtn2.gif","includes/aptree/img/collapsebtn2.gif"];
var texpandBtnW      = 9;
var texpandBtnH      = 9;
var texpandBtnAlign  = "left"

var texpanded = 1;

var tpoints       = 0;
var tpointsImage  = "";
var tpointsVImage = "";
var tpointsCImage = "";

// XP-Style Parameters
var tXPStyle = 1;
var tXPIterations = 5;                  // expand/collapse speed
var tXPTitleTopBackColor = "";
var tXPTitleBackColor    = "#265BCC";
var tXPTitleLeft    = "img/xptitleleft.gif";
var tXPExpandBtn    = ["img/xpexpand1.gif","img/xpexpand2.gif","img/xpcollapse1.gif","img/xpcollapse2.gif"];
var tXPBtnHeight    = 25;
var tXPTitleBackImg = "img/xptitle.gif";

var tstyles =
[
    ["tfontStyle=bold 8pt Tahoma","titemBackColor=#265BCC,#265BCC","tfontColor=#FFFFFF,#428EFF", "tfontDecoration=none,none"],
    ["tfontStyle=bold 8pt Tahoma","titemBackColor=#265BCC,#265BCC","tfontColor=#215DC6,#428EFF", "tfontDecoration=none,none"],
    ["tfontDecoration=none,none"],
    ["tfontStyle=bold 8pt Tahoma","tfontColor=#444444,#5555FF"],
];

var tXPStyles =
[
    ["tXPTitleBackColor=#D0DAF8", "tXPExpandBtn=img/xpexpand3.gif,img/xpexpand4.gif,img/xpcollapse3.gif,img/xpcollapse4.gif", "tXPTitleBackImg=img/xptitle2.gif"]
];

var tmenuItems =
[
    ["XP-style Title with Icon", "", "img/xpicon1.gif","","", "Main Page",,"0"],
    ["|Information", "testlink.htm", "img/icons/info.gif", "",  "", "Information","_blank"],
    ["|Support",     "",             "img/icons/support.gif",   "", "", "Support",, "2"],
    ["||Contacts",   "mailto:support@apycom.com", "img/icons/contacts.gif", "", "", "Contacts"],
    ["||E-mail",     "mailto:support@apycom.com", "img/icons/email.gif", "", "", "E-mail"],

    ["|Help",       "",              "img/icons/help1.gif", "img/icons/help1.gif", "img/icons/help2.gif", "Help",,"2"],
    ["||Glossary",  "testlink.htm", "img/icons/paper.gif", "",                     "",                   "Glossary"],
    ["||Index",     "testlink.htm", "img/icons/paper.gif", "",                     "",                   "Index"],
    ["||<nobr><input value='search' size=10 style='font-size:10'>&nbsp;<input type='button' value='Search' style='font-size:10'></nobr>",
     "", "img/icons/search.gif", "", "", "Search",,"2"],
     
    ["||<nobr>Contents:&nbsp;<select  style='font-size:10'><option>Item 1</option><option>Item 2</option><option>Item 3</option></select></nobr>",
     "", "img/icons/list.gif", "", "", "Contents",,"2"],

    ["XP-style Title without Icon",   "", "", "","","Download software",,"1","0"],
    ["|Item without icon",          "testlink.htm", ,,, "Item 1 Hint"],
    ["|Item with individual style", "",             ,,, "Item 2 Hint",,"3"],
    ["||SubItem 1",    "testlink.htm", "img/icons/help1.gif", "","", "SubItem 1 Hint"],
    ["||SubItem 2",    "testlink.htm", "img/icons/help1.gif", "","", "SubItem 1 Hint"],
    ["|||SubItem 2_1", "testlink.htm", ,,, "SubItem 1_2 Hint"],

];

apy_tmenuInit();
