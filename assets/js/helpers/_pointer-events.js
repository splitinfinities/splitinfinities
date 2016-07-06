// namespace: pointer_events

var pointer_events = {
	body: document.body,
	timer: null
};

// Disables the ability to hover over items as you scroll down the page
// which would cause the browser to deliver less than 60fps while scrolling
// since it is trying to render hover states for everything you pass over
window.addEventListener('scroll', function() {
  clearTimeout(pointer_events.timer);
  if(!pointer_events.body.classList.contains('disable-hover')) {
    pointer_events.body.classList.add('disable-hover')
  }

  pointer_events.timer = setTimeout(function(){
    pointer_events.body.classList.remove('disable-hover')
  }, 100);
}, false);
