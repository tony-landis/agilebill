// Author: Hartwig Weinkauf h_weinkauf@gmx.de
// Feel free to use / redistribute under the GNU LGPL.
// ** I18N

// full day names
Calendar._DN = new Array
("Sonntag",
 "Montag",
 "Dienstag",
 "Mittwoch",
 "Donnerstag",
 "Freitag",
 "Samstag",
 "Sonntag");

// short day names only use 2 letters instead of 3
Calendar._SDN_len = 2;

// full month names
Calendar._MN = new Array
("Januar",
 "Februar",
 "Maerz",
 "April",
 "Mai",
 "Juni",
 "Juli",
 "August",
 "September",
 "Oktober",
 "November",
 "Dezember");

// tooltips
Calendar._TT = {};
Calendar._TT["TOGGLE"] = "Ersten Tag der Woche waehlen";
Calendar._TT["PREV_YEAR"] = "Vorheriges Jahr (gedrueckt halten fuer Auswahlmenue)";
Calendar._TT["PREV_MONTH"] = "Vorheriger Monat (gedrueckt halten fuer Auswahlmenue)";
Calendar._TT["GO_TODAY"] = "Gehe zum heutigen Datum";
Calendar._TT["NEXT_MONTH"] = "Folgender Monat (gedrueckt halten fuer Auswahlmenue)";
Calendar._TT["NEXT_YEAR"] = "Folgendes Jahr (gedrueckt halten fuer Auswahlmenue)";
Calendar._TT["SEL_DATE"] = "Datum auswaehlen";
Calendar._TT["DRAG_TO_MOVE"] = "Klicken und gedrueckt halten um zu verschieben";
Calendar._TT["PART_TODAY"] = " (heute)";
Calendar._TT["MON_FIRST"] = "Wochenanzeige mit Montag beginnen";
Calendar._TT["SUN_FIRST"] = "Wochenanzeige mit Sonntag beginnen";
Calendar._TT["CLOSE"] = "Schliessen";
Calendar._TT["TODAY"] = "Heute";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "dd-mm-y";
Calendar._TT["TT_DATE_FORMAT"] = "DD, d MM";

Calendar._TT["WK"] = "KW";
