
function brief_message_load_more(e){

  var widget = document.getElementById(e + '_wrapper'),
  button = document.getElementById(e + '_load_more_button'),
  author_name = button.getAttribute('data-author_name'),
  category = button.getAttribute('data-category'),
  max_content = button.getAttribute('data-max_content'),
  load_more_per_page = button.getAttribute('data-load_more_per_page'),
  inner = document.getElementById(e + '_inner'),
  spin = widget.getElementsByClassName('bfm_load_more_spin'),
  content_count = inner.childElementCount;

  send_data = {
    action:'brief_message_ajax_load_more',
    nonce: brief_message_frontend_ajax.nonce,
    author_name: author_name,
    content_count:content_count,
    category: category,
    load_more_per_page:load_more_per_page
  };

  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {

    if (xhr.readyState === 4) {
      
      
      
      spin[0].style.display = 'none';

      var msg = JSON.parse(xhr.response);

      
      if (xhr.status === 200 && msg['message'] === "OK") {
        
        inner.insertAdjacentHTML( 'beforeend' , msg['content']);

        if(msg['last']){
          var bfm_load_more = widget.getElementsByClassName('bfm_load_more');
          bfm_load_more[0].parentNode.removeChild(bfm_load_more[0]);
        }

        brief_message_pop_up_message(brief_message_common.pop_up_completed,'#4caf50');

      }else{
        
        brief_message_pop_up_message(brief_message_common.pop_up_error,'#e91e63');
      }
    }else{
      
      spin[0].style.display = '';
      brief_message_pop_up_message(brief_message_common.pop_up_now_loading,'');
    }
  };

  xhr.open("POST", brief_message_frontend_ajax.ajax_url , true);
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