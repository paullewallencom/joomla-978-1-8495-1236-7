var JOSCmySlide = new Fx.Slide('JOSCMTtexttoogle');
$('JOSCMTstretchertoogleFORM').addEvent('click', function(e){
	e = new Event(e);
	JOSCmySlide.toggle();
	e.stop();
});
var JOSCmySlide2 = new Fx.Slide('JOSCMTCommentstoogle');
$('JOSCMTstretchertoogleCOMMENTS').addEvent('click', function(e){
	e = new Event(e);
	JOSCmySlide2.toggle();
	e.stop();
});
function JOSC_afterAjaxResponse(action) {
	switch(action) {
		case 'show':
		case 'response_reply':
		case 'response_edit':
		case 'response_quote':
			JOSCmySlide.show();
			return 0;
			break;
		case 'hide':
		case 'response_editpost':
		case 'response_posted':
		case 'response_approval':
			JOSCmySlide.hide();
			return 0;
			break;
		/* because of bug with IE ...(?)
			case 'response_searchform':
			case 'response_getcomments':
				JOSCmySlide.show();
                JOSCmySlide.hide();
				return 0;
                break;
*/
		default:
			return 0;
	}
}
if (window.document.URL.indexOf("#CommentForm")<0) {
	if (JOSC_is_ie) window.onload = function(){
		JOSC_afterAjaxResponse("hide");
	}
	else JOSC_afterAjaxResponse("hide");
}
if (window.document.URL.indexOf("#josc")<0) {
	if (JOSC_is_ie) window.onload = function(){
		JOSC_afterAjaxResponse("hide");
	}
	else JOSC_afterAjaxResponse("hide");
}
JOSCmySlide2.hide();