
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML lang=en><HEAD><META content="IE=7.0000" http-equiv="X-UA-Compatible">
<head>
<TITLE>ALTA Workshop 26-28 November 2014 - Registration</TITLE>
<META content="text/html; charset=iso-8859-1" http-equiv=content-type>
<LINK rel=stylesheet type=text/css href="altaworkshop.css">
<link rel="shortcut icon" href="favicon.ico" >
</head>
<style>
.rego_td {
    border: 1px solid black;
    border-collapse: collapse;
    padding: 5px;
}
</style>
</head>
<BODY>

<script src="/site/jquery/jquery.min.js" type="text/javascript"></script>
<script src="/site/jquery/jquery.validate.pack.js" type="text/javascript"></script>
<script src="/site/jquery/additional-methods2.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready(function(){
  // validate signup form on keyup and submit
  var validator = $("#contactform").validate({

    rules: {
      contactname: {
	required: true,
	propername: true,
	minlength: 4
	},
      institution: {
	required: true,
	instname: true,
	minlength: 2
	},
     address1: {
	required: true,
	addy: true,
	minlength: 8
	},
      city: {
        required: true,
        propername: true,
        minlength: 2
        },
      country: {
        required: true,
        provcount: true,
        minlength: 2
        },
      telephone: {
        required: true,
        phonenum: true,
        minlength: 7
        },
      email: {
	required: true,
	email: true
	},
      zanum: {
	integer: true
	},
      fcnum: {
	integer: true
	},
      },
    messages: {
      contactname: {
	required: "Please enter your name",
	propername: "Please enter standard alphabetic characters",
	minlength: jQuery.format("Enter at least {0} characters")
	},
      institution: {
	required: "Please enter the name of your organisation.",
	instname: "Please enter standard alphabetic characters",
	minlength: jQuery.format("Enter at least {0} characters")
	},
      address1: {
	required: "Please enter your contact address.",
	addy: "Please enter only letters, numbers, '(',')','-'",
	minlength: jQuery.format("Enter at least {0} characters")
	},
      city: {
        required: "Please enter the name of the city for this address.",
        propername: "Please enter only letters, numbers, '(',')','-'",
        minlength: jQuery.format("Enter at least {0} characters")
        },
      country: {
        required: "Please enter the name of the country for this address.",
        provcount: "Please enter only letters and optionally ',' and/or '-'",
        minlength: jQuery.format("Enter at least {0} characters")
        },
      telephone: {
        required: "Please enter a contact phone-number.",
        phonenum: "Please enter only digits and optionally '-' and/or '(' ')'",
        minlength: jQuery.format("Enter at least {0} characters")
        },
     email: {
	required: "Please enter a valid email address",
	minlength: "Please enter a valid email address"
	},
     zanum: {
	integer: "Please enter numbers only"
	},
     fcnum: {
	integer: "Please enter numbers only"
	},
      },

    // set this class to error-labels to indicate valid fields
      success: function(label) {
	label.addClass("checked");
	}
      });
    });

</script>

<?php
//If the form is submitted
if(isset($_POST['submit'])) {
$namerr = '';

	//Check to make sure that the name field is not empty
	if(trim($_POST['contactname']) == '')
          { $hasError = 'name'; }
	else if ( eregi("http:", trim($_POST['contactname'])))
                { $namerr = true; $hasError = 'name'; }
	     else
		{ $contactname = trim($_POST['contactname']);
                }

        if(trim($_POST['prefname']) == '')
                { $prefname = ""; }
        else
                { $prefname = trim($_POST['prefname']);
                }

	//Check to make sure that the Organisation field is not empty
	if(trim($_POST['institution']) == '')
		{ $hasError = 'institution'; }
	else
	 	{ $institution = trim($_POST['institution']);
		}

	//Check to make sure that Address line 1 is not empty
	if(trim($_POST['address1']) == '')
		{ $hasError = 'address'; }
	else
	 	{ $address1 = trim($_POST['address1']);
		}

        if(trim($_POST['address2']) == '')
                { $address2= ""; }
        else
                { $address2= trim($_POST['address2']);
                }

	//Check to make sure that the city field is not empty
	if(trim($_POST['city']) == '')
		{ $hasError = 'city'; }
	else
	 	{ $city = trim($_POST['city']);
		}

        if(trim($_POST['postcode']) == '')
                { $postcode = ""; }
        else
                { $postcode = trim($_POST['postcode']);
                }

        if(trim($_POST['state']) == '')
                { $state = ""; }
        else
                { $state = trim($_POST['state']);
                }

	//Check to make sure that the Country field is not empty
	if(trim($_POST['country']) == '')
		{ $hasError = 'country'; }
	else
	 	{ $country = trim($_POST['country']);
		}

	//Check to make sure that a Contact Phone number has been supplied
	if(trim($_POST['telephone']) == '')
		{ $hasError = 'telephone'; }
	else
	 	{ $telephone = trim($_POST['telephone']);
		}

	//Check to make sure sure that a valid email address is submitted
	if(trim($_POST['email']) == '')
		{ $hasError = 'email'; }
	else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email'])))
		{ $hasError = 'email seems invalid'; }
	    else
		{ $email = trim($_POST['email']);
		}

        if(eregi("http:", trim($_POST['special_requirements'])))
             { $hasError = 'special requirements seem invalid'; }
             else if(!trim($_POST['special_requirements']) == '')
                { $special_requirements= trim($_POST['special_requirements']);
                }

	$workshop = trim($_POST['workshop']);

	$tutorial = trim($_POST['tutorial']);

        if(trim($_POST['student']) == '')
                { $student = "0"; }
        else
                { $student = "1";
                }

        if(trim($_POST['RadioGroup1']) == '')
                { $hasError = 'dinner attendance'; }
        else if(trim($_POST['RadioGroup1']) == 'pizzayes')
                    { $pizzaopt = 'dinner'; }
                else if (trim($_POST['RadioGroup1']) == 'pizzano')
                        { $pizzaopt = 'no dinner'; }

	if(trim($_POST['zanum'])== '')
	 	{ $zanum = "0";}
	else
		{ $zanum = trim($_POST['zanum']);}


	if(trim($_POST['fcnum'])== '')
		{ $fcnum = "0";}
	else
		{ $fcnum = trim($_POST['fcnum']);}

        if(trim($_POST['paccepted']) == '')
                { $paccepted = "0";
		  $ptitle1 = "";
		  $pnumber1 = "";
		  $ptitle2 = "";
		  $pnumber2 = "";
		}
        else
                { $paccepted = trim($_POST['paccepted']);
		  $ptitle1 = trim($_POST['ptitle1']);
		  $pnumber1 = trim($_POST['pnumber1']);
		  $ptitle2 = trim($_POST['ptitle2']);
		  $pnumber2 = trim($_POST['pnumber2']);
                }

	$privacy = trim($_POST['privacy']);


$fields = array(
    'contactname'=>$contactname,
    'prefname'=>$prefname,
    'institution'=>$institution,
    'address1'=>$address1,
    'address2'=>$address2,
    'city'=>$city,
    'postcode'=>$postcode,
    'state'=>$state,
    'country'=>$country,
    'telephone'=>$telephone,
    'email'=>$email,
    'special_requirements'=>$special_requirements,
    'workshop'=>$workshop,
    'tutorial'=>$tutorial,
    'student'=>$student,
    'pizzaopt'=>$pizzaopt,
    'zanum'=>$zanum,
    'fcnum'=>$fcnum,
    'paccepted'=>$paccepted,
    'ptitle1'=>$ptitle1,
    'pnumber1'=>$pnumber1,
    'ptitle2'=>$ptitle2,
    'pnumber2'=>$pnumber2,
    'privacy'=>$privacy,
    'sentfrom'=>$_SERVER['SERVER_NAME']
    );

$postvars='';
$sep='';

foreach($fields as $key=>$value)
{
   $postvars.=$sep.urlencode($key).'='.urlencode($value);
   $sep='&';
}
$fname = md5($postvars);

if(!isset($hasError)) {
    $fh = fopen('/disks/etna/home/clt/ltg/www/htdocs/alta/events/alta2014/alta-reg/regos/'.$fname.'.csv', 'w');
    fputcsv($fh, $fields);
    fclose($fh);
    $submitted = true;

    mail("$email,stephen.wan@csiro.au, gabriela.ferraro@nicta.com.au", #to
         'ALTA 2014 registration', #subject
         "Thank you for registering for the ALTA 2014 workshop.  If you are a non-student participant, please pay your online registration fee by visiting this website: http://www.trybooking.com/GHJZ.  Payment is due by the 21st of November, 2014.\n\n\nKind regards\n\nStephen Wan and Gabriela Ferraro.", #message
         'From: ALTA 2014 registration <workshop@alta.asn.au>' #extra_hdr
    );
}

}
 
?>

<!-- Add left side menu -->
<?php include("macquarie-header"); ?>

<!-- Start of main content -->

<br/>
<br/>
<hr size="1" noshade>
<h1>ALTA Workshop 2014 - Registration form</h1>

<div class="wrapper">
  <div id="contactWrapper" role="form">

<?php
# registrations closed
$closed=0; # 1 or 0; 1=closed, 0=open
if ($closed) {
?>
<p>Registrations are now closed as we have reached capacity. If you have not
yet registered and wish to participate, please contact us directly.</p>
<?php include("macquarie-footer"); ?>
</BODY></HTML>
<?php
  exit;
}
?>

<p>Registration for the ALTA Workshop 2014 (RMIT, Melbourne, 26th - 28th November 2014) is a 2-stage process:</p>
<ol>
<li>Fill in this form to register for the event.</li>
<li>Upon completion of this form, you will be directed to a payment site to pay the registration fee if you are not a student.</li>
</ol>

<p>Note: In this form, please do not put links in response to any of the questions or your registration will fail.</p>

	<?php if(isset($hasError)) { //If errors are found ?>
	    <p class="error"><em>Please check that all the fields have valid information (e.g. <?php echo $hasError; ?>). Thanks.</em></p>
	<?php } ?>
	<?php if(isset($submitted)) { //If submitted?>
	    <p class="error"><strong>Thank you for registering.  </strong></p>
		<p>If you are a non-student participant, please pay your online registration fee by visiting this website: <a href="http://www.trybooking.com/GHJZ" target="_blank">http://www.trybooking.com/GHJZ</a> (Payments due by the 21st of November, 2014).</p>
	<?php } else { ?>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="contactform">

<fieldset id="personalinfo">
<legend>Personal Information</legend>

	  <div>
	    <label for="contactname"><font size="2">Contact Name </font><span class="required" title="Required field.">*</span></label>
	    <input type="text" name="contactname" id="contactname" value="<?php echo $contactname ?>" class="required" role="input" />
	  </div>

          <div>
            <label for="prefname">Preferred Name for ID : </label>
            <input type="text" name="prefname" id="prefname" value="<?php echo $prefname ?>" />
          </div>

	  <div>
	    <label for="institution">Organisation <span class="required" title="Required field.">*</span></label>
	    <input type="text" name="institution" id="Organisation" value="<?php echo $institution ?>" class="required" role="input" aria-required="true" />
	  </div>

	  <div>
	   <label for="address1">Contact address Line 1 <span class="required" title="Required field.">*</span></label>
	  <input type="text" name="address1" id="address1" value="<?php echo $address1 ?>" /> 
	  </div>

	  <div>
	    <label for="address2">Contact address Line 2 : </label>
	    <input type="text" name="address2" id="address2" value="<?php echo $address2 ?>" />  
	  </div>

	  <div>
	    <label for="city">City <span class="required" title="Required field.">*</span></label>
	    <input type="text" name="city" id="city" value="<?php echo $city ?>" />	    
	  </div>

	  <div>
	    <label for="postcode">Postcode : </label>
	    <input type="text" name="postcode" id="postcode" value="<?php echo $postcode ?>" />  
	  </div>

	  <div>
	    <label for="state">State/Province : </label>
	    <input type="text" name="state" id="state" value="<?php echo $state ?>" />  
	  </div>

	  <div>
	    <label for="country">Country <span class="required" title="Required field.">*</span></label>
	    <input type="text" name="country" id="country" value="<?php echo $country ?>" />  
	  </div>

	  <div>
	    <label for="telephone">Contact Phone <span class="required" title="Required field.">*</span></label>
	    <input type="text" name="telephone" id="telephone" value="<?php echo $telephone ?>" />  
	  </div>

	  <div>
	    <label for="email"><font size="2">Email </font><span class="required" title="Required field.">*</span></label>
	    <input type="text" name="email" id="email" value="<?php echo $email ?>" class="required email" role="input" aria-required="true" />
	  </div>

	  <div class="stage clear">
	    <label for="special_requirements">Please inform us of any dietary and/or access requirements you may have <br/><br/><br/></label>
	    <textarea name="special_requirements" id="special_requirements"><?php echo $special_requirements ?></textarea>
	  </div>

</fieldset>

<fieldset id="confinfo">
<legend>Workshop and Tutorial Registration</legend>

<div class="stage clear">
<br/>
<p>Thanks to our generous sponsors (CSIRO, Google), there is no registration fee for student participants who register online.</p>

<b>Registration fees</b>
<center>
<table style="border: 1px solid black;border-collapse: collapse;">
<tr>
<td class="rego_td">Participant type</td><td  class="rego_td">Online (closing 21 Nov 2014)</td><td  class="rego_td">In person at the registration desk</td>
</tr>
<tr>
<td  class="rego_td">Student</td><td  class="rego_td">$0 AUD</td><td  class="rego_td">$75 AUD</td>
</tr>
<tr>
<td  class="rego_td">Non-student</td><td  class="rego_td">$50 AUD </td><td  class="rego_td">$75 AUD</td>
</tr>
</table>
</center>

<p>Please note:</p>
<ul>
<li>We are using a 3rd party site (TryBooking) to collect registration fees.  You will be given the payment link upon completion of this registration form.</li>
<li>If you choose to attend without registering online (before 21 Nov 2014) or to pay on-site you will be required to pay a $75 registration fee in cash.</li>
<li>We will organise a conference dinner, but that must be paid for directly by each attendee at the dinner venue.</li>
<li>Students may ask to be considered for a <a href="http://alta.asn.au/events/alta2014/alta-2014-studenttravel.html" target="_blank">student travel support</a>
</li>
</ul>

<div class="stage clear">
<label for="workshop"><strong>Register for ALTA 2014</strong></label>
<input type="checkbox" name="workshop" id="workshop" value="<?php echo isset($workshop)?$workshop:'YES'; ?>" CHECKED>
(Please <strong>uncheck</strong> this box if you do <strong> NOT</strong> wish to attend the ALTA Workshop main sessions.)
</div>

<div class="stage clear">
<label for="student"><strong>Student</strong></label>
<input type="checkbox" name="student" id="student" value="<?php echo isset($student)?$student:'NO'; ?>">
(<strong>Check this box if you are a student</strong> at an accredited higher education institute.  Proof of student status may be requested.)
</div>


<br/>
<strong>Tutorial Registration</strong><br/>
<p>Tutorials will run on 26th of November (check the <a href="http://www.alta.asn.au/events/alta2014/alta-2014-programme.html">programme</a> for the timetable). 
</p>
<p>You may register to attend a tutorial even if you do not intend to come to the Workshop.</p>
Please indicate your preference by selecting the relevant option:<br/>
<select name="tutorial" size=1>
<option>None</option>
<option>Gaussian Processes for NLP (Dr. Trevor Cohn)</option>
<!-- <option>Machine Learning Approaches for Dealing with Limited Bilingual Data in Statistical Machine Translation (Dr. Gholamreza Haffari)</option> 
<option>Both</option>
-->
</select>
<p>Please note that there will be no catering on the tutorial day; refreshments will be available for purchase within the building.</p>



</fieldset>

<fieldset id="otherinfo">
	<legend><span>Accepted papers</span></legend>
          <div class="stage clear">

	<p>Each accepted paper requires one of the authors to register and attend the
	conference or the paper will need to be withdrawn.</p>

<p> If you are presenting one of more papers, please specify how many and provide the paper number :-
</p>
	<label for="accepted">Number of papers accepted for presentation</label>
	<input type="number" name="paccepted" id="paccepted" value="0" />
<br/> <p>
</p>
<p>
<br/>
	<label for="ptitle1">Paper 1 Title</label>
	<input type="text" name="ptitle1" id="paper_title_1" value="" />
	<label for="pnumber1">Paper 1 Number</label>
	<input type="text" name="pnumber1" id="paper_number_1" value="" />
<br/>  </p><p>
	<label for="ptitle2">Paper 2 Title</label>
	<input type="text" name="ptitle2" id="paper_title_2" value="" />
	<label for="pnumber2">Paper 2 Number</label>
	<input type="text" name="pnumber2" id="paper_number_2" value="" />
<br/></p>
</div>
</fieldset>

<fieldset> <legend>Social Event</legend>
<p>There will be a Conference Dinner at 7:00pm on Thursday, November 27th.  The estimated cost for a meal will be approximately $20 and will be paid for by the attendee (student and non-student) at the dinner. </p>
<p>You may be accompanied by extra people to either/both of the social events if you wish, but you must indicate to the organisers how many people will be
attending, so that the venue can be informed of numbers.
</p>
		  <br/>
          Please indicate whether you would like to attend:<br/><br/>
                <label for="pizzayes">Yes</label>
              <input type="radio" name="RadioGroup1" id="pizzayes" value="pizzayes" class="required" role="input" aria-required="true" /> <br/>
                <label for="pizzano">No</label>
              <input type="radio" name="RadioGroup1" id="pizzano" value="pizzano"  class="required" role="input" aria-required="true" /> <br/>
<br/>

If you would like to bring extra people to the dinner, please indicate the <br/>
<label for=zanum>number of <strong>extra people</strong> here:</label>
		<input type="number" name="zanum" id="zanum" value="0" />  
</fieldset>

<fieldset>
<legend>Disclaimer</legend>

<p>In the event of unforeseen circumstances (industrial or otherwise) that
disrupt the conference, the conference organisers accept no responsibility.</p>

<p>The information on the conference website and in the printed material is
correct at the time of publication. However, the conference organisers reserve
the right to change information if required and to adjust the conference
programme accordingly.</p></fieldset>

<fieldset><legend>Submission of Details</legend>
<p>Please check that you have entered your details correctly, and then use the
"Submit" button to send them to the organisers. 
<input type="submit" value="Submit" name="submit" id="submitButton" title="Register" />
</p>

</form>
<?php } ?>

      </div>
    </div>


<!-- End of main table --><!-- Start of footer -->

<?php include("macquarie-footer"); ?>
<br /> <br />
</BODY></HTML>

