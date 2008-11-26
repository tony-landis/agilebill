//******************************************************************************
// ------ Apycom.com Tree-menu Data --------------------------------------------
//******************************************************************************
var tlevelDX = 10;

var tblankImage      = "img/blank.gif";
var tfontStyle       = "normal 8pt Tahoma";
var tfontColor       = ["#3F3D3D","#7E7C7C"];
var tfontDecoration  = ["none","underline"];

var titemBackColor   = ["#F0F1F5","#F0F1F5"];
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
var tmenuHeight      = 500;

var titemTarget      = "_blank";
var ticonWidth       = 16;
var ticonHeight      = 16;
var ticonAlign       = "center";

var texpanded        = 1;
var texpandBtn       =["img/expandbtn2.gif","img/expandbtn2.gif","img/collapsebtn2.gif"];
var texpandBtnW      = 9;
var texpandBtnH      = 9;
var texpandBtnAlign  = "left"

var tpoints       = 0;
var tpointsImage  = "";
var tpointsVImage = "";
var tpointsCImage = "";

var tmoveable        = 1;
var tmoveImage       = "img/movepic.gif";
var tmoveImageHeight = 12;
var tabsolute        = 1;
var tleft            = 20;
var ttop             = 120;

var tfloatable       = 0;
var tfloatIterations = 6;

// XP-Style Parameters
var tXPStyle = 1;
var tXPIterations = 5;                  // expand/collapse speed
var tXPTitleTopBackColor = "";
var tXPTitleBackColor    = "#57C34A";
var tXPTitleLeft    = "img/xptitleleft_green.gif";
var tXPExpandBtn    = ["img/xpexpand1_green.gif","img/xpexpand1_green.gif","img/xpcollapse1_green.gif","img/xpcollapse1_green.gif"];
var tXPBtnHeight    = 25;
var tXPTitleBackImg = "img/xptitle_green.gif";

var tstyles =
[
    ["tfontStyle=bold 8pt Tahoma","tfontColor=#FFFFFF,#D2FCD5",
     "tfontDecoration=none,none"],
    ["tfontStyle=bold 8pt Tahoma","tfontColor=#3F3D3D,#659669",
     "tfontDecoration=none,none"],
    ["tfontDecoration=none,none"],
    ["tfontStyle=bold 8pt Tahoma","tfontColor=#444444,#5555FF"],
];

var tXPStyles =
[
    ["tXPTitleBackColor=#DEF3DB",
     "tXPExpandBtn=img/xpexpand2_green.gif,img/xpexpand2_green.gif,img/xpcollapse2_green.gif,img/xpcollapse2_green.gif", "tXPTitleBackImg=img/xptitle2_green.gif"]
];

var tmenuItems =
[
    ["1st submenu", "", "img/xpicon_green.gif","","", "Main Page",,"0"],
    ["|This menu has 7 items.<br> 3rd and 6th are hidden.", "", "img/icons/info.gif"],
    ["|Item 1", "testlink.htm", "img/icons/support.gif"],
    ["|Item 2", "testlink.htm", "img/icons/support.gif"],
    ["#|Item 3", "testlink.htm", "img/icons/support.gif"],
    ["|Item 4", "testlink.htm", "img/icons/support.gif"],
    ["|Item 5", "testlink.htm", "img/icons/support.gif"],
    ["#|Item 6", "testlink.htm", "img/icons/support.gif"],
    ["|Item 7", "testlink.htm", "img/icons/support.gif"],

    ["#2nd submenu",   "", "", "","","",,"1","0"],
    ["|This is the second submenu.<br>Also you can hide even a submenu title like here."],

    ["3rd submenu", "", "img/xpicon_green.gif","","", "Main Page",,"0"],
    ["|This submenu has 3 levels.<br>But 3rd level items are hidden.", "", "img/icons/info.gif"],
    ["|1st level item", "testlink.htm", "img/icons/support.gif"],
    ["||2nd level item", "testlink.htm", "img/icons/support.gif"],
    ["#||| 3rd level item 1", "testlink.htm", "img/icons/support.gif"],
    ["#||| 3rd level item 2", "testlink.htm", "img/icons/support.gif"],
    ["|1st level item", "testlink.htm", "img/icons/support.gif"],
    ["||2nd level item", "testlink.htm", "img/icons/support.gif"],
    ["#||| 3rd level item 1", "testlink.htm", "img/icons/support.gif"],
    ["#||| 3rd level item 2", "testlink.htm", "img/icons/support.gif"],
    ["#||| 3rd level item 3", "testlink.htm", "img/icons/support.gif"],
    ["#||| 3rd level item 4", "testlink.htm", "img/icons/support.gif"],
];

apy_tmenuInit();
