var highlightedHeader = "header1";

/*
window.onload = function() {
	highlightHeader(highlightedHeader);
};

var isScrolling;
window.onscroll = function() {
	window.clearTimeout(isScrolling);
	isScrolling = setTimeout(function() {
		onWindowScroll(); //scrolling has stopped, run ops once
	}, 75);
};
*/

function onSidebarClick(event)
{
	highlightedHeader = this.getAttribute("href");
	highlightHeader(highlightedHeader);
}

function onWindowScroll()
{
	var newHighlightedHeader = highlightedHeader;
	var anchors = document.getElementsByTagName('a');
	for(var i = 0; i < anchors.length; i++)
	{
		if(anchors[i].id.indexOf("header") == -1)
			continue;

		var anchorY = anchors[i].getBoundingClientRect().top;
		if(anchorY > 40)
			break;
		
		newHighlightedHeader = anchors[i].id;
	}
	if(newHighlightedHeader != null && newHighlightedHeader != highlightedHeader)
	{
		highlightedHeader = newHighlightedHeader;
		highlightHeader(newHighlightedHeader);
	}
}

function highlightHeader(headerId)
{
	var anchors = document.getElementsByTagName('a');
	for(var i = 0; i < anchors.length; i++)
	{
		if(anchors[i].id != null && anchors[i].id.length > 0)
		{
			/*if(anchors[i].id == headerId)
			{
				anchors[i].parentNode.classList.add("highlight-text");
				anchors[i].parentNode.nextElementSibling.classList.add("highlight-section");
			}
			else
			{
				anchors[i].parentNode.classList.remove("highlight-text");
				anchors[i].parentNode.nextElementSibling.classList.remove("highlight-section");
			}*/
		}
		else
		{
			if(anchors[i].getAttribute("href") == "#"+headerId)
			{
				anchors[i].classList.add("highlight");
				scrollSidebarTo(anchors[i]);
			}
			else
				anchors[i].classList.remove("highlight");
		}
	}	
}

function scrollSidebarTo(element)
{
	var div = document.getElementsByClassName("sidebar")[0];
	var divRect = div.getBoundingClientRect();
	var elementYInDiv = element.getBoundingClientRect().top - divRect.top;
	if(elementYInDiv > 0 && elementYInDiv < 100)
		return;
	var contentHeight = div.scrollHeight;
	var viewHeight = divRect.height;
	var maxScroll = contentHeight - viewHeight;
	animateScrollSidebarTarget = div.scrollTop + elementYInDiv - 50;
	animateScrollSidebar();
}

var animateScrollSidebarTarget = null; //so don't have multiple running at once
function animateScrollSidebar() {
	if(animateScrollSidebarTarget == null)
		return;
	var div = document.getElementsByClassName("sidebar")[0];
	var divMaxScroll = div.scrollHeight - div.getBoundingClientRect().height;
	if(animateScrollSidebarTarget > divMaxScroll)
		animateScrollSidebarTarget = divMaxScroll;
	if(div.scrollTop == animateScrollSidebarTarget)
		return;
	var movementUnit = 15;
	if(Math.abs(div.scrollTop - animateScrollSidebarTarget) < movementUnit)
	{
		div.scrollTop = animateScrollSidebarTarget;
		return;
	}
	if(div.scrollTop > animateScrollSidebarTarget)
		div.scrollTop -= movementUnit;
	else
		div.scrollTop += movementUnit;
	
    setTimeout(function() {
        animateScrollSidebar();
    }, movementUnit);
}
