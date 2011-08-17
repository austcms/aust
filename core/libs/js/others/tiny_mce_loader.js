tinyMCE.init({
	theme : "advanced",
	mode : "exact",
		relative_urls : false,
		convert_urls : 0, // default 1
	elements : "jseditor"+elementsToLoad,
	theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,forecolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,cleanup,|,insertdate,inserttime,preview",
	theme_advanced_buttons3_add : "pastetext,pasteword,selectall",
	theme_advanced_buttons3 : "insertimage,code,pagebreak,|,tablecontrols",//"insertimage",
	theme_advanced_buttons4 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	theme_advanced_resizing_min_height : 200, // altura mínima
	pagebreak_separator : '<br clear="all" class="pagebreak" />',
	height : '350',
	convert_fonts_to_spans : true,
	font_size_style_values : "8pt,10pt,12pt,14pt,18pt,24pt,36pt",
	inline_styles: false,
	extended_valid_elements : "embed,param,object,iframe",
//	entity_encoding: "raw",
	language : "pt",
	plugins : 'safari,paste,pagebreak,table'+pluginsToLoad // imagemanager
});
