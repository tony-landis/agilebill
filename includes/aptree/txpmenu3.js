//******************************************************************************
// ------ Apycom.com Tree-menu Data --------------------------------------------
//******************************************************************************
var tblankImage      = "img/blank.gif";
var tfontStyle       = "normal 8pt Tahoma";
var tfontColor       = ["#3F3D3D","#7E7C7C"];
var tfontDecoration  = ["none","underline"];

var titemBackColor   = ["#F6F6EC","#F6F6EC"];
var titemAlign       = "left";
var titemBackImage   = ["",""];
var titemCursor      = "hand";
var titemHeight      = 22;

var tmenuBackImage   = "";
var tmenuBackColor   = "";
var tmenuBorderColor = "#FFFFFF";
var tmenuBorderStyle = "solid";
var tmenuBorderWidth = 0;
var tmenuWidth       = 230;
var tmenuHeight      = 400;

var titemTarget      = "_blank";
var ticonWidth       = 16;
var ticonHeight      = 16;
var ticonAlign       = "left";

var texpandBtn       =["img/expandbtn2.gif","img/expandbtn2.gif","img/collapsebtn2.gif"];
var texpandBtnW      = 9;
var texpandBtnH      = 9;
var texpandBtnAlign  = "left"

var texpanded = 0;

var tpoints       = 0;
var tpointsImage  = "";
var tpointsVImage = "";
var tpointsCImage = "";

var tabsolute        = 1;
var tmoveable        = 0;
var tmoveImage       = "img/movepic.gif";
var tleft            = 20;
var ttop             = 120;

var tfloatable       = 1;
var tfloatIterations = 6;

// XP-Style Parameters
var tXPStyle = 1;
var tXPIterations = 10;                  // expand/collapse speed
var tXPTitleTopBackColor = "";
var tXPTitleBackColor    = "#94A664";
var tXPTitleLeft    = "img/xptitleleft_o.gif";
var tXPExpandBtn    = ["img/xpexpand1_o.gif","img/xpexpand2_o.gif","img/xpcollapse1_o.gif","img/xpcollapse2_o.gif"];
var tXPBtnHeight    = 23;
var tXPTitleBackImg = "img/xptitle_o.gif";

var tstyles =
[
    ["tfontStyle=bold 8pt Tahoma","tfontColor=#FFFFFF,#E0E7B8",
     "tfontDecoration=none,none"],
    ["tfontStyle=bold 8pt Tahoma","tfontColor=#56662D,#72921D",
     "tfontDecoration=none,none"],
    ["tfontDecoration=none,none"],
    ["tfontStyle=bold 8pt Tahoma","tfontColor=#444444,#5555FF"],
];

var tXPStyles =
[
    ["tXPTitleBackColor=#E2E9BC",
     "tXPExpandBtn=img/xpexpand3_o.gif,img/xpexpand4_o.gif,img/xpcollapse3_o.gif,img/xpcollapse4_o.gif", "tXPTitleBackImg=img/xptitle2_o.gif"]
];

var tmenuItems =
[
    ["+XP-style Title with Icon", "",             "img/xpicon1_o.gif",      "", "", "Main Page",,"0"],
    ["|Information", "testlink.htm",              "img/icons/info.gif",     "", "", "Information","_blank"],
    ["|Support",     "",                          "img/icons/support.gif",  "", "", "Support",, "2"],
    ["||Contacts",   "mailto:support@apycom.com", "img/icons/contacts.gif", "", "", "Contacts"],
    ["||E-mail",     "mailto:support@apycom.com", "img/icons/email.gif",    "", "", "E-mail"],

    ["|Help",       "",             "img/icons/help1.gif", "img/icons/help1.gif", "img/icons/help2.gif", "Help",,"2"],
    ["||Glossary",  "testlink.htm", "img/icons/paper.gif", "",                    "",                    "Glossary"],
    ["||Index",     "testlink.htm", "img/icons/paper.gif", "",                    "",                    "Index"],
    ["||<nobr><input value='search' size=10 style='font-size:10'>&nbsp;<input type='button' value='Search' style='font-size:10'></nobr>", "", "img/icons/search.gif", "", "", "Search",,"2"],

    ["||<nobr>Contents:&nbsp;<select  style='font-size:10'><option>Item 1</option><option>Item 2</option><option>Item 3</option></select></nobr>", "", "img/icons/list.gif", "", "", "Contents",,"2"],

    ["+XP-style Title without Icon",             "", "", "", "", "Download software",,"1","0"],
    ["|Item without icon",          "testlink.htm", "", "", "", "Item 1 Hint"],
    ["|Item with individual style",             "", "", "", "", "Item 2 Hint",,"3"],
    ["||SubItem 1",    "testlink.htm", "img/icons/help1.gif", "", "", "SubItem 1 Hint"],
    ["||SubItem 2",    "testlink.htm", "img/icons/help1.gif", "", "", "SubItem 1 Hint"],
    ["|||SubItem 2_1", "testlink.htm",                    "", "", "", "SubItem 1_2 Hint"],

];

