window.addEvent('domready', function() {
	if($('tname').value == '' && !$('tname').disabled) {
		$('tname').value = 'enter your name...';
	}
	if($('temail').value == '' && !$('temail').disabled) {
		$('temail').value = 'enter your e-mail address...';
	}
	if($('twebsite').value == '') {
		$('twebsite').value = 'enter your site URL...';
	}

	comments = $('Comments').getChildren();

	for(var i=1; i < comments.length; i++) {
		if(i % 2 ) {
			commentClass = comments[i].className
			comments[i].className = commentClass + ' odd'
		}
	}
});
var RecaptchaOptions = {
	theme : 'clean'
};

function JOSC_editPost(id, parentid) {
	var form = document.joomlacommentform;

	if (form.tcomment.value == '' || form.tcomment.value == 'enter your message here...' ) {
		alert(_JOOMLACOMMENT_FORMVALIDATE);
		return 0;
	}

	if($('tname').value == '' || $('tname').value == 'enter your name...' ) {
		alert ('You need to input a name');
		return 0;
	}

	if(!form.temail.disabled) {
		if (!validate(form.temail.value, form)) {
			return 0;
		}

	}
	if($('twebsite').value == '' || $('twebsite').value == 'enter your site URL...' ) {
		$('twebsite').value = '';
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
}
