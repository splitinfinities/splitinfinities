var context = null;
var isPlaying = false;
var startTime;
var current16thNote;
var tempo = 58.81;
var lookahead = 25.0;
var scheduleAheadTime = 0.1;
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

var bufferLoader, stems;

var gainNode = {
	kick: null,
	highhum: null
}

var interactions = {
	kickVolume: 0,
	highhumVolume: 0,
};

// First, let's shim the requestAnimationFrame API, with a setTimeout fallback
window.requestAnimFrame = (function(){
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

	if (stem_title === "kick") {
		gainNode['kick'] = context.createBiquadFilter();
		source.connect(gainNode['kick']);
		gainNode['kick'].connect(context.destination);
		gainNode['kick'].type = "bandpass";
		gainNode['kick'].frequency.value = interactions['kickVolume'];
	}
	else if (stem_title === "highhum") {
		gainNode['highhum'] = context.createBiquadFilter();
		source.connect(gainNode['highhum']);
		gainNode['highhum'].connect(context.destination);
		gainNode['highhum'].type = "lowshelf";
		gainNode['highhum'].frequency.value = -interactions['highhumVolume'];
	}
	else {
		source.connect(context.destination);
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
	playSound(stems[1], 0, "hum");
	if (playAway) {
		playSound(stems[0], 0, "kick");
		playSound(stems[2], 0, "highhum");
	}

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
	bufferLoader = new BufferLoader( context, [ 'moth_stems/hum_high.mp3', 'moth_stems/drums_excited.mp3', 'moth_stems/hum_base.mp3', ], finishedLoading );
	bufferLoader.load();
}

window.addEventListener("load", init );

function finishedLoading(bufferList) {
	stems = bufferList;
	play();
}

$(document).ready(function(){
	kick_elements = $('[data-kick]');
	snare_elements = $('[data-snare]');



	$("html").mousemove(function(event) {
		var x = (event.clientX - $('#center').offset().left) + $(window).scrollLeft();
		var y = (event.clientY - $('#center').offset().top) + $(window).scrollTop();
		x = -x>0 ? -x : x;
		y = -y>0 ? -y : y;
		x = x / $(window).innerWidth() * 3000;
		y = y / $(window).innerHeight() * 3000;

		if (playAway) {

			if (gainNode['kick'] !== null) {
				kickVolume = y;
				gainNode['kick'].frequency.value = kickVolume;
				interactions['kickVolume'] = kickVolume;
			}

			if (gainNode['highhum'] !== null) {
				highhumVolume = x ;
				gainNode['highhum'].frequency.value = highhumVolume;
				interactions['highhumVolume'] = highhumVolume;
			}
		}

		if ($("#debug").length == 1) {
			$("#x-pos .value", "#debug").text( event.clientX );
			$("#y-pos .value", "#debug").text( event.clientY );

			$("#x-half-pos .value", "#debug").text( event.clientX / $(window).innerWidth() );
			$("#y-half-pos .value", "#debug").text( event.clientY / $(window).innerHeight() );

			$("#x-half-per .value", "#debug").text( x );
			$("#y-half-per .value", "#debug").text( y );
		}
	});

$('.stripe').on('click', function() {
	if (!$('#home').hasClass('spread')) {
		$('.bounceInLeft').addClass('animated bounceOutRight');
		$('.bounceInRight').addClass('animated bounceOutLeft');
		window.playAway = !window.playAway;
		track_functions[track_functions.length] = function() {
			$("#home").toggleClass('spread');
			$('.bounceInRight, .bounceInLeft, .bounceOutRight, .bounceOutLeft ').removeClass('animated bounceInRight bounceInLeft bounceOutLeft bounceOutRight');
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
			play();
		}
		else {
			play();
		}
	}

	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			if ($('#home').hasClass('spread')) {
				$('.bounceInLeft').addClass('animated bounceOutRight');
				$('.bounceInRight').addClass('animated bounceOutLeft');
				window.playAway = !window.playAway;
				track_functions[track_functions.length] = function() {
					$("#home").toggleClass('spread');
					$('.bounceInRight, .bounceInLeft, .bounceOutRight, .bounceOutLeft ').removeClass('animated bounceInRight bounceInLeft bounceOutLeft bounceOutRight');
				}
			}
		}
	});
});

