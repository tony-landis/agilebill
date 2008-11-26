//******************************************************************************
// ------ Apycom.com Tree-menu Data --------------------------------------------
//******************************************************************************
var tblankImage      = "img/blank.gif";
var tfontStyle       = "normal 8pt Tahoma";
var tfontColor       = ["#000000","#000088"];
var tfontDecoration  = ["none","underline"];

var titemBackColor   = ["#CCCCCC","#CCCCCC"];
var titemAlign       = "left";
var titemBackImage   = ["",""];
var titemCursor      = "default";
var titemHeight      = 20;

var tmenuBackImage   = "";
var tmenuBackColor   = "";
var tmenuBorderColor = "#FFFFFF";
var tmenuBorderStyle = "dotted";
var tmenuBorderWidth = 1;
var tmenuWidth       = "";
var tmenuHeight      = "";

var titemTarget      = "_blank";
var ticonWidth       = 16;
var ticonHeight      = 16;
var ticonAlign       = "left";

var texpandBtn       =["img/expandbtn2.gif","img/expandbtn2.gif","img/collapsebtn2.gif"];
var texpandBtnW      = 9;
var texpandBtnH      = 9;
var texpandBtnAlign  = "left";

var tpoints       = 1;
var tpointsImage  = "img/vpoint.gif";
var tpointsVImage = "img/hpoint.gif";
var tpointsCImage = "img/cpoint.gif";

var tmoveable        = 0;
var tmoveImage       = "img/movepic.gif";
var tabsolute        = 0;
var tleft            = 20;
var ttop             = 120;

var tfloatable       = 0;
var tfloatIterations = 6;

// XP-Style Parameters
var tXPStyle = 0;
var tXPIterations = 0;                  // expand/collapse speed
var tXPTitleTopBackColor = "";
var tXPTitleBackColor    = "";
var tXPTitleLeft    = "";
var tXPExpandBtn    = [];
var tXPBtnHeight    = 0;
var tXPTitleBackImg = "";

var tstyles =
[

];

var tmenuItems =
[
    ["Item 1", "testlink.htm", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 4", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 4", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 4", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 4", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["|||SubItem 1","", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 5", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 4", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 6", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 1", "testlink.htm", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 4", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 4", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 4", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 4", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["|||SubItem 1","", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 5", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
      ["|SubItem 4", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 1", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 2", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
        ["||SubItem 3", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
    ["Item 6", "", "img/icons/folder1.gif","img/icons/folder1.gif","img/icons/folder2.gif"],
];


