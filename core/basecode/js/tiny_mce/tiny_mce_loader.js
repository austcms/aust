tinyMCE.init({
	theme : "advanced",
	mode : "exact",
	    relative_urls : false,
	    convert_urls : 0, // default 1
    elements : "jseditor",
	theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,forecolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,|,insertdate,inserttime,preview",
	theme_advanced_buttons3_add : "pastetext,pasteword,selectall",
	theme_advanced_buttons3 : "", //"insertimage",
	theme_advanced_buttons4 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	language : "pt",
	plugins : "paste,safari" // "imagemanager,paste,safari"
});
