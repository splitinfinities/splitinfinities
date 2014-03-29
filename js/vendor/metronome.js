var context = null;
var isPlaying = false;
var startTime;
var current16thNote;
var tempo = 58.81;
var lookahead = 25.0;
var scheduleAheadTime = 0.025;
var nextNoteTime = 0.0;
var noteResolution = 2;
var noteLength = 0.1;
var timerID = 0;
var canvas,
canvasContext;
var last16thNoteDrawn = -1;
var notesInQueue = [];
var playAway = false;
var kick_elements = null;
var snare_elements = null;
var kick_functions = [];
var snare_functions = [];
var track_functions = [];
var onlyplaypizzi = false;

var getParameterByName = function (e){e=e.replace(/[\[]/,"\\[").replace(/[\]]/,"\\]");var t=new RegExp("[\\?&]"+e+"=([^&#]*)"),n=t.exec(location.search);return n==null?"":decodeURIComponent(n[1].replace(/\+/g," "))};


var bufferLoader, stems;

var gainNode = {
	"hum_base": null,
	"real_pizzicato": null,
	"drums_relaxed": null,
	"drums_excited": null,
}

var interactions = {
	"hum_base": 1,
	"real_pizzicato": 1,
	"drums_relaxed": 1,
	"drums_excited": 1,
};

// First, let's shim the requestAnimationFrame API, with a setTimeout fallback
window.requestAnimFrame = (function() {
	return  window.requestAnimationFrame ||
	window.webkitRequestAnimationFrame ||
	window.mozRequestAnimationFrame ||
	window.oRequestAnimationFrame ||
	window.msRequestAnimationFrame ||
	function( callback ){
		window.setTimeout(callback, 1000 / 60);
	};
})();

function BufferLoader(context, urlList, callback) {
	this.context = context;
	this.urlList = urlList;
	this.onload = callback;
	this.bufferList = new Array();
	this.loadCount = 0;
}

BufferLoader.prototype.loadBuffer = function(url, index) {
	// Load buffer asynchronously
	var request = new XMLHttpRequest();
	request.open("GET", url, true);
	request.responseType = "arraybuffer";

	var loader = this;

	request.onload = function() {
	// Asynchronously decode the audio file data in request.response
	loader.context.decodeAudioData(
		request.response,
		function(buffer) {
			if (!buffer) {
				alert('error decoding file data: ' + url);
				return;
			}
			loader.bufferList[index] = buffer;
			if (++loader.loadCount == loader.urlList.length)
				loader.onload(loader.bufferList);
		},
		function(error) {
			console.error('decodeAudioData error', error);
		});
}

request.onerror = function() {
	alert('BufferLoader: XHR error');
}

request.send();
}

BufferLoader.prototype.load = function() {
	for (var i = 0; i < this.urlList.length; ++i)
		this.loadBuffer(this.urlList[i], i);
}

function nextNote() {
	var secondsPerBeat = 60.0 / tempo;
	nextNoteTime += 0.25 * secondsPerBeat;
	current16thNote++;
	if (current16thNote == 16) {
		current16thNote = 0;
	}
}

function playSound(buffer, time, stem_title) {
	var source = context.createBufferSource();
	source.buffer = buffer;

	if (onlyplaypizzi === true) {
		if (stem_title === "real_pizzicato") {
			gainNode['real_pizzicato'] = context.createBiquadFilter();
			source.connect(gainNode['real_pizzicato']);
			gainNode['real_pizzicato'].connect(context.destination);
			gainNode['real_pizzicato'].type = "lowpass";
			gainNode['real_pizzicato'].frequency.value = 1075;
			gainNode['real_pizzicato'].gain.value = 1;
		}
		else if (stem_title === "drums_relaxed") {
			gainNode['drums_relaxed'] = context.createGain();
			source.connect(gainNode['drums_relaxed']);
			gainNode['drums_relaxed'].connect(context.destination);
			gainNode['drums_relaxed'].gain.value = 1;
		}
	} else {
		if (stem_title === "hum_base") {
			gainNode['hum_base'] = context.createBiquadFilter();
			source.connect(gainNode['hum_base']);
			gainNode['hum_base'].connect(context.destination);
			gainNode['hum_base'].type = "highpass";
			gainNode['hum_base'].frequency.value = interactions['hum_base'];
		}
		else if (stem_title === "real_pizzicato") {
			gainNode['real_pizzicato'] = context.createBiquadFilter();
			source.connect(gainNode['real_pizzicato']);
			gainNode['real_pizzicato'].connect(context.destination);
			gainNode['real_pizzicato'].type = "lowpass";
			gainNode['real_pizzicato'].frequency.value = interactions['real_pizzicato'];
		}
		else if (stem_title === "drums_relaxed") {
			gainNode['drums_relaxed'] = context.createGain();
			source.connect(gainNode['drums_relaxed']);
			gainNode['drums_relaxed'].connect(context.destination);
			gainNode['drums_relaxed'].gain.value = (playAway === true) ? 0 : 1;
		}
		else if (stem_title === "drums_excited") {
			gainNode['drums_excited'] = context.createGain();
			source.connect(gainNode['drums_excited']);
			gainNode['drums_excited'].connect(context.destination);
			gainNode['drums_excited'].gain.value = (playAway === true) ? 1 : 0;
		}
		else {
			source.connect(context.destination);
		}
	}

	source.start(time);
}

function scheduleNote( beatNumber, time ) {
	notesInQueue.push( { note: beatNumber, time: time } );
	var i = 0;

	if (beatNumber%16 === 0) {
		queueActive();
	}

	if (beatNumber%4 === 0) {
		runKick();
	}
	else if (beatNumber%2 === 0) {
		runSnare();
	}

}

function queueActive() {
	playSound(stems[1], 0, "drums_relaxed");

	if (playAway) {
		playSound(stems[0], 0, "hum_base");
		playSound(stems[2], 0, "real_pizzicato");
		playSound(stems[3], 0, "drums_excited");
	}

	// execute each queued up track function
	$.each(track_functions, function(key, val){
		val();
	});

	track_functions = [];
}

function runKick() {
	kick_elements.toggleClass('kick');

	$.each(kick_functions, function(key, val){
		val();
	});

	kick_functions = [];
}

function runSnare(){
	snare_elements.toggleClass('snare');

	$.each(snare_functions, function(key, val){
		val();
	});

	snare_functions = [];
}

function scheduler() {
	while (nextNoteTime < context.currentTime + scheduleAheadTime ) {
		scheduleNote( current16thNote, nextNoteTime );
		nextNote();
	}
	timerID = window.setTimeout( scheduler, lookahead );
}

function play() {
	isPlaying = !isPlaying;

	if (isPlaying) {
		current16thNote = 0;
		nextNoteTime = context.currentTime;
		scheduler();
		return "stop";
	} else {
		window.clearTimeout( timerID );
		return "play";
	}
}

function init() {
	var container = document.createElement( 'div' );
	window.context = window.context || window.webkitcontext;
	context = new AudioContext();
	bufferLoader = new BufferLoader( context, [ 'moth_stems/real_pizzicato.mp3', 'moth_stems/drums_relaxed.mp3', 'moth_stems/hum_base.mp3', 'moth_stems/drums_excited.mp3', ], finishedLoading );
	bufferLoader.load();
}

window.addEventListener("load", init );

function finishedLoading(bufferList) {
	stems = bufferList;
	play();
}

function accelerationUpdates(x, y, newx, newy, event) {

	if (playAway) {
		if (gainNode['hum_base'] !== null) {
			frequency = newy;
			gainNode['hum_base'].frequency.value = frequency;
			interactions['hum_base'] = frequency;
		}

		if (gainNode['real_pizzicato'] !== null) {
			frequency = newx;
			gainNode['real_pizzicato'].frequency.value = frequency;
			interactions['real_pizzicato'] = frequency;
		}
	} else {

	}

	$('#home').css("transform", "translate("+ ( ( x / 60 ) * -1) +"%, "+ ( ( y / 30 ) * -1) +"%)");
	$('#portfolio, #resume, #ideas, #inspirations').css("transform", "translate("+ ((x/80)*-1) +"%, "+ ((y/60)*-1) +"%)");

	$('.subtle-parallax').css("transform", "translate("+ ( ( x / 60 ) * -1) +"%, "+ ( ( y / 30 ) * -1) +"%)");

	if ($("#debug").length == 1) {
		$("#x-pos .value", "#debug").text( x );
		$("#y-pos .value", "#debug").text( y );

		$("#x-half-pos .value", "#debug").text( newx );
		$("#y-half-pos .value", "#debug").text( newy );

		$("#x-half-per .value", "#debug").text( x );
		$("#y-half-per .value", "#debug").text( y );
	}
}

var mobileInit = 0;

$(document).ready(function() {
	kick_elements = $('[data-kick]');
	snare_elements = $('[data-snare]');

	if (getParameterByName('trigger')) {
		track_functions[track_functions.length] = function() {
			kick_functions[kick_functions.length] = function() {
				$('[data-activate=#'+getParameterByName('trigger')+']').trigger('click');
			}
		}
	}

	$(document).pjax('.pjax', '#pjax-container');

	$(document).on('pjax:complete', function() {
		kick_elements = $('[data-kick]');
		snare_elements = $('[data-snare]');
	})

	setTimeout(function() {  $('#initial-intro').show(); setTimeout(function() { $('#initial-intro').hide(); }, 6000) }, 1000);

	if (Modernizr.touch && mobileInit !== 1) {
		$("html").one('click', function() {
			isPlaying = false;
			play();
			mobileInit = 1;
		});
	}

	if (Modernizr.touch) {
		window.ondeviceorientation = function(event) {
			var accelerationX = Math.ceil(event.gamma * 10);
			var accelerationY = Math.ceil(event.beta * 10);
			var x = (accelerationX - $('#center').offset().left) + $(window).scrollLeft();
			var y = (accelerationY - $('#center').offset().top) + $(window).scrollTop();
			var newx = -x>0 ? 0 : x;
			var newy = -y>0 ? 0 : y;
			var newx = x;
			var newy = y;

			accelerationUpdates(x, y, newx, newy, event);
		}
	}
	else {
		$("html").mousemove(function(event) {
			var x = (event.clientX - $('#center').offset().left) + $(window).scrollLeft();
			var y = (event.clientY - $('#center').offset().top) + $(window).scrollTop();
			var newx = (-x>0) ? -x : x;
			var newy = (-y>0) ? -y : y;
			newx = newx / $(document).innerWidth() * 4000;
			newy = newx / $(document).innerHeight() * 4000;
			accelerationUpdates(x, y, newx, newy, event);
		});
	}

	$('.stripe').on('click', function() {
		var panelToPop = $(this).attr('data-activate');

		if (!$('#home').hasClass('spread')) {
			window.playAway = !window.playAway;

			$('#home').hide()

			track_functions[track_functions.length] = function() {
				$(panelToPop).addClass('animated');
				$('.alert').hide();
				$('#first-intro[data-init]').show();
				setTimeout( function() { $('#first-intro[data-init]').removeAttr('data-init').hide(); }, 6000);
				$("#home").toggleClass('spread').show();
				$('.stripe').removeClass('animated bounceInLeft bounceInRight');
			}

			snare_functions[snare_functions.length] = function() {

			}

			kick_functions[kick_functions.length] = function() {
				$('.panel').not($(panelToPop)).removeClass('animated');
			}
		} else {
			track_functions[track_functions.length] = function() {
				$(panelToPop).addClass('animated');
			}

			snare_functions[snare_functions.length] = function() {
				$('.panel').not($(panelToPop)).removeClass('animated');

				kick_functions[kick_functions.length] = function() {
					$(panelToPop).addClass('animated');
				}
			}
		}
	});

	var hidden = "hidden";
	// Standards:
	if (hidden in document)
		document.addEventListener("visibilitychange", onchange);
	else if ((hidden = "mozHidden") in document)
		document.addEventListener("mozvisibilitychange", onchange);
	else if ((hidden = "webkitHidden") in document)
		document.addEventListener("webkitvisibilitychange", onchange);
	else if ((hidden = "msHidden") in document)
		document.addEventListener("msvisibilitychange", onchange);
	// IE 9 and lower:
	else if ('onfocusin' in document)
		document.onfocusin = document.onfocusout = onchange;
	// All others:
	else
		window.onpageshow = window.onpagehide
	= window.onfocus = window.onblur = onchange;

	function onchange (evt) {
		var v = 'visible', h = 'hidden',
		evtMap = {
			focus:v, focusin:v, pageshow:v, blur:h, focusout:h, pagehide:h
		};
		evt = evt || window.event;
		if (evt.type in evtMap) {
			onlyplaypizzi = !onlyplaypizzi; // TODO: tie onblur to stopping, and onvisible to playing only IF the track isn't currently playing.
		}
		else {
			onlyplaypizzi = !onlyplaypizzi;
		}
	}

	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			$('#pjax-container').addClass('animated bounceOutDown');
			if ($('#home').hasClass('spread')) {
				$('.bounceInLeft').addClass('animated bounceOutRight');
				$('.bounceInRight').addClass('animated bounceOutLeft');
				window.playAway = !window.playAway;
				history.pushState({}, "", "/");
				track_functions[track_functions.length] = function() {
					$("#home").toggleClass('spread');
					$('.panel').removeClass('animated');
					$('#pjax-container').removeClass('animated bounceOutDown')
				}
			}
		}
	});
});

