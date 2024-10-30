
document.addEventListener("DOMContentLoaded", function(){
	var div = document.createElement('div');
	div.id = 'bfm_pop_up_message';
	document.body.appendChild(div);
});



var brief_message_timeoutID;

function brief_message_stopTimeout() {
	var pop_up_message = document.getElementById('bfm_pop_up_message');
	pop_up_message.classList.add('inactive');
	clearTimeout(brief_message_timeoutID);
	setTimeout(function() {
		pop_up_message.classList.remove('inactive');
	}, 100);

}

function brief_message_pop_up_message(message,bg_color) {
	if(typeof brief_message_timeoutID !== 'undefined')
		brief_message_stopTimeout();
	if (bg_color === '') bg_color = '#222';
	var pop_up_message = document.getElementById('bfm_pop_up_message');
	pop_up_message.style.backgroundColor = bg_color;
	pop_up_message.classList.add('active');
	pop_up_message.innerHTML = message;
	brief_message_timeoutID = setTimeout(function() {
		pop_up_message.classList.remove('active');
	}, 4000);
}
