#! /usr/local/bin/python
# publish.py -- Update the header and footer of an HTML page

# This program reads standard input, which is assumed to be an HTML page.
# The header and footer are updated to the latest Web structure and
# formatting design.

# Author: Diego Molla
# Created: 25/02/03

################# Document Scanning  #####################################

import sys
import re
import time
import os

if len(sys.argv) == 1:
   filein = sys.stdin
   fileout = sys.stdout
else:
   fname = sys.argv[1]
   os.system('cp '+fname+' '+fname+'.bk')
   filein = open(fname+'.bk')
   fileout = open(fname,'w')

document = filein.readlines()

line = document.pop(0)

search_pattern = re.compile(r'<!-- .*Document Id:.*?"(.*?)".* -->')

res = search_pattern.search(line)
while not res:
      line = document.pop(0)
      res = search_pattern.search(line)

body = line
line = document.pop(0)

search_pattern = re.compile(r'<!-- ----- FOOTER - DO NOT EDIT BEYOND THIS LINE ----- -->')

while not search_pattern.search(line):
      body += line
      line = document.pop(0)

body += line

################# Header ##############################
header = '''
<!DOCTYPE 
HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">

<html lang="en">
<head>
  <title>ALTA Summer School</title>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-15">
  <!-- Start generic metadata -->
  <meta name="description" content="ALTA: ALTSS and ALTW">
  <meta name="Author" content="ALTA secretary">
  <!-- end generic -->
  
  <link rel="stylesheet" href="summer_school.css" type="text/css">

</head>

<body>
<!-- This table contains the main structure of the page -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" summary="This table contains the main structure">

  <tr valign="middle">
    <!-- Logo -->
    <td width="85" align="center"><a href="http://www.alta.asn.au"><img src="http://www.alta.asn.au/images/logo_sm.jpg" width="74" height="65" border="0" alt="ALTA logo"></a></td>
    <!-- Title -->
    <td width="100%" colspan="5" class="sitename" align="center">
Australasian Language Technology Summer School</td>
  </tr>

  <tr valign="top">

<td width="15%">
 
<!-- This table contains the Macquarie navigation links -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="headerbar" summary="This table contains the top level navigation links">


  <tr>
    <td align="left" class = "headercell">
	  <a class="headerlink" href="index.html">Home</a> 
    </td>
  </tr>


  <tr>
    <td align="left" class = "headercell">
	  <a class="headerlink" href="about.html">About</a> 
    </td>
  </tr>


  <tr>
    <td align="left" class = "headercell">
	  <a class="headerlink" href="registration.html">Registration</a> 
    </td>
  </tr>


  <tr>
    <td align="left" class = "headercell">
	  <a class="headerlink" href="accommodation.html">Accommodation</a> 
    </td>
  </tr>


  <tr> 
    <td align="left" class = "headercell">
	  <a class="headerlink" href="program.html">Program</a>
    </td>
  </tr>

  <tr> 
    <td align="left" class = "headercell">
	  <a class="headerlink" href="http://www.alta.asn.au/events/altw2004/">Workshop</a>
    </td>
  </tr>

  <tr> 
    <td align="left" class = "headercell">
	  <a class="headerlink" href="http://www.assta.org/sst/2004/">SST 2004</a>
    </td>
  </tr>

  <tr> 
    <td align="left" class = "headercell">
	  <a class="headerlink" href="sponsorship.html">Sponsorship</a>
    </td>
  </tr>

  <tr> 
    <td align="left" class = "headercell">
	  <a class="headerlink" href="presenters.html">Information for Presenters</a>
    </td>
  </tr>

  <tr> 
    <td align="left" class = "headercell">
	  <a class="headerlink" href="venues.html">Venues</a>
    </td>
  </tr>

  <tr> 
    <td align="left" class = "headercell">
	  <a class="headerlink" href="poster.pdf">Poster</a>
    </td>
  </tr>

<tr><td>&nbsp;</td></tr>
</table>
<!-- End Macquarie navigation table -->
</td>

<td width="2%">&nbsp;</td>

'''


####################### Footer ##########################################
footer = '''
</td>

<td width="2%">&nbsp;</td>

</tr>
</table>
<!-- End of main table -->

<!-- Start of footer -->
<p class="footertext">
For any comments or questions about these pages please contact the <a href="mailto:diego@ics.mq.edu.au">ALTA secretary</a>.
</p>   
<!-- End of footer -->
<br>
Copyright 2003 ALTA. Last updated: '''+time.strftime("%a, %d %b %Y %H:%M:%S +0000", time.gmtime())+'''
</body>
</html>
'''

###################### Document Printing ###################################

print >>fileout, header

if fname == 'index.html':
   print >>fileout, "<td width=\"53%\">\n"
else:
   print >>fileout, "<td width=\"81%\">\n"
   


print >>fileout, body

print >>fileout, footer

