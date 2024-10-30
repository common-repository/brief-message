
function brief_message_add_post(e){

  var form = document.getElementById(e + '_form'),
  textarea = document.getElementById(e + '_textarea');

  if(textarea.value === ''){
    brief_message_pop_up_message(brief_message_common.pop_up_is_blank,'#ff5722');
    return;
  }

  var widget = document.getElementById(e + '_wrapper'),
  inner = document.getElementById(e + '_inner'),
  button = document.getElementById(e + '_post_button'),
  spin = document.getElementById(e + '_post_spin'),
  send_data = {
    action:'brief_message_ajax_add_content',
    nonce: brief_message.nonce,
    category: button.getAttribute('data-category'),
    author_name: button.getAttribute('data-author_name'),
    content:textarea.value
  };

  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {

    if (xhr.readyState === 4) {
      
      
      
      var msg = JSON.parse(xhr.response);

      button.disabled = false;
      textarea.disabled = false;
      button.style.cursor = '';
      textarea.style.cursor = '';
      spin.style.display = 'none';
      
      if (xhr.status === 200 && msg['message'] === "OK") {
        
        textarea.value = '';

        
        var no_content = inner.getElementsByClassName('bfm_no_content');
        if( no_content.length !== 0){
          no_content[0].parentNode.removeChild(no_content[0]);
        }

        
        inner.insertAdjacentHTML( 'afterbegin' , msg['content']);

        brief_message_pop_up_message(brief_message_common.pop_up_completed,'#4caf50');

      }else{
        
        brief_message_pop_up_message(brief_message_common.pop_up_error,'#e91e63');
      }
    }else{
      
      textarea.disabled = true;
      button.disabled = true;
      textarea.style.cursor = 'not-allowed';
      button.style.cursor = 'not-allowed';
      spin.style.display = '';
      brief_message_pop_up_message(brief_message_common.pop_up_now_posting,'');
    }
  };

  xhr.open("POST", brief_message.ajax_url , true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.send( encodeURI( brief_message_encodeURI(send_data) ) );

  function brief_message_encodeURI(obj) {
    var result = '',
    splitter = '';

    if (typeof obj === 'object') {
      Object.keys(obj).forEach(function (key) {
        result += splitter + key + '=' + encodeURIComponent(obj[key]);
        splitter = '&';
      });
    }
    return result;
  }

}