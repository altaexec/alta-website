/***********************************************************************************
*	(c) Ger Versluis 2000 version 5.411 24 December 2001 (updated Jan 31st, 2003 by Dynamic Drive for Opera7)
*	For info write to menus@burmees.nl		          *
*	You may remove all comments for faster loading	          *		
***********************************************************************************/

	var NoOffFirstLineMenus=7;			// Number of first level items
	var LowBgColor='white';			// Background color when mouse is not over
	var LowSubBgColor='white';			// Background color when mouse is not over on subs
	var HighBgColor='#333333';			// Background color when mouse is over
	var HighSubBgColor='#333333';			// Background color when mouse is over on subs
	var FontLowColor='black';			// Font color when mouse is not over
	var FontSubLowColor='black';			// Font color subs when mouse is not over
	var FontHighColor='white';			// Font color when mouse is over
	var FontSubHighColor='white';			// Font color subs when mouse is over
	var BorderColor='black';			// Border color
	var BorderSubColor='black';			// Border color for subs
	var BorderWidth=1;				// Border width
	var BorderBtwnElmnts=1;			// Border between elements 1 or 0
	var FontFamily="arial,comic sans ms,technical"	// Font family menu items
	var FontSize=9;				// Font size menu items
	var FontBold=1;				// Bold menu items 1 or 0
	var FontItalic=0;				// Italic menu items 1 or 0
	var MenuTextCentered='left';			// Item text position 'left', 'center' or 'right'
	var MenuCentered='left';			// Menu horizontal position 'left', 'center' or 'right'
	var MenuVerticalCentered='static';		// Menu vertical position 'top', 'middle','bottom' or static
	var ChildOverlap=.2;				// horizontal overlap child/ parent
	var ChildVerticalOverlap=.2;			// vertical overlap child/ parent
	var StartTop=0;				// Menu offset x coordinate
	var StartLeft=10;				// Menu offset y coordinate
	var VerCorrect=0;				// Multiple frames y correction
	var HorCorrect=0;				// Multiple frames x correction
	var LeftPaddng=3;				// Left padding
	var TopPaddng=2;				// Top padding
	var FirstLineHorizontal=1;			// SET TO 1 FOR HORIZONTAL MENU, 0 FOR VERTICAL
	var MenuFramesVertical=1;			// Frames in cols or rows 1 or 0
	var DissapearDelay=200;			// delay before menu folds in
	var TakeOverBgColor=1;			// Menu frame takes over background color subitem frame
	var FirstLineFrame='navig';			// Frame where first level appears
	var SecLineFrame='space';			// Frame where sub levels appear
	var DocTargetFrame='space';			// Frame where target documents appear
	var TargetLoc='';				// span id for relative positioning
	var HideTop=0;				// Hide first level when loading new document 1 or 0
	var MenuWrap=1;				// enables/ disables menu wrap 1 or 0
	var RightToLeft=0;				// enables/ disables right to left unfold 1 or 0
	var UnfoldsOnClick=1;			// Level 1 unfolds onclick/ onmouseover
	var WebMasterCheck=0;			// menu tree checking on or off 1 or 0
	var ShowArrow=1;				// Uses arrow gifs when 1
	var KeepHilite=1;				// Keep selected path highligthed
	var Arrws=['../images/tri.gif',5,10,'../images/tridown.gif',10,5,'../images/trileft.gif',5,10];	// Arrow source, width and height

function BeforeStart(){return}
function AfterBuild(){return}
function BeforeFirstOpen(){return}
function AfterCloseAll(){return}


// Menu tree
//	MenuX=new Array(Text to show, Link, background image (optional), number of sub elements, height, width);
//	For rollover images set "Text to show" to:  "rollover:Image1.jpg:Image2.jpg"
Menu1=new Array("Home","admin_home.php","",0,20,60);

Menu2=new Array("Conference","#","",5,20,110);
	Menu2_1=new Array("Conference Info","view_conference_info.php","",0,20,150);
	Menu2_2=new Array("Edit Conference Info","edit_conference_info.php","",0);
	Menu2_3=new Array("View Phases","view_phases.php","",0);
	Menu2_4=new Array("Change Current Phase","change_phase.php","",0);	
	Menu2_5=new Array("Edit Phases","edit_phases.php","",0);

Menu3=new Array("Papers","#","",4,20,110);
	Menu3_1=new Array("View Papers","view_all_papers.php","",0,20,150);	
	Menu3_2=new Array("View Withdrawn Papers","view_withdrawn_papers.php","",0);
	Menu3_3=new Array("View Paper Tracks","view_tracks.php","",0);
	Menu3_4=new Array("View Paper Topics","view_categories.php","",0);

Menu4=new Array("User Admin","#","",8,20,110);
	Menu4_1=new Array("View All Users","view_all_users.php","",0,20,150);
	Menu4_2=new Array("View All Reviewers","view_all_reviewers.php","",0);	
	Menu4_3=new Array("Setup Reviewer Account","setup_new_account.php?accountType=Reviewer","",0);
	Menu4_4=new Array("Setup Admin Account","setup_new_account.php?accountType=Administrator","",0);
	Menu4_5=new Array("Change Admin Password","change_pwd.php","",0);
	Menu4_6=new Array("Login to Another Account","su.php","",0);
	Menu4_7=new Array("Print Name Tags","name_tags_preview.php","",0);
	Menu4_8=new Array("View Letters","view_letters.php","",0);

Menu5=new Array("Program","#","",4,20,110);
	Menu5_1=new Array("Rooms","rooms.php","",0,20,150);
	Menu5_2=new Array("Presentation Types","presentation_types.php","",0);
	Menu5_3=new Array("Sessions","sessions.php","",0);
	Menu5_4=new Array("Program","program.php","",0);
	// Menu5_2=new Array("Bulk Email","bulk_email.php","",0);		

//Menu6=new Array("Registration","#","",2,20,110);
//	Menu6_1=new Array("Verify Form Payment","form_payment.php","",0,20,150);
//	Menu6_2=new Array("Registration Statistics","payment_statistics.php","",0);

Menu6=new Array("General Admin","#","",4,20,110);
	Menu6_1=new Array("Change Settings","general_settings.php","",0,20,150);
	Menu6_2=new Array("Import/Export Settings","import_export_settings.php","",0);
	Menu6_3=new Array("Extract All Papers","extract_papers.php","",0);	
	Menu6_4=new Array("Build CD Structure","build_cd_structure.php","",0);
//Menu7=new Array("Database","#","",0);
	// Menu7_1=new Array("Explore Database","dataman.php","",0,20,150);
	// Menu7_1=new Array("Backup Database","backup_database.php","",0);
	// Menu7_2=new Array("Restore Database","restore_database.php","",0);
	
Menu7=new Array("Logout","../logout.php","",0,20,60);