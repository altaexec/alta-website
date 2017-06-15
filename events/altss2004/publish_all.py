#! /usr/local/bin/python

publish_command = "publish.py"

import os

for file in ["index.html", "about.html", "program.html", "sponsorship.html",
             "accommodation.html", "registration.html", "presenters.html",
             "venues.html"]:
    print file
    os.system(publish_command + ' ' + file)
