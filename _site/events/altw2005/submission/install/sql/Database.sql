CREATE TABLE Author 
(
	AuthorID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Author ID */
	FirstName VARCHAR(30) NOT NULL , /* First name of Author */
	MiddleName VARCHAR(30) NOT NULL , /* Middle name of Author */
	LastName VARCHAR(30) NOT NULL , /* Last name of Author */
	Email VARCHAR(50) /* E-mail of the author, may be NULL as Author may not have an e-mail */	
)
;

CREATE TABLE Written
(
	PaperID INT UNSIGNED NOT NULL , /* Paper ID of submitted paper */
	AuthorID INT UNSIGNED NOT NULL , /* Author who wrote this submitted paper */
	PRIMARY KEY ( PaperID , AuthorID ) /* Composite keys */	
)
;

CREATE TABLE AuthorLevel
(
	AuthorLevelID TINYINT UNSIGNED UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Level ID of the author of that paper */
	AuthorLevelName VARCHAR(30) NOT NULL /* Author Level Name, High / Medium / Low / etc... */
)
;

INSERT INTO AuthorLevel ( AuthorLevelName )
VALUES ( 'Main Author' ) ;
INSERT INTO AuthorLevel ( AuthorLevelName )
VALUES ( 'Co Author' ) ;

CREATE TABLE Review
(
	PaperID INT UNSIGNED NOT NULL , /* Paper ID of submitted paper */
	MemberName VARCHAR(50) NOT NULL , /* User name of reviewer */
	Objectives TINYINT UNSIGNED DEFAULT 0, /* The paper fits in with
the objectives of this workshop */
	Validity TINYINT UNSIGNED DEFAULT 0, /* All reported claims and
results are correct and valid, and there are no technical and/or
methodological flaws*/	
	Innovativeness TINYINT UNSIGNED DEFAULT 0, /* The paper is
innovative and makes a genuine contribution to the field */
	Presentation TINYINT UNSIGNED DEFAULT 0, /* The objectives,
methodology and contributions of the paper are clearly described*/	
	Bibliography TINYINT UNSIGNED DEFAULT 0, /* The bibliography is
relevant and exhaustive */		
	AcceptPaper TINYINT UNSIGNED DEFAULT 0, /* The paper should be
accepted as a regular paper */
	AcceptPoster TINYINT UNSIGNED DEFAULT 0, /* The paper should be
accepted as a poster */
	Comments TEXT, /* Comments of this Paper given by Reviewer */
	PRIMARY KEY ( PaperID , MemberName ) /* Composite keys */
)
;

CREATE TABLE Selection
(
	PaperID INT UNSIGNED NOT NULL , /* Paper ID of submitted paper */
	MemberName VARCHAR(30) NOT NULL , /* User name of reviewer */
	PreferenceID TINYINT UNSIGNED NOT NULL , /* Preference this Paper given by Reviewer */
	PRIMARY KEY ( PaperID , MemberName ) /* Composite keys */
)
;

CREATE TABLE Preference
(
	PreferenceID TINYINT AUTO_INCREMENT PRIMARY KEY , /* PreferenceID of the paper */
	PreferenceName VARCHAR(250) NOT NULL /* Preference Name set by administrator */
);

INSERT INTO Preference ( PreferenceName )
VALUES ( 'I want to review this paper.' ) ;
INSERT INTO Preference ( PreferenceName )
VALUES ( 'I am qualified to review this paper.' ) ;
INSERT INTO Preference ( PreferenceName )
VALUES ( 'I do not know this area well.' ) ;
INSERT INTO Preference ( PreferenceName )
VALUES ( 'I have a conflict of interest on this paper.' ) ;

CREATE TABLE Paper
(
	PaperID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Paper ID of submitted paper*/
	Title VARCHAR(255) NOT NULL , /* Title of this paper */
	PaperAbstract TEXT NOT NULL , /* Description of paper */
	NumberOfPages	SMALLINT NOT NULL , /* Number of pages in this document */
	PaperStatusID TINYINT NOT NULL DEFAULT 1 , /* Paper Status ID of this submitted paper */
	MemberName VARCHAR(50) NOT NULL , /* Name of user who uploaded this paper */
	Withdraw VARCHAR(5) NOT NULL DEFAULT 'false' , /* Is the paper withdrawn by user? */
	Invited VARCHAR(5) NOT NULL DEFAULT 'false' , /* Is this an invited paper? */
	Copyright VARCHAR(5) NOT NULL DEFAULT 'false' , /* Permission to publish/copyright form received */
	OverallRating FLOAT NOT NULL DEFAULT 0 , /* Overall rating from reviews */
	PresenterName VARCHAR(50) NOT NULL , /* Name of presenter who will present this paper */
	PresenterBio TEXT NOT NULL , /* Biography of Presenter */
	TrackID INT UNSIGNED NOT NULL /* Track of Paper */
	
)
;

CREATE TABLE File
(
	FileID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* File ID of uploaded file */
	File LongBLOB NOT NULL, /* File binary of the uploaded docuement */ 
	FileName VARCHAR(255) NOT NULL , /* Filename of the uploaded document */
	FileSize VARCHAR(50) NOT NULL , /* Filesize of the uploaded document */
	FileType VARCHAR(50) NOT NULL , /* Filetype of the uploaded document */
	DateTime DATETIME NOT NULL , /* Date and time of uploaded document */		
	PaperID INT UNSIGNED NOT NULL, INDEX PaperID (PaperID)/* Paper ID of submitted paper, indexed for speed */	
)
;

CREATE TABLE PaperStatus
(
	PaperStatusID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Paper Status ID */
	PaperStatusName VARCHAR(15) NOT NULL DEFAULT "Not Reviewed"/* Not Reviewed / Reviewing / Accepted / Rejected */
)
;

INSERT INTO PaperStatus ( PaperStatusName )
VALUES ( 'Not Reviewed' ) ;
INSERT INTO PaperStatus ( PaperStatusName )
VALUES ( 'Reviewed' ) ;
INSERT INTO PaperStatus ( PaperStatusName )
VALUES ( 'Accepted' ) ;
INSERT INTO PaperStatus ( PaperStatusName )
VALUES ( 'Rejected' ) ;
INSERT INTO PaperStatus ( PaperStatusName )
VALUES ( 'Reviewing' ) ;
INSERT INTO PaperStatus ( PaperStatusName )
VALUES ( 'Marginal' ) ;

CREATE TABLE Member
(
	MemberName VARCHAR(50) PRIMARY KEY , /* User Name of registered member */
	Password VARCHAR(50) NOT NULL , /* Password of this registered user */
	PrivilegeTypeID TINYINT NOT NULL DEFAULT 1 , /* Privilege type of this user */
	RegisterID INT UNSIGNED NOT NULL , /* Registration ID of this registered user */
	TrackID TINYINT NOT NULL DEFAULT 0 /* Track papers accessible to this member, if reviewer or trackchair */
)
;

CREATE TABLE PrivilegeType
(
	PrivilegeTypeID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Previlege Type ID of a registered user */
	PrivilegeTypeName VARCHAR(30) NOT NULL  /* User 01 / Evaluator 02 / Reviewer 03 */
)
;

INSERT INTO PrivilegeType ( PrivilegeTypeName )
VALUES ( 'User' ) ;
INSERT INTO PrivilegeType ( PrivilegeTypeName )
VALUES ( 'Reviewer' ) ;
INSERT INTO PrivilegeType ( PrivilegeTypeName )
VALUES ( 'Administrator' ) ;

CREATE TABLE Session
(
	SessionID SMALLINT AUTO_INCREMENT PRIMARY KEY , /* Session ID of a session being held */
	SessionName TINYTEXT NOT NULL , /* Session name */
	TrackID TINYINT UNSIGNED, /* The track all papers in this session belong to, NULL if no restriction */
	PresentationTypeID TINYINT UNSIGNED NOT NULL, /* Presentation type ID of that one session */
	StartTime DATETIME NOT NULL , /* Start time of one session */
	EndTime DATETIME NOT NULL , /* End time of one session */
	RoomID TINYINT UNSIGNED NOT NULL , /* Room ID where conference is held */
	ChairID INT UNSIGNED NOT NULL /* RegisterID of Reviewer who is chairing the conference at one session */
)
;

CREATE TABLE SessionSlot
(
	SessionID SMALLINT NOT NULL, /* Session ID of a session the slot is in */
	SlotID SMALLINT UNSIGNED NOT NULL, /* Slot ID of which dictates order in slot */
	PaperID INT UNSIGNED NOT NULL, /* Paper ID of presented paper */
	PRIMARY KEY ( PaperID )
)
;


CREATE TABLE Room
(
	RoomID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Room ID of a place where session being held */
	RoomName TINYTEXT NOT NULL /* Room where conference is held
	Chapel 01 / Common Room 02 / Chapel Gardens 03 / Main Foyer 04 / Dining Room 05 */
)
;

CREATE TABLE Presenter
(
	PaperID INT UNSIGNED NOT NULL PRIMARY KEY, /* Paper ID which the presenter would talk about */
	RegisterID INT UNSIGNED NOT NULL /* Registered ID of a presenter at a slot */
)
;

CREATE TABLE PresentationType
(
	PresentationTypeID TINYINT AUTO_INCREMENT PRIMARY KEY , /* Presentation Type ID */
	PresentationTypeName VARCHAR(30) NOT NULL, /* eg, Oral, Poster, etc. */
	SlotLength INT NOT NULL /* Length in minutes of a SessionSlot of this type */
)
;

CREATE TABLE UnscheduledPaper /* Holds papers accepted but not in sessions yet */
(
	PaperID INT UNSIGNED NOT NULL PRIMARY KEY,
	PresentationTypeID TINYINT NOT NULL /* Presentation Type ID */
)
;

CREATE TABLE Registration
(
	RegisterID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Register ID of a member */
	FirstName VARCHAR(30) NOT NULL , /* First name of registered member */
	MiddleName VARCHAR(30) NOT NULL , /* Middle name of registered member */
	LastName VARCHAR(30) NOT NULL , /* First name of registered member */
	Organisation VARCHAR(50) NOT NULL , /* Organisation of registered member */
	Address1 VARCHAR(200) NOT NULL , /* Address 1 of residence of registered member */
	Address2 VARCHAR(200) , /* Address 2 of residence of registered member */
	City VARCHAR(30) NOT NULL , /* City of residence of registered member */
	State VARCHAR(30) NOT NULL , /* State of residence of registered member */
	PostalCode VARCHAR(15) NOT NULL , /* Postal code in country of residence of registered member */
	Country VARCHAR(30) NOT NULL , /* Country of residence of registered member */
	Email VARCHAR(50) NOT NULL , /* Work E-mail of registered member */
	EmailHome VARCHAR(50) NOT NULL , /* Home E-mail of registered member */
	PhoneNumber VARCHAR(25) NOT NULL, /* Work Phone number of registered member */
	PhoneNumberHome VARCHAR(25) NOT NULL, /* Home Phone number of registered member */
	FaxNumber VARCHAR(25) /* Faxnumber of the registered member */
)
;

CREATE TABLE PaymentForm
(
	FormID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY ,
	RegisterID INT UNSIGNED NOT NULL , /* Registration ID of user */
	Form BLOB NOT NULL , /* Registration Request XML */
	Paid BOOL NOT NULL DEFAULT 0
)
;

CREATE TABLE PaperCategory
(
	PaperID INT UNSIGNED NOT NULL , /* Paper ID of submitted paper */
	CategoryID INT UNSIGNED NOT NULL , /* Category ID of this submitted paper */
	PRIMARY KEY ( PaperID , CategoryID ) /* Composite keys */	
)
;

CREATE TABLE Category
(
	CategoryID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Category ID of this submitted paper */
	CategoryName VARCHAR(50) NOT NULL /* Medical 01 / Physics 02 / Electrical 03 / etc... */
)
;

INSERT INTO Category ( CategoryName )
VALUES ( 'Medical Applications' ) ;
INSERT INTO Category ( CategoryName )
VALUES ( 'Computer vision' ) ;
INSERT INTO Category ( CategoryName )
VALUES ( 'Structural pattern recognition' ) ;
INSERT INTO Category ( CategoryName )
VALUES ( 'Image coding and processing' ) ;
INSERT INTO Category ( CategoryName )
VALUES ( 'Biomedical pattern analysis' ) ;
INSERT INTO Category ( CategoryName )
VALUES ( 'Speech recognition' ) ;

CREATE TABLE Track
(
	TrackID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Track ID of this submitted paper */
	TrackName VARCHAR(50) NOT NULL /* Medical 01 / Physics 02 / Electrical 03 / etc... */
)
;

INSERT INTO Track ( TrackName )
VALUES ( 'Pattern Recognition' ) ;
INSERT INTO Track ( TrackName )
VALUES ( 'Computer Vision' ) ;
INSERT INTO Track ( TrackName )
VALUES ( 'Medical Imaging' ) ;
INSERT INTO Track ( TrackName )
VALUES ( 'Applications' ) ;


CREATE TABLE ConferencePhase
(
	PhaseID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, /* Phase ID of the conference */
	PhaseName VARCHAR(50) NOT NULL,/* Phase Name of the conference */
	StartDate DATETIME NOT NULL, /* Starting Date of the phase */
	EndDate DATETIME NOT NULL, /* Ending Date of the phase */
	Status VARCHAR(10) NOT NULL DEFAULT 'false' /* Current Phase Status */
)
;

INSERT INTO ConferencePhase ( PhaseName , Status )
VALUES ( 'Paper Submission' , 'true' ) ;
INSERT INTO ConferencePhase ( PhaseName , Status )
VALUES ( 'Reviewer Bidding' , 'false' ) ;
INSERT INTO ConferencePhase ( PhaseName , Status )
VALUES ( 'Reviewing' , 'false' ) ;
INSERT INTO ConferencePhase ( PhaseName , Status )
VALUES ( 'Final Paper Submission' , 'false' ) ;

# MySQL-Front Dump 2.5
#
# Host: localhost   Database: dmquerylog
# --------------------------------------------------------
# Server version 3.23.42-nt


#
# Table structure for table 'querylog'
#

CREATE TABLE querylog (
  logID int(5) NOT NULL auto_increment PRIMARY KEY,
  dmdate date default NULL,
  dmquery text,
  dmbrowser varchar(250) default '0',
  dmpath varchar(250) default '0',
  dmremote varchar(30) default '0',
  dmreferer varchar(250) default '0',
  dmquerystring varchar(250) default '0',
  dmtablename varchar(255) default '0',
  dmdbname varchar(255) default '0'
)
;

CREATE TABLE Conference
(
	ConferenceID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Conference ID */
	ConferenceName VARCHAR(100) NOT NULL , /* Conference Name of the Running Conference */
	ConferenceCodeName VARCHAR(20) NOT NULL, /* Code Name of the Conference Example: WDIC 2003 */
	ConferenceStartDate DATE NOT NULL, /* The Date that the conference will held*/
	ConferenceEndDate DATE NOT NULL, /* The Date that the conference will held*/	
	ConferenceLocation VARCHAR(20) NOT NUll, /* The City or the location of the conference */
	ConferenceHostName VARCHAR(100) NOT NULL, /* The host name of the conference */
	ConferenceContact VARCHAR (50) NOT NULL, /*The main contact email for the conference*/
	ConferenceContactName VARCHAR (50) NOT NULL, /*The main contact person for the conference*/
	LogoFile LONGBLOB, /*The logo file of the conference */
	FileName VARCHAR(50), /*The logo file name of the conference */
	FileSize VARCHAR(50), /*The logo size name of the conference */
	FileType VARCHAR(50) /*The logo type name of the conference */	
)
;

CREATE TABLE RecipientGroup
(
	RecipientGroupID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* RecipientGroup ID */
	RecipientGroupName VARCHAR(100) NOT NULL /* RecipientGroup Name */
)
;

INSERT INTO RecipientGroup (RecipientGroupName)
VALUES ('Undecided');

INSERT INTO RecipientGroup (RecipientGroupName)
VALUES ('Users');

INSERT INTO RecipientGroup (RecipientGroupName)
VALUES ('Reviewers');

INSERT INTO RecipientGroup (RecipientGroupName)
VALUES ('Administrators');

INSERT INTO RecipientGroup (RecipientGroupName)
VALUES ('Accepted Users');

INSERT INTO RecipientGroup (RecipientGroupName)
VALUES ('Rejected Users');

CREATE TABLE Letter
(
	LetterID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY , /* Letter ID */
	Title VARCHAR(250) NOT NULL, /* Title of the letter */	
	Subject VARCHAR(250), /* Subject of the letter */
	BodyContent TEXT, /* Body or Middle Content of the mail */
	RecipientGroupID INT UNSIGNED NOT NULL /* The norminal recipient group of the letter */
)
;

INSERT INTO Letter (LetterID, Title, Subject, BodyContent, RecipientGroupID) 
VALUES("1", "Reviewer Invitation and Instructions", "Invitation to join $confcode PC", "Dear $fullname,\r\n\r\nPlease accept this invitation to join the Program Committee of $confname.  This will involve the electronic review of a small number of papers in your domain of expertise.\r\n\r\nFor more information please visit the $confcode Website at \r\n$url\r\n\r\nPlease let me know if you are willing accept this invitation by replying to $contact.\r\n\r\nAll the best\r\n\r\n\r\n\r\n", "3");

INSERT INTO Letter (LetterID, Title, Subject, BodyContent, RecipientGroupID) 
VALUES("2", "User Account Info", "$confcode Account Information", "Dear $fullname,\r\n\r\nHere is your $confcode account information\r\n\r\nUsername: $username \r\nPassword: $password\r\n\r\nPlease log in at \r\n$url \r\nto upload papers. \r\n\r\nIf you experience any problems please contact $contact.", "2");

INSERT INTO Letter (LetterID, Title, Subject, BodyContent, RecipientGroupID) 
VALUES("3", "Reviewer Account Info", "$confcode Reviewer Account Information", "Dear $fullname,\r\n\r\nYour $confcode reviewer account information is\r\n\r\nUserName: $username\r\nPassWord: $password\r\n\r\nPlease log in at \r\n$url\r\nto perform reviewing tasks.  \r\n\r\nIf you experience any problems please email $contact.", "3");

INSERT INTO Letter (LetterID, Title, Subject, BodyContent, RecipientGroupID) 
VALUES("4", "Admin Account Info", "$confcode Admin Account Information", "Admin account\r\n\r\nUserName: $username\r\nPassWord: $password\r\n\r\nPlease log in at $url to manage the conference system. If you experience any problems, please email $contact.", "4");

INSERT INTO Letter (LetterID, Title, Subject, BodyContent, RecipientGroupID) 
VALUES("5", "Paper Acceptance", "$confcode Paper Status", "Dear $fullname,\r\n\r\npaper number: #$paperID\r\nTitle: $papertitle\r\nAuthors: $authors\r\nCategory: $papercat\r\n\r\nWe are pleased to inform you that your paper has been accepted for the $confname.\r\n\r\nPlease log in at \r\n$url\r\n to view your reviewers\\\' comments.  \r\n\r\nReviewers have spent considerable time and effort in reviewing your submission, so please take their comments into account in your revision.  Finally remember to upload the final version of your paper by the due date. \r\n\r\nWe look forward to seeing you at $confcode.", "5");

INSERT INTO Letter (LetterID, Title, Subject, BodyContent, RecipientGroupID) 
VALUES("6", "Paper Rejection", "$confcode Paper Status", "Dear $fullname,\r\n\r\nPaper Number: $paperID\r\nTitle: $papertitle\r\nAuthors: $authors\r\nCategory: $papercat\r\n\r\n\r\nWe regret to inform you that your paper was not accepted for $confname.\r\n\r\nPlease log in at \r\n$url\r\nto view the reviewers\\\' comments.\r\n\r\nWe hope you can still attend $confname and look forward to your participation.", "6");

CREATE TABLE MailLog
(
	MemberName VARCHAR(30) NOT NULL , /* User name of reviewer */	
	LetterID INT UNSIGNED NOT NULL, /* Letter ID */	
	PRIMARY KEY ( MemberName , LetterID ) /* Composite keys */	
)
;

CREATE TABLE Settings
(
    Name VARCHAR(128) NOT NULL, 
    Value BLOB NULL, 
    PRIMARY KEY (Name)
)
;

INSERT INTO Settings(Name,Value) VALUES ("MaxUploadSize", '10485760');
INSERT INTO Settings(Name,Value) VALUES ("MaxLogoSize", '1048576');
INSERT INTO Settings(Name,Value) VALUES ("MaxLogoWidth", '60');
INSERT INTO Settings(Name,Value) VALUES ("MaxLogoHeight", '60');
INSERT INTO Settings(Name,Value) VALUES ("EmailSignature", 'Thanks,\r\nConference Management');
INSERT INTO Settings(Name,Value) VALUES ("BackgroundColor", '#FFFFFF');
INSERT INTO Settings(Name,Value) VALUES ("TrackName", 'Track');
INSERT INTO Settings(Name,Value) VALUES ("TopicName", 'Topic');
INSERT INTO Settings(Name,Value) VALUES ("LevelName", 'Level');
INSERT INTO Settings(Name,Value) VALUES ("CountryDefault", 'AU');
INSERT INTO Settings(Name,Value) VALUES ("DateFormatShort", 'j M Y');
INSERT INTO Settings(Name,Value) VALUES ("DateFormatLong", 'l, j F Y');
INSERT INTO Settings(Name,Value) VALUES ("DoubleBlindReview", 'false');
INSERT INTO Settings(Name,Value) VALUES ("UserPhase", '0');
INSERT INTO Settings(Name,Value) VALUES ("ReviewerPhase", '0');
INSERT INTO Settings(Name,Value) VALUES ("SESUG", '0');
INSERT INTO Settings(Name,Value) VALUES ("AbstractOnlySubmissions", '0');
