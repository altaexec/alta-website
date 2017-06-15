function show_properties(obj)
{
	for (element in obj){
		document.write(element+"<br />");
	}
}

function movedown(listbox)
{
	var lb1 = -1; // index of selected listbox
	for (i = 0; i < listbox.length; i++)
	{
		if (listbox[i].selectedIndex > -1)
		{
			lb1 = i;
		}
	}
	// this is the list box of the to-be-swapped cell
	var lb2 = lb1;
	// if index is too high, go to next box
	if ((listbox[lb1].selectedIndex + 1) == listbox[lb1].length)
		lb2 = (lb1 + 1) % listbox.length;
	
	var lbs1 = listbox[lb1].selectedIndex;
	var lbs2 = (lbs1 + 1) % listbox[lb1].length ;
	
	// Swap text
	var text = listbox[lb1].options[lbs1].text;
	listbox[lb1].options[lbs1].text = listbox[lb2].options[lbs2].text;
	listbox[lb2].options[lbs2].text = text;
	
	// Swap value
	var value = listbox[lb1].options[lbs1].value;
	listbox[lb1].options[lbs1].value = listbox[lb2].options[lbs2].value;
	listbox[lb2].options[lbs2].value = value;
	
	// Swap colour
	var color = listbox[lb1].options[lbs1].style['color'];
	listbox[lb1].options[lbs1].style['color'] = listbox[lb2].options[lbs2].style['color'];
	listbox[lb2].options[lbs2].style['color'] = color;
	
	listbox[lb1].selectedIndex = -1;
	listbox[lb2].selectedIndex = lbs2;
}

function moveup(listbox)
{
	var lb1 = -1; // index of selected listbox
	for (i = 0; i < listbox.length; i++)
	{
		if (listbox[i].selectedIndex > -1)
		{
			lb1 = i;
		}
	}
	// this is the list box of the to-be-swapped cell
	var lb2 = lb1;
	// if index is too low, go to previous box
	if (listbox[lb1].selectedIndex - 1 < 0)
		lb2 = (lb1 + listbox.length - 1) % listbox.length;
	
	var lbs1 = listbox[lb1].selectedIndex;
	var lbs2 = lbs1 - 1;
	if (lbs2 < 0)
		lbs2 = listbox[lb2].length - 1;
	
	// Swap text
	var text = listbox[lb1].options[lbs1].text;
	listbox[lb1].options[lbs1].text = listbox[lb2].options[lbs2].text;
	listbox[lb2].options[lbs2].text = text;
	
	// Swap value
	var value = listbox[lb1].options[lbs1].value;
	listbox[lb1].options[lbs1].value = listbox[lb2].options[lbs2].value;
	listbox[lb2].options[lbs2].value = value;
	
	// Swap colour
	var color = listbox[lb1].options[lbs1].style['color'];
	listbox[lb1].options[lbs1].style['color'] = listbox[lb2].options[lbs2].style['color'];
	listbox[lb2].options[lbs2].style['color'] = color;
	
	listbox[lb1].selectedIndex = -1;
	listbox[lb2].selectedIndex = lbs2;
}

function clearothers(theList, allLists)
{
	for (var i = 0; i < allLists.length; i++)
	{
		if (allLists[i].id != theList.id)
		{
			allLists[i].selectedIndex = -1;
		}
	}
}


function process(f)
{
	var result = "";
	for (var i = 0; i < f.ListBox.length; i++)
	{
		if (f.ListBox[i].nodeName != "SELECT") continue;
		result += f.ListBox[i].id+"= ";
		for (var j = 0; j < f.ListBox[i].length; j++)
		{
			result += f.ListBox[i].options[j].value+" ";
		}
		result += ";";
	}
	f.NewOrder.value = result;
}
