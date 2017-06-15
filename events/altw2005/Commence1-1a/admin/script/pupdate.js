/* PopUp Calendar v2.1
© PCI, Inc.,2000 • Freeware
webmaster@personal-connections.com
+1 (925) 955 1624
Permission granted  for unlimited use so far
as the copyright notice above remains intact. */

/* Settings. Please read readme.html file for instructions*/
var ppcDF = "d/m/Y";
var ppcMN = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
var ppcWN = new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
var ppcER = new Array(4);
ppcER[0] = "Required DHTML functions are not supported in this browser.";
ppcER[1] = "Target form field is not assigned or not accessible.";
ppcER[2] = "Sorry, the chosen date is not acceptable. Please read instructions on the page.";
ppcER[3] = "Unknown error occured while executing this script.";
var ppcUC = false;
 var ppcUX = 4;
 var ppcUY = 4;

/* Do not edit below this line unless you are sure what are you doing! */

var ppcIE=(navigator.appName == "Microsoft Internet Explorer");
var ppcNN=((navigator.appName == "Netscape")&&(document.layers));
var ppcTT="<table width=\"200\" cellspacing=\"1\" cellpadding=\"2\" border=\"1\" bordercolorlight=\"#000000\" bordercolordark=\"#000000\">\n";
var ppcCD=ppcTT;var ppcFT="<font face=\"MS Sans Serif, sans-serif\" size=\"1\" color=\"#000000\">";var ppcFC=true;
var ppcTI=false;var ppcSV=null;var ppcRL=null;var ppcXC=null;var ppcYC=null;
var ppcML=new Array(31,28,31,30,31,30,31,31,30,31,30,31);
var ppcWE=new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
var ppcNow=new Date();var ppcPtr=new Date();
if (ppcNN) {
 window.captureEvents(Event.RESIZE);
 window.onresize = restoreLayers;
 document.captureEvents(Event.MOUSEDOWN|Event.MOUSEUP);
 document.onmousedown = recordXY;
 document.onmouseup = confirmXY;}
function restoreLayers(e) {
 if (ppcNN) {
  with (window.document) {
   open("text/html");
   write("<html><head><title>Restoring the layer structure...</title></head>");
   write("<body bgcolor=\"#FFFFFF\" onLoad=\"history.go(-1)\">");
   write("</body></html>");
   close();}}}
function recordXY(e) {
 if (ppcNN) {
  ppcXC = e.x;
  ppcYC = e.y;
  document.routeEvent(e);}}
function confirmXY(e) {
 if (ppcNN) {
  ppcXC = (ppcXC == e.x) ? e.x : null;
  ppcYC = (ppcYC == e.y) ? e.y : null;
  document.routeEvent(e);}}
function getCalendarFor(target,rules) {
 ppcSV = target;
 ppcRL = rules;
 if (ppcFC) {setCalendar();ppcFC = false;}
 if ((ppcSV != null)&&(ppcSV)) {
  if (ppcIE) {
   var obj = document.all['PopUpCalendar'];
   obj.style.left = document.body.scrollLeft+event.clientX;
   obj.style.top  = document.body.scrollTop+event.clientY;
   obj.style.visibility = "visible";}
  else if (ppcNN) {
   var obj = document.layers['PopUpCalendar'];
   obj.left = ppcXC
   obj.top  = ppcYC
   obj.visibility = "show";}
  else {showError(ppcER[0]);}}
 else {showError(ppcER[1]);}}
function switchMonth(param) {
 var tmp = param.split("|");
 setCalendar(tmp[0],tmp[1]);}
function moveMonth(dir) {
 var obj = null;
 var limit = false;
 var tmp,dptrYear,dptrMonth;
 if (ppcIE) {obj = document.ppcMonthList.sItem;}
 else if (ppcNN) {obj = document.layers['PopUpCalendar'].document.layers['monthSelector'].document.ppcMonthList.sItem;}
 else {showError(ppcER[0]);}
 if (obj != null) {
  if ((dir.toLowerCase() == "back")&&(obj.selectedIndex > 0)) {obj.selectedIndex--;}
  else if ((dir.toLowerCase() == "forward")&&(obj.selectedIndex < 12)) {obj.selectedIndex++;}
  else {limit = true;}}
 if (!limit) {
  tmp = obj.options[obj.selectedIndex].value.split("|");
  dptrYear  = tmp[0];
  dptrMonth = tmp[1];
  setCalendar(dptrYear,dptrMonth);}
 else {
  if (ppcIE) {
   obj.style.backgroundColor = "#FF0000";
   window.setTimeout("document.ppcMonthList.sItem.style.backgroundColor = '#FFFFFF'",50);}}}
function selectDate(param) {
 var arr   = param.split("|");
 var year  = arr[0];
 var month = arr[1];
 var date  = arr[2];
 var ptr = parseInt(date);
 ppcPtr.setDate(ptr);
 if ((ppcSV != null)&&(ppcSV)) {
  if (validDate(date)) {ppcSV.value = dateFormat(year,month,date);hideCalendar();}
  else {showError(ppcER[2]);if (ppcTI) {clearTimeout(ppcTI);ppcTI = false;}}}
 else {
  showError(ppcER[1]);
  hideCalendar();}}
function setCalendar(year,month) {
 if (year  == null) {year = getFullYear(ppcNow);}
 if (month == null) {month = ppcNow.getMonth();setSelectList(year,month);}
 if (month == 1) {ppcML[1]  = (isLeap(year)) ? 29 : 28;}
 ppcPtr.setYear(year);
 ppcPtr.setMonth(month);
 ppcPtr.setDate(1);
 updateContent();}
function updateContent() {
 generateContent();
 if (ppcIE) {document.all['monthDays'].innerHTML = ppcCD;}
 else if (ppcNN) {
  with (document.layers['PopUpCalendar'].document.layers['monthDays'].document) {
   open("text/html");
   write("<html>\n<head>\n<title>DynDoc</title>\n</head>\n<body bgcolor=\"#FFFFFF\">\n");
   write(ppcCD);
   write("</body>\n</html>");
   close();}}
 else {showError(ppcER[0]);}
 ppcCD = ppcTT;}
function generateContent() {
 var year  = getFullYear(ppcPtr);
 var month = ppcPtr.getMonth();
 var date  = 1;
 var day   = ppcPtr.getDay();
 var len   = ppcML[month];
 var bgr,cnt,tmp = "";
 var j,i = 0;
 for (j = 0; j < 7; ++j) {
  if (date > len) {break;}
  for (i = 0; i < 7; ++i) {
   bgr = ((i == 0)||(i == 6)) ? "#FFFFCC" : "#FFFFFF";
   if (((j == 0)&&(i < day))||(date > len)) {tmp  += makeCell(bgr,year,month,0);}
   else {tmp  += makeCell(bgr,year,month,date);++date;}}
  ppcCD += "<tr align=\"center\">\n" + tmp + "</tr>\n";tmp = "";}
 ppcCD += "</table>\n";}
function makeCell(bgr,year,month,date) {
 var param = "\'"+year+"|"+month+"|"+date+"\'";
 var td1 = "<td width=\"20\" bgcolor=\""+bgr+"\" ";
 var td2 = (ppcIE) ? "</font></span></td>\n" : "</font></a></td>\n";
 var evt = "onMouseOver=\"this.style.backgroundColor=\'#FF0000\'\" onMouseOut=\"this.style.backgroundColor=\'"+bgr+"\'\" onMouseUp=\"selectDate("+param+")\" ";
 var ext = "<span Style=\"cursor: hand\">";
 var lck = "<span Style=\"cursor: default\">";
 var lnk = "<a href=\"javascript:selectDate("+param+")\" onMouseOver=\"window.status=\' \';return true;\">";
 var cellValue = (date != 0) ? date+"" : "&nbsp;";
 if ((ppcNow.getDate() == date)&&(ppcNow.getMonth() == month)&&(getFullYear(ppcNow) == year)) {
  cellValue = "<b>"+cellValue+"</b>";}
 var cellCode = "";
 if (date == 0) {
  if (ppcIE) {cellCode = td1+"Style=\"cursor: default\">"+lck+ppcFT+cellValue+td2;}
  else {cellCode = td1+">"+ppcFT+cellValue+td2;}}
 else {
  if (ppcIE) {cellCode = td1+evt+"Style=\"cursor: hand\">"+ext+ppcFT+cellValue+td2;}
  else {
   if (date < 10) {cellValue = "&nbsp;" + cellValue + "&nbsp;";}
   cellCode = td1+">"+lnk+ppcFT+cellValue+td2;}}
 return cellCode;}
function setSelectList(year,month) {
 var i = 0;
 var obj = null;
 if (ppcIE) {obj = document.ppcMonthList.sItem;}
 else if (ppcNN) {obj = document.layers['PopUpCalendar'].document.layers['monthSelector'].document.ppcMonthList.sItem;}
 else {/* NOP */}
 while (i < 13) {
  obj.options[i].value = year + "|" + month;
  obj.options[i].text  = year + " • " + ppcMN[month];
  i++;
  month++;
  if (month == 12) {year++;month = 0;}}}
function hideCalendar() {
 if (ppcIE) {document.all['PopUpCalendar'].style.visibility = "hidden";}
 else if (ppcNN) {document.layers['PopUpCalendar'].visibility = "hide";window.status = " ";}
 else {/* NOP */}
 ppcTI = false;
 setCalendar();
 ppcSV = null;
 if (ppcIE) {var obj = document.ppcMonthList.sItem;}
 else if (ppcNN) {var obj = document.layers['PopUpCalendar'].document.layers['monthSelector'].document.ppcMonthList.sItem;}
 else {/* NOP */}
 obj.selectedIndex = 0;}
function showError(message) {
 window.alert("[ PopUp Calendar ]\n\n" + message);}
function isLeap(year) {
 if ((year%400==0)||((year%4==0)&&(year%100!=0))) {return true;}
 else {return false;}}
function getFullYear(obj) {
 if (ppcNN) {return obj.getYear() + 1900;}
 else {return obj.getYear();}}
function validDate(date) {
 var reply = true;
 if (ppcRL == null) {/* NOP */}
 else {
  var arr = ppcRL.split(":");
  var mode = arr[0];
  var arg  = arr[1];
  var key  = arr[2].charAt(0).toLowerCase();
  if (key != "d") {
   var day = ppcPtr.getDay();
   var orn = isEvenOrOdd(date);
   reply = (mode == "[^]") ? !((day == arg)&&((orn == key)||(key == "a"))) : ((day == arg)&&((orn == key)||(key == "a")));}
  else {reply = (mode == "[^]") ? (date != arg) : (date == arg);}}
 return reply;}
function isEvenOrOdd(date) {
 if (date - 21 > 0) {return "e";}
 else if (date - 14 > 0) {return "o";}
 else if (date - 7 > 0) {return "e";}
 else {return "o";}}
function dateFormat(year,month,date) {
 if (ppcDF == null) {ppcDF = "m/d/Y";}
 var day = ppcPtr.getDay();
 var crt = "";
 var str = "";
 var chars = ppcDF.length;
 for (var i = 0; i < chars; ++i) {
  crt = ppcDF.charAt(i);
  switch (crt) {
   case "M": str += ppcMN[month]; break;
   case "m": str += (month<9) ? ("0"+(++month)) : ++month; break;
   case "Y": str += year; break;
   case "y": str += year.substring(2); break;
   case "d": str += ((ppcDF.indexOf("m")!=-1)&&(date<10)) ? ("0"+date) : date; break;
   case "W": str += ppcWN[day]; break;
    default: str += crt;}}
 return unescape(str);}