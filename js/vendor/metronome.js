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

var bufferLoader, stems;

var gainNode = {
	kick: null,
	highhum: null
}

var interactions = {
	kickVolume: 1,
	highhumVolume: 1,
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
		gainNode['kick'] = context.createGain();
		source.connect(gainNode['kick']);
		gainNode['kick'].connect(context.destination);
		gainNode['kick'].gain.value = interactions['kickVolume'];
	}
	else if (stem_title === "highhum") {
		gainNode['highhum'] = context.createGain();
		source.connect(gainNode['highhum']);
		gainNode['highhum'].connect(context.destination);
		gainNode['highhum'].gain.value = interactions['highhumVolume'];
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
}

function runKick(){

}

function runSnare(){

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
	bufferLoader = new BufferLoader( context, [ 'sounds/kick.mp3', 'sounds/hum.mp3', 'sounds/highhum.mp3', ], finishedLoading );
	bufferLoader.load();
}

window.addEventListener("load", init );

function finishedLoading(bufferList) {
	stems = bufferList;
	play();
}

$(document).ready(function(){
	$("html").mousemove(function(event) {
		if (playAway) {
			if (gainNode['kick'] !== null) {
				kickVolume = event.pageY / 1000;
				gainNode['kick'].gain.value = kickVolume;
				interactions['kickVolume'] = kickVolume;
			}

			if (gainNode['highhum'] !== null) {
				highhumVolume = event.pageX / 1000;
				gainNode['highhum'].gain.value = highhumVolume;
				interactions['highhumVolume'] = highhumVolume;
			}
		}
	});

	$('body').hammer().on('doubletap', function(){
		window.playAway = !window.playAway;
	});
});
