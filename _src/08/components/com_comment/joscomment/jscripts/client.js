var JOSC_http = (window.XMLHttpRequest ? new XMLHttpRequest : (window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : false));
var JOSC_operaBrowser = (navigator.userAgent.toLowerCase().indexOf("opera") != -1);
var JOSC_rsearchphrase_selection="any";
/* in case of modify */
var JOSC_userName = ''; 
var JOSC_userEmail = ''; 
var JOSC_userWebsite = '';
var JOSC_userNotify = '';
/* ***************** */
var JOSC_XmlErrorAlert = false; /* will be redefined by setting */
var JOSC_AjaxDebug = false; /* will be redefined by setting */
var JOSC_AjaxDebugLevel = 2; /* will be redefined by setting */

var JOSC_postREFRESH=false;

var JOSC_clientPC = navigator.userAgent.toLowerCase();
var JOSC_clientVer = parseInt(navigator.appVersion);

var JOSC_is_ie = ((JOSC_clientPC.indexOf("msie") != -1) && (JOSC_clientPC.indexOf("opera") == -1));
var JOSC_is_nav = ((JOSC_clientPC.indexOf('mozilla')!=-1) && (JOSC_clientPC.indexOf('spoofer')==-1)
	&& (JOSC_clientPC.indexOf('compatible') == -1) && (JOSC_clientPC.indexOf('opera')==-1)
	&& (JOSC_clientPC.indexOf('webtv')==-1) && (JOSC_clientPC.indexOf('hotjava')==-1));
var JOSC_is_moz = 0;

var JOSC_is_win = ((JOSC_clientPC.indexOf("win")!=-1) || (JOSC_clientPC.indexOf("16bit") != -1));
var JOSC_is_mac = (JOSC_clientPC.indexOf("mac")!=-1);

var JOSC_scrollTopPos = 0;
var JOSC_scrollLeftPos = 0;

function JOSC_insertAdjacentElement( object, where, parsedNode ) {
	if (!object.JOSCinsertAdjacentElement)
		object.insertAdjacentElement(where, parsedNode);
	else
		object.JOSCinsertAdjacentElement(where, parsedNode);
}

function JOSC_insertAdjacentHTML( object, where, htmlStr ) {
	if (!object.JOSCinsertAdjacentHTML)
		object.insertAdjacentHTML(where, htmlStr);
	else
		object.JOSCinsertAdjacentHTML(where, htmlStr);
}

if (typeof HTMLElement != "undefined" && !
	HTMLElement.prototype.JOSCinsertAdjacentElement) {
	HTMLElement.prototype.JOSCinsertAdjacentElement = function
		(where, parsedNode)
		{
			switch (where) {
				case 'beforeBegin':
					this.parentNode.insertBefore(parsedNode, this)
					break;
				case 'afterBegin':
					this.insertBefore(parsedNode, this.firstChild);
					break;
				case 'beforeEnd':
					this.appendChild(parsedNode);
					break;
				case 'afterEnd':
					if (this.nextSibling)
						this.parentNode.insertBefore(parsedNode, this.nextSibling);
					else this.parentNode.appendChild(parsedNode);
					break;
			}
		}

	HTMLElement.prototype.JOSCinsertAdjacentHTML = function
		(where, htmlStr)
		{
			var r = this.ownerDocument.createRange();
			r.setStartBefore(this);
			var parsedHTML = r.createContextualFragment(htmlStr);
			this.JOSCinsertAdjacentElement(where, parsedHTML)
		}

/*    HTMLElement.prototype.JOSCinsertAdjacentText = function
    (where, txtStr)
    {
        var parsedText = document.createTextNode(txtStr)
        this.JOSCinsertAdjacentElement(where, parsedText)
    }
    */
}

/***************************
 * F U N C T I O N S
 ***************************/
 
function JOSC_HTTPParam()
{
}

JOSC_HTTPParam.prototype.create = function(josctask, id)
{
	this.result = 'option=com_comment';
	this.insert('no_html', 1);
	var form = document.joomlacommentform;
	this.insert('component', form.component.value);
	this.insert('joscsectionid', form.joscsectionid.value);
	this.insert('josctask', josctask);
	this.insert('comment_id', id);
	this.insert('content_id', form.content_id.value);
	return this.result;
}

JOSC_HTTPParam.prototype.insert = function(name, value)
{
	this.result += '&' + name + '=' + value;
	return this.result;
}

JOSC_HTTPParam.prototype.encode = function(name, value)
{
	return this.insert(name, encodeURIComponent(value));
}

function JOSC_BusyImage()
{
}

JOSC_BusyImage.prototype.create = function(id)
{
	//	var form = document.joomlacommentform;
	var image = document.createElement('img');
	image.setAttribute('src', JOSC_template + '/images/busy.gif');
	image.setAttribute('id', id+"Image");
	var element = document.getElementById(id);
	if (!element.innerHTML) element.appendChild(image);
	JOSC_ajaxNotActive = false;
}

JOSC_BusyImage.prototype.destroy = function(id)
{
	var image = document.getElementById(id+"Image");
	image.parentNode.removeChild(image);
	JOSC_ajaxNotActive = true;
}

var JOSC_ajaxNotActive = true; /* will be set in create/destroy BusyImage */
var JOSC_busyImage = new JOSC_BusyImage();

function JOSC_ajaxSend(data, onReadyStateChange)
{
	document.joomlacommentform.bsend.disabled = true;
	JOSC_busyImage.create('JOSC_busypage');
	JOSC_busyImage.create('JOSC_busy');
	var URL = JOSC_ConfigLiveSite+'index.php';
	JOSC_http.open("POST", URL , true);
	JOSC_http.onreadystatechange = onReadyStateChange;
	JOSC_http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	if (JOSC_AjaxDebug) alert('###AJAXSEND:\n##URL=' + URL + ' ?' + data  + '\n##onReadyStateChange=' + onReadyStateChange);
	JOSC_http.send(data);
}

function JOSC_ajaxReady() {
	if (JOSC_http.readyState == 4) { /* received */
		JOSC_busyImage.destroy('JOSC_busy');
		JOSC_busyImage.destroy('JOSC_busypage');
		document.joomlacommentform.bsend.disabled = false;
		JOSC_addNew(1);
		if (JOSC_http.status == 200) {/* response is ok */
			if (JOSC_AjaxDebug) alert('AJAXREADY: OK !' );
			return true;
		} else {
			if (JOSC_AjaxDebug) alert('AJAXREADY: KO ! Status=' + JOSC_http.status );
			return false;
		}
	}
	return false;
}

function JOSC_goToAnchor(name)
{
	clearTimeout(self.timer);
	action = function()
	{
		var url = window.location.toString();
		var index = url.indexOf('#');
		if (index == -1) {
			window.location = url + '#' + name;
		}
		else {
			window.location = url.substring(0, index) + '#' + name;
		}
		if (JOSC_operaBrowser) window.location = '##';
	}
	if (JOSC_operaBrowser) self.timer = setTimeout(action, 50);
	else action();
}

function JOSC_refreshPage(msg, id) 
{
	if (msg) alert(msg);

	clearTimeout(self.timer);
	action = function()
	{
		var url = window.location.toString();
		var index = url.indexOf('?option=');
		if (index == -1) {
			var sep = '?';
		} /* SEF */
		else			 {
			var sep = '&';
		} /* normal */
		window.location = JOSC_linkToContent + sep + 'comment_id=' + id + '#josc' + id;
	//if (JOSC_operaBrowser) window.location = '##';
	}
	if (JOSC_operaBrowser) self.timer = setTimeout(action, 50);
	else action();
}

function JOSC_getXmlResponse(withalert) {
	/* return DOM (W3C) if no parsing xml error else null  (alert will show a javascript alert) */
	if (JOSC_http.responseXML && JOSC_http.responseXML.parseError &&(JOSC_http.responseXML.parseError.errorCode !=0)) {
		error = JOSC_getXmlError(withalert);
		return null;
	} else {
		if (JOSC_AjaxDebug) alert('###GETXMLRESPONSE:\n' + JOSC_http.responseText );
		/*    if (JOSC_operaBrowser && JOSC_AjaxDebug && JOSC_AjaxDebugLevel>1) {
         txt = '';
         for (prop in JOSC_http.responseXML)
         {
             txt = txt + '\n' + prop + '=' + JOSC_http.responseXML[prop];
         }
         alert('JOSC_getXmlResponse:http.responseXML='+txt);
    }*/
		return JOSC_http.responseXML;
	}
}

function JOSC_getXmlError(withalert) {
	if (JOSC_http.responseXML.parseError.errorCode !=0 ) {
		line = JOSC_http.responseXML.parseError.line;
		pos = JOSC_http.responseXML.parseError.linepos;
		error = JOSC_http.responseXML.parseError.reason;
		error = error + "Contact the support ! and send the following informations:\n error is line " + line + " position " + pos;
		error = error + " >>" + JOSC_http.responseXML.parseError.srcText.substring(pos);
		error = error + "\nGLOBAL:" + JOSC_http.responseText;
		if (withalert)
			alert(error);
		return error;
	} else {
		return "";
	}
}

/*
 * Form type function
 */
function JOSC_modifyForm(formTitle, buttonValue, onClick)
{
	document.getElementById('CommentFormTitle').innerHTML = formTitle;
	button = document.joomlacommentform.bsend;
	button.value = buttonValue;
	button.onclick = onClick;
}

function JOSC_xmlValue(xmlDocument, tagName)
{
	try {
		var result = xmlDocument.getElementsByTagName(tagName).item(0).firstChild.data;
	}
	catch(e) {
		var result = '';
	}
	return result;
}

function JOSC_removePost(post)
{
	document.getElementById('Comments').removeChild(post);
}

/********************* 
 * ajax call functions
 */
function JOSC_deleteComment(id)
{
	if (window.confirm(_JOOMLACOMMENT_MSG_DELETE)) {
		var data = new JOSC_HTTPParam().create('ajax_delete', id);
		JOSC_ajaxSend(data, function()
		{
			if (JOSC_ajaxReady()) {
				if (JOSC_http.responseText != '') alert(JOSC_http.responseText);
				else JOSC_removePost(document.getElementById('post' + id));
			}
		}
		);
	}
}

function JOSC_deleteAll()
{
	if (window.confirm(_JOOMLACOMMENT_MSG_DELETEALL)) {
		var form = document.joomlacommentform;
		var param = new JOSC_HTTPParam();
		param.create('ajax_delete_all', -1);
		JOSC_ajaxSend(param.insert('content_id',form.content_id.value), function()
		{
			if (JOSC_ajaxReady()) {
				if (JOSC_http.responseText != '') alert(JOSC_http.responseText);
				else {
					/* JOSC_addNew();  why ? */
					document.getElementById('Comments').innerHTML='';
				}
			}
		}
		);
	}
}


function JOSC_editComment(id)
{
	JOSC_modifyForm(_JOOMLACOMMENT_EDITCOMMENT, _JOOMLACOMMENT_EDIT,
		function(event)
		{
			JOSC_editPost(id, -1);
		}
		);
	JOSC_goToAnchor('CommentForm');
	var data = new JOSC_HTTPParam().create('ajax_modify', id);
	JOSC_ajaxSend(data, JOSC_editResponse);
}

function JOSC_publishUnpublishComment(id, publish)
{
	if(publish == 0) {
		var data = new JOSC_HTTPParam().create('ajax_unpublish', id);
	} else {
		var data = new JOSC_HTTPParam().create('ajax_publish', id);
	}
	JOSC_ajaxSend(data, JOSC_getCommentsResponse);
}

function JOSC_quote(id)
{
	var data = new JOSC_HTTPParam().create('ajax_quote', id);
	JOSC_goToAnchor('CommentForm');
	JOSC_ajaxSend(data, JOSC_quoteResponse);
}

function JOSC_voting(id, yes_no)
{
	var data = new JOSC_HTTPParam().create('ajax_voting_' + yes_no, id);
	JOSC_ajaxSend(data, JOSC_votingResponse);
}

function JOSC_reloadCaptcha() {
	if (JOSC_captchaType=="default") {
		var data = new JOSC_HTTPParam().create('ajax_reload_captcha', 0);
		JOSC_ajaxSend(data, JOSC_editPostResponse);
	} else if (JOSC_captchaType=="recaptcha") {
		Recaptcha.reload();
	}
}

function JOSC_searchForm()
{
	JOSC_removeSearchResults();
	var searchform = document.joomlacommentsearch;
	var form = document.joomlacommentform;
	if (searchform) {
		searchform.parentNode.removeChild(searchform);
		if (!JOSC_operaBrowser) document.joomlacommentsearch = null;
	} else {
		var param = new JOSC_HTTPParam();
		param.create('ajax_insert_search', 0);
		JOSC_ajaxSend(param.insert('content_id', form.content_id.value), JOSC_searchFormResponse);
	}
}

function JOSC_search()
{
	JOSC_removeSearchResults();
	var keyword = document.joomlacommentsearch.tsearch.value;
	if (keyword=='') return 0;
	var param = new JOSC_HTTPParam();
	param.create('ajax_search', 0);
	param.encode('search_keyword', keyword)
	JOSC_ajaxSend(param.insert('search_phrase',JOSC_rsearchphrase_selection), JOSC_searchResponse);
}

function validate(email) {
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var address = email;
	if(reg.test(address) == false) {
		alert(_JOOMLACOMMENT_FORMVALIDATE_INVALID_EMAIL);
		return false;
	}
	return true;
}

function JOSC_editPost(id, parentid) {
	var form = document.joomlacommentform;
	if(typeof(form.temail) != 'undefined') {
		if(form.temail.value != '' && !form.temail.disabled) {
			if (!validate(form.temail.value, form)) {
				return 0;
			}
		}
	}
	if (form.tcomment.value == '' ) {
		alert(_JOOMLACOMMENT_FORMVALIDATE);
		return 0;
	}
	if  ( document.getElementsByName('tnotify')[0]  && document.getElementsByName('temail')[0] ) {
		if ( form.tnotify.selectedIndex && form.temail.value == '') {
			alert(_JOOMLACOMMENT_FORMVALIDATE_EMAIL);
			return 0;
		}
	}
	if (JOSC_captchaEnabled) {
		if (JOSC_captchaType=="default" && form.security_try.value == '') {
			alert(_JOOMLACOMMENT_FORMVALIDATE_CAPTCHA);
			return 0;
		} else if (JOSC_captchaType=="recaptcha" && form.recaptcha_response_field.value == '') {
			alert(_JOOMLACOMMENT_FORMVALIDATE_CAPTCHA);
			return 0;
		}
	}
	if (JOSC_ajaxEnabled) {
		var param = new JOSC_HTTPParam();
		param.create(id == -1 ? 'ajax_insert' : 'ajax_edit', id);
		param.insert('content_id', form.content_id.value);
		if (JOSC_captchaEnabled) {
			if (JOSC_captchaType=="default") {
				param.insert('security_try', form.security_try.value);
				param.insert('security_refid', form.security_refid.value);
			} else if (JOSC_captchaType=="recaptcha") {
				param.insert('recaptcha_challenge_field', form.recaptcha_challenge_field.value);
				param.insert('recaptcha_response_field', form.recaptcha_response_field.value);
			}
		}
		if (parentid != -1) param.insert('parent_id', parentid);
		param.encode('tname', form.tname.value);
		/* optional */
		if (document.getElementsByName('tnotify')[0])  {
			if (form.tnotify.selectedIndex) param.encode('tnotify', '1'); else param.encode('tnotify', '0');
		};
		if (document.getElementsByName('temail')[0])    param.encode('temail', form.temail.value);
		if (document.getElementsByName('twebsite')[0])  param.encode('twebsite', form.twebsite.value);
		/************/
		param.encode('ttitle', form.ttitle.value);
		JOSC_ajaxSend(param.encode('tcomment', form.tcomment.value), JOSC_editPostResponse);
	} else {
		/* should we use JOSC_ConfigLiveSite ? */
		form.action = JOSC_ConfigLiveSite+'/index.php?option=com_comment&josctask=noajax';
		form.submit();
	}
}

function JOSC_getComments(id, limitstart) 
{
    
	var form = document.joomlacommentform;
	if (JOSC_ajaxEnabled && JOSC_ajaxNotActive)
	{
		JOSC_ShowHide('', 'joscPageNavNoLink', 'joscPageNavLink');
		var param = new JOSC_HTTPParam();
		param.create('ajax_getcomments', id);
		param.insert('content_id',form.content_id.value);
		JOSC_ajaxSend(param.insert('josclimitstart', limitstart), JOSC_getCommentsResponse);
	}
}

/*
 * END of ajax call functions
 */
 
/********************
 * response functions
 */
function JOSC_editResponse()
{
	if (JOSC_ajaxReady()) {
		var form = document.joomlacommentform;
		var xmlDocument = JOSC_getXmlResponse(JOSC_XmlErrorAlert);
		; /*JOSC_http.responseXML;*/
		if (xmlDocument) {

			JOSC_userName = form.tname.value;
			form.tname.value = JOSC_xmlValue(xmlDocument, 'name');

			form.ttitle.value = JOSC_xmlValue(xmlDocument, 'title');
			form.tcomment.value = JOSC_xmlValue(xmlDocument, 'comment');

			/* optional values of the templates ! */
			if (document.getElementsByName('tnotify')[0]) {
				JOSC_userNotify = form.tnotify.selectedIndex;
				form.tnotify.selectedIndex = new Boolean(JOSC_xmlValue(xmlDocument, 'notify')*1);
			}
			if (document.getElementsByName('temail')[0]) {
				JOSC_userEmail = form.temail.value;
				form.temail.value = JOSC_xmlValue(xmlDocument, 'email');
			}
			if (document.getElementsByName('twebsite')[0]) {
				JOSC_userWebsite = form.twebsite.value;
				form.twebsite.value = JOSC_xmlValue(xmlDocument, 'website');
			}
		/* ********************** */
		} else {
			form.tcomment.value = 'failed to retrieve datas';
		}
		if (self.JOSC_afterAjaxResponse) JOSC_afterAjaxResponse('response_edit');
	}
}

function JOSC_quoteResponse()
{
	if (JOSC_ajaxReady()) {
		var form = document.joomlacommentform;
		var xmlDocument = JOSC_getXmlResponse(true);
		if (xmlDocument) {
			name = JOSC_xmlValue(xmlDocument, 'name');
			if (name == '') name = _JOOMLACOMMENT_ANONYMOUS;
			if (form.ttitle.value == '') form.ttitle.value = 're: ' +
				JOSC_xmlValue(xmlDocument, 'title');
			form.tcomment.value += '[quote=' + name + ']' +
			JOSC_xmlValue(xmlDocument, 'comment') + '[/quote]';
		} else {
			form.tcomment.value = 'failed to retrieve datas';
		}
		if (self.JOSC_afterAjaxResponse) JOSC_afterAjaxResponse('response_quote');
	}
}

function JOSC_votingResponse()
{
	if (JOSC_ajaxReady()) {
		var form = document.joomlacommentform;
		var xmlDocument = JOSC_getXmlResponse(JOSC_XmlErrorAlert); /*JOSC_http.responseXML;*/
		var id = JOSC_xmlValue(xmlDocument, 'id');
		var yes = JOSC_xmlValue(xmlDocument, 'yes');
		var no = JOSC_xmlValue(xmlDocument, 'no');
		document.getElementById('yes' + id).innerHTML = yes;
		document.getElementById('no' + id).innerHTML = no;
		if (self.JOSC_afterAjaxResponse) JOSC_afterAjaxResponse('response_voting');
	}
}

function JOSC_editPostResponse()
{
	if (JOSC_ajaxReady()) {
		var form = document.joomlacommentform;
		var element = document.getElementById('Comments');
		var xmlDocument = JOSC_getXmlResponse(true); /*JOSC_http.responseXML;*/
		if (!xmlDocument) {
			return 0;
		}
		var id = JOSC_xmlValue(xmlDocument, 'id');
		var captcha = JOSC_xmlValue(xmlDocument, 'captcha');
		if (captcha) {
			JOSC_refreshCaptcha(captcha);
			if (id == 'captchaalert') {
				alert(_JOOMLACOMMENT_FORMVALIDATE_CAPTCHA_FAILED);
				return 0;
			}
			if (id == 'captcha') {
				return 0;
			}
		}
		anchor = 'josc' + id;
		var idsave = id;
		id = 'post' + id;
		var body = JOSC_xmlValue(xmlDocument, 'body');
		var post = document.getElementById(id);
		var after = JOSC_xmlValue(xmlDocument, 'after');
		JOSC_clearInputbox();
		var noerror = JOSC_xmlValue(xmlDocument, 'noerror');
		if (noerror==0) {
			alert(_JOOMLACOMMENT_REQUEST_ERROR);
			form.tcomment.value=JOSC_http.responseText;
			return 0;
		}
		var published = JOSC_xmlValue(xmlDocument, 'published');
		if (published==0) {
			alert(_JOOMLACOMMENT_BEFORE_APPROVAL);
			form.tcomment.value="";
			if (self.JOSC_afterAjaxResponse) JOSC_afterAjaxResponse('response_approval');
			return 0;
		}
		if (post) {
			var indent = post.style.marginLeft;
			JOSC_insertAdjacentHTML(post, 'beforeBegin', body);
			JOSC_removePost(post);
			newPost = document.getElementById(id);
			newPost.style.marginLeft = indent;
			JOSC_modifyForm(_JOOMLACOMMENT_WRITECOMMENT, _JOOMLACOMMENT_SENDFORM,
				function(event)
				{
					JOSC_editPost(-1, -1);
				});
			form.tname.value = JOSC_userName;
			if (document.getElementsByName('temail')[0])   {
				form.temail.value = JOSC_userEmail;
			}
			if (document.getElementsByName('website')[0])   {
				form.website.value = JOSC_userWebsite;
			}
			if (self.JOSC_afterAjaxResponse) {
				JOSC_afterAjaxResponse('response_editpost');
			}
		} else {
			if (!after || after == -1) {
				if (JOSC_sortDownward != 0) {
					if (JOSC_postREFRESH) {
						JOSC_refreshPage(_JOOMLACOMMENT_MSG_NEEDREFRESH, idsave);
					}else {
						JOSC_insertAdjacentHTML(element, 'afterBegin', body);
					}
				} else {
					if (JOSC_postREFRESH) {
						JOSC_refreshPage(_JOOMLACOMMENT_MSG_NEEDREFRESH, idsave);
					} else {
						JOSC_insertAdjacentHTML(element, 'beforeEnd', body);
					}
				}
			} else {
				if (document.getElementById('post' + after)) {
					JOSC_insertAdjacentHTML(document.getElementById('post' + after), 'afterEnd', body);
				}else{
					/* pagination or post has been deleted or new one from another users...=> refresh */
					JOSC_refreshPage(_JOOMLACOMMENT_MSG_NEEDREFRESH, idsave);
				}
			}
		
			if (self.JOSC_afterAjaxResponse) {
				JOSC_afterAjaxResponse('response_posted');
			}
		}
		JOSC_goToAnchor(anchor);
	//JOSC_refreshPage('', idsave);
	// TODO : Below line hides comment form after submit. Make something useful like a 'Thank you for your comment' option.
	//document.getElementById('joomlacommentform').innerHTML = '';
	}
}

function JOSC_getCommentsResponse() {

	//JOSC_ShowHide('', 'joscPageNavLink', 'joscPageNavNoLink');
    
	if (JOSC_ajaxReady()) {


		JOSC_resetFormPos(); /* if reply... */
			
		var element = document.getElementById('Comments');
		var elementPN = document.getElementById('joscPageNav');

		var xmlDocument = JOSC_getXmlResponse(true); /*JOSC_http.responseXML;*/
		if (!xmlDocument) {
			return 0;
		}

		element.innerHTML='';
		elementPN.innerHTML='';

		var body 	= JOSC_xmlValue(xmlDocument, 'body');
		var pagenav	= JOSC_xmlValue(xmlDocument, 'pagenav');

		if (JOSC_sortDownward != 0)
			JOSC_insertAdjacentHTML(element, 'afterBegin', body);
		else {
			JOSC_insertAdjacentHTML(element, 'beforeEnd', body);
		}
		JOSC_insertAdjacentHTML(elementPN, 'afterBegin', pagenav);

		if (self.JOSC_afterAjaxResponse) JOSC_afterAjaxResponse('response_getcomments');
	}
}

function JOSC_searchFormResponse()
{
	if (JOSC_ajaxReady()) {
		form = JOSC_http.responseText;
		if (form != '') {
			JOSC_insertAdjacentHTML(document.getElementById('CommentMenu'), 'afterEnd', form);
			if (self.JOSC_afterAjaxResponse) JOSC_afterAjaxResponse('response_searchform');
		}
	}
}

function JOSC_searchResponse()
{
	if (JOSC_ajaxReady()) {
		form = JOSC_http.responseText;
		if (form != '') {
			JOSC_insertAdjacentHTML(document.joomlacommentsearch, 'afterEnd', form);
			if (self.JOSC_afterAjaxResponse) JOSC_afterAjaxResponse('response_search');
		}
	}
}

/*
 * END of response functions
 */

/*
 * Template functions
 */
//function JOSC_goToPost(contentid, id)
//{
//	var form = document.joomlacommentform;
//	if (form.content_id.value==contentid) JOSC_goToAnchor('josc'+id); /* not correct in case of pagination. use JOSC_viewPost */
//	else window.location = 'index.php?option=' + form.component + '&task=view&id=' + contentid + '#josc' + id;
//	if (JOSC_operaBrowser) window.location = '##';
//}
//
//function JOSC_viewPost(contentid, id, itemid)
//{
//	var form = document.joomlacommentform;
//	window.location = 'index.php?option=' + form.component + '&task=view&id=' + contentid + (itemid ? ('&Itemid='+itemid) : '') + '&comment_id=' + id + '#josc' + id;
//	if (navigator.userAgent.toLowerCase().indexOf("opera") != -1) window.location = '##';
//}

function JOSC_reply(id)
{
	var form = document.joomlacommentform;
	var post = document.getElementById('post' + id);
	var postPadding = post.style.paddingLeft.replace('px','')*1;
	form.style.paddingLeft = ( postPadding + 20 ) + 'px';
	JOSC_modifyForm(_JOOMLACOMMENT_WRITECOMMENT, _JOOMLACOMMENT_SENDFORM,
		function(event)
		{
			JOSC_editPost(-1, id);
		});
	JOSC_insertAdjacentElement(post, 'afterEnd', form);
	if (self.JOSC_afterAjaxResponse) JOSC_afterAjaxResponse('response_reply');
}

function JOSC_resetFormPos() {
	var form = document.joomlacommentform;
	var formpos = document.getElementById('JOSC_formpos');
	if (form.parentNode.id != 'comment' || (formpos && form.parentNode.id != 'JOSC_formpos'))
	{
		form.style.paddingLeft = '0px';
		form.bsend.onclick = function(event)
		{
			JOSC_editPost(-1, -1);
		};
		if (!formpos) {
			JOSC_insertAdjacentElement(document.getElementById('Comments'), 'afterEnd', form);
		} else {
			JOSC_insertAdjacentElement(formpos, 'afterEnd', form);
		}

	}
}
		    
function JOSC_insertUBBTag(tag) {
	if (tag == "url") {
		JOSC_insertTags('[url=', ']here[/url]');
	} else {
		JOSC_insertTags('[' + tag + ']', '[/' + tag + ']');
	}
}

function JOSC_fontColor(){
	var color = document.joomlacommentform.menuColor.selectedIndex;
	switch (color){
		case 0:
			color='';
			break;
		case 1:
			color='aqua';
			break;
		case 2:
			color='black';
			break;
		case 3:
			color='blue';
			break;
		case 4:
			color='fuchsia';
			break;
		case 5:
			color='gray';
			break;
		case 6:
			color='green';
			break;
		case 7:
			color='lime';
			break;
		case 8:
			color='maroon';
			break;
		case 9:
			color='navy';
			break;
		case 10:
			color='olive';
			break;
		case 11:
			color='purple';
			break;
		case 12:
			color='red';
			break;
		case 13:
			color='silver';
			break;
		case 14:
			color='teal';
			break;
		case 15:
			color='white';
			break;
		case 16:
			color='yellow';
			break;
	}
	if (color!='') JOSC_insertTags('[color='+color+']','[/color]');
}

function JOSC_fontSize()
{
	var size = document.joomlacommentform.menuSize.selectedIndex;
	switch (size) {
		case 0:
			size = '';
			break;
		case 1:
			size = 'x-small';
			break;
		case 2:
			size = 'small';
			break;
		case 3:
			size = 'medium';
			break;
		case 4:
			size = 'large';
			break;
		case 5:
			size = 'x-large';
			break;
	}
	if (size != '') JOSC_insertTags('[size=' + size + ']', '[/size]');
}

function JOSC_emoticon(icon)
{
	var txtarea = document.joomlacommentform.tcomment;
	JOSC_scrollToCursor(txtarea, 0);
	txtarea.focus();
	JOSC_pasteAtCursor(txtarea, ' ' + icon + ' ');
	JOSC_scrollToCursor(txtarea, 1);
}
/*
 * END of template function
 */
 
/*
 * ALL OTHERS UTILS FUNCTION
 */
function JOSC_insertTags(bbStart, bbEnd) {
	var txtarea = document.joomlacommentform.tcomment;
	JOSC_scrollToCursor(txtarea, 0);
	txtarea.focus();
	if ((JOSC_clientVer >= 4) && JOSC_is_ie && JOSC_is_win) {
		theSelection = document.selection.createRange().text;
		if (theSelection) {
			// IE8
			if (bbStart == "[url=") {
				if (theSelection.substring(7, 4) != "://") theSelection = "http://" + theSelection; // append guessed protocol if not detected
			}
			document.selection.createRange().text = bbStart + theSelection + bbEnd;
			theSelection = '';
			return;
		} else {
			JOSC_pasteAtCursor(txtarea, bbStart + bbEnd);
		}
	} else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0)) {
		// Firefox 3.x, Opera 9.x
		var selLength = txtarea.textLength;
		var selStart = txtarea.selectionStart;
		var selEnd = txtarea.selectionEnd;
		var s1 = (txtarea.value).substring(0,selStart);
		var s2 = (txtarea.value).substring(selStart, selEnd)
		var s3 = (txtarea.value).substring(selEnd, selLength);
		if (bbStart == "[url=") {
			if (s2.substring(7, 4) != "://") s2 = "http://" + s2; // append guessed protocol if not detected
		}
		txtarea.value = s1 + bbStart + s2 + bbEnd + s3;
		txtarea.selectionStart = selStart + (bbStart.length + s2.length + bbEnd.length);
		txtarea.selectionEnd = txtarea.selectionStart;
		JOSC_scrollToCursor(txtarea, 1);
		return;
	} else {
		JOSC_pasteAtCursor(txtarea, bbStart + bbEnd);
		JOSC_scrollToCursor(txtarea, 1);
	}
}

function JOSC_scrollToCursor(txtarea, action) {
	if (JOSC_is_nav) {
		if (action == 0) {
			JOSC_scrollTopPos = txtarea.scrollTop;
			JOSC_scrollLeftPos = txtarea.scrollLeft;
		} else {
			txtarea.scrollTop = JOSC_scrollTopPos;
			txtarea.scrollLeft = JOSC_scrollLeftPos;
		}
	}
}

function JOSC_pasteAtCursor(txtarea, txtvalue) {
	if (document.selection) {
		var sluss;
		txtarea.focus();
		sel = document.selection.createRange();
		sluss = sel.text.length;
		sel.text = txtvalue;
		if (txtvalue.length > 0) {
			sel.moveStart('character', -txtvalue.length + sluss);
		}
	} else if (txtarea.selectionStart || txtarea.selectionStart == '0') {
		var startPos = txtarea.selectionStart;
		var endPos = txtarea.selectionEnd;
		txtarea.value = txtarea.value.substring(0, startPos) + txtvalue + txtarea.value.substring(endPos, txtarea.value.length);
		txtarea.selectionStart = startPos + txtvalue.length;
		txtarea.selectionEnd = startPos + txtvalue.length;
	} else {
		txtarea.value += txtvalue;
	}
}

function JOSC_clearInputbox()
{
	var form = document.joomlacommentform;
	form.ttitle.value = '';
	form.tcomment.value = '';
}

function JOSC_refreshCaptcha(captcha) {
	if (JOSC_captchaType=="default") {
		document.getElementById('captcha').innerHTML = captcha;
		document.joomlacommentform.security_try.value = '';
	} else if (JOSC_captchaType=="recaptcha") {
		Recaptcha.reload();
	}
}

function JOSC_removeSearchResults()
{
	var searchResults = document.getElementById('SearchResults');
	if (searchResults) searchResults.parentNode.removeChild(searchResults);
}

function JOSC_addNew(nomove)
{
	JOSC_resetFormPos();
	if (!nomove) {
		JOSC_goToAnchor('CommentForm');
	}
}

function JOSC_ShowHide(emptyvalue, showId, hideId) {

	if (showId && showId!=emptyvalue) {
		document.getElementById(showId).style.visibility='visible';
		document.getElementById(showId).style.display = '';
	}
	if (hideId && hideId!=emptyvalue) {
		document.getElementById(hideId).style.visibility = 'hidden';
		document.getElementById(hideId).style.display = 'none';
	}
	return(showId);
}

function JOSC_toogle(ElementId) {
     		    
	if (ElementId) {
		if (document.getElementById(ElementId).style.visibility=='hidden') {
			document.getElementById(ElementId).style.visibility='visible';
			document.getElementById(ElementId).style.display = '';
		} else {
			document.getElementById(ElementId).style.visibility = 'hidden';
			document.getElementById(ElementId).style.display = 'none';
		}
	}
}

/*
 * return 0 if nothing done
 * return 1 if hidden->visible
 * return 2 if visible->hidden
 */
function JOSC_toogleR(ElementId) {
     		    
	if (ElementId) {
		if (document.getElementById(ElementId).style.visibility=='hidden') {
			document.getElementById(ElementId).style.visibility='visible';
			document.getElementById(ElementId).style.display = '';
			return 1;
		} else {
			document.getElementById(ElementId).style.visibility = 'hidden';
			document.getElementById(ElementId).style.display = 'none';
			return 2;
		}
	} else return 0;
} 