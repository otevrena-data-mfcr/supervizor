var souhvezdi_coordinates = {
	"velky_vuz": {
		"label": "Velký vůz",
		"stars": [[564,605],[850,687],[954,445],[466,261],[326,133],[62,143],[566,403]],
		"lines": "M566 403L564 605L850 687L954 445L566 403L466 261L326 133L62 143"
	},
	"maly_vuz": {
		"label": "Malý vůz",
		"stars": [[590,80],[614,627],[533,707],[467,317],[516,200],[508,490],[424,543]],
		"lines": "M590 80L516 200L467 317L508 490L614 627L533 707L424 543L508 490"
	},
	"blizenci": {
		"label": "Blíženci",
		"stars": [[338,267],[729,678],[432,177],[708,431],[842,520],[461,475],[320,365]],
		"lines": "M338 267L432 177L708 431L842 520L729 678L461 475L320 365z"
	},
	"orion": {
		"label": "Orion",
		"stars": [[754,632],[382,177],[636,197],[553,434],[454,696],[510,460],[589,405]],
		"lines": "M754 632L454 696L510 460L553 434L589 405L636 197L540 109L382 177L510 460M589 405L754 632"
	}
	/*
	"kruh":{
		"stars": [
			[570 + Math.sin(5*2*Math.PI/7)*300,400 - Math.cos(5*2*Math.PI/7)*300],
			[570 + Math.sin(6*2*Math.PI/7)*300,400 - Math.cos(6*2*Math.PI/7)*300],
			[570 + Math.sin(0*2*Math.PI/7)*300,400 - Math.cos(0*2*Math.PI/7)*300],
			[570 + Math.sin(1*2*Math.PI/7)*300,400 - Math.cos(1*2*Math.PI/7)*300],
			[570 + Math.sin(2*2*Math.PI/7)*300,400 - Math.cos(2*2*Math.PI/7)*300],
			[570 + Math.sin(3*2*Math.PI/7)*300,400 - Math.cos(3*2*Math.PI/7)*300],
			[570 + Math.sin(4*2*Math.PI/7)*300,400 - Math.cos(4*2*Math.PI/7)*300]
		],
		"lines": "M270,400a300,300 0 1,0 600,0a300,300 0 1,0 -600,0"
	},
	"kruh2":{
		"stars": [
			[570,400],
			[570 + Math.sin(0*2*Math.PI/6)*300,400 - Math.cos(0*2*Math.PI/6)*300],
			[570 + Math.sin(1*2*Math.PI/6)*300,400 - Math.cos(1*2*Math.PI/6)*300],
			[570 + Math.sin(2*2*Math.PI/6)*300,400 - Math.cos(2*2*Math.PI/6)*300],
			[570 + Math.sin(3*2*Math.PI/6)*300,400 - Math.cos(3*2*Math.PI/6)*300],
			[570 + Math.sin(4*2*Math.PI/6)*300,400 - Math.cos(4*2*Math.PI/6)*300],
			[570 + Math.sin(5*2*Math.PI/6)*300,400 - Math.cos(5*2*Math.PI/6)*300]
		],
		"lines": "M270,400a300,300 0 1,0 600,0a300,300 0 1,0 -600,0"
	},
	"duha":{
		"stars": [
			[570 + Math.sin(0*Math.PI/6 - Math.PI/2)*300,700 - Math.cos(0*Math.PI/6 - Math.PI/2)*300],
			[570 + Math.sin(1*Math.PI/6 - Math.PI/2)*300,700 - Math.cos(1*Math.PI/6 - Math.PI/2)*300],
			[570 + Math.sin(2*Math.PI/6 - Math.PI/2)*300,700 - Math.cos(2*Math.PI/6 - Math.PI/2)*300],
			[570 + Math.sin(3*Math.PI/6 - Math.PI/2)*300,700 - Math.cos(3*Math.PI/6 - Math.PI/2)*300],
			[570 + Math.sin(4*Math.PI/6 - Math.PI/2)*300,700 - Math.cos(4*Math.PI/6 - Math.PI/2)*300],
			[570 + Math.sin(5*Math.PI/6 - Math.PI/2)*300,700 - Math.cos(5*Math.PI/6 - Math.PI/2)*300],
			[570 + Math.sin(6*Math.PI/6 - Math.PI/2)*300,700 - Math.cos(6*Math.PI/6 - Math.PI/2)*300]
		],
		"lines": "M285 700 A300 300 0 0 1 885 700",
	},
	"uhlopricka":{
		"stars": [
			[200 + 0*770/6,700 - 0*600/6],
			[200 + 1*770/6,700 - 1*600/6],
			[200 + 2*770/6,700 - 2*600/6],
			[200 + 3*770/6,700 - 3*600/6],
			[200 + 4*770/6,700 - 4*600/6],
			[200 + 5*770/6,700 - 5*600/6],
			[200 + 6*770/6,700 - 6*600/6]
		],
		"lines": "M200 700L970 100"
	},
	"pyramida":{
		"stars": [
			[570,700 - 0*600/6],
			[570,700 - 1*600/6],
			[570,700 - 2*600/6],
			[570,700 - 3*600/6],
			[570,700 - 4*600/6],
			[570,700 - 5*600/6],
			[570,700 - 6*600/6]
		],
		"lines": "M570 700L570 100"
	}*/
};

var souhvezdi_ikony;
var konami_def = [38,38,40,40,37,39,37,39,66,65];
var konami_stack = [38,38,40,40,37,39,37,39,66,65];

function createStarIcons(paper){
	souhvezdi_ikony = paper.set();
	var i = 1;
	$.each(souhvezdi_coordinates,function(souhvezdi_id,data){
		
		var souhvezdi = paper.set();
		var ikona = paper.set();
		var x = 1140 - 50;
		var y = 50*i;
		var transform = "T" + (x-570) + "," + (y-400) + "s0.05,0.05,570,400";
		var line = paper.path(data.lines).transform(transform);
		
		ikona.push(line);
		
		$.each(data.stars,function(j,star){
			var dot = paper.circle(star[0],star[1],40).attr({"fill":"#000","transform":transform});
			ikona.push(dot);
		});
		
		var ctverec = paper.rect(x-25,y-25,50,50).attr({"fill":"transparent","stroke":"transparent","cursor":"pointer"});
		ikona.push(ctverec);
		
		ikona.attr("opacity",0.1);
		
		souhvezdi.push(ikona);
		
		var napis = paper.text(x - 40,y,data.label).attr({"opacity":0,"text-anchor":"end"});
		souhvezdi.push(napis);
		
		ctverec.hover(function(){ikona.attr("opacity",1);napis.attr("opacity",1);},function(){ikona.attr("opacity",0.1);napis.attr("opacity",0);});
		
		ctverec.click(function(){
			setStars(souhvezdi_id);
		});
		
		
		souhvezdi_ikony.push(souhvezdi);
		
		i++;
	});
	
	/* KONAMI */
	$(document).keydown(function(e){
		
		if(currentState.view !== "index") return; //kdyz nejsme na úvodní, tak nic

		var code = (typeof e.which == "number") ? e.which : e.keyCode;
		if(code !== konami_stack.shift()) konami_stack = konami_def.slice(0);
		if(konami_stack.length === 0){
			souhvezdi_ikony.show();
			konami_stack = konami_def.slice(0);
		}
	});
	
	souhvezdi_ikony.hide();
													
}

function setStars(souhvezdi_id,callback,immediate){
	
	
	var scale = 1;
	var moveY = window.innerHeight >= 1000 ? 0 : -90;
	
	souhvezdi.hide().attr({"path":souhvezdi_coordinates[souhvezdi_id].lines,"transform":"s" + scale + "," + scale + ",570,400t0," + moveY});

	var stars_coordinates = scale === 1 ? souhvezdi_coordinates[souhvezdi_id].stars : scaleXY(souhvezdi_coordinates[souhvezdi_id].stars,scale,scale,570,400);
	var i = 0;
	
	$.each(skupiny,function(skupina_id,skupina){
		var callback_f;
		
		if(i === 0) callback_f = function(){souhvezdi.show();if(callback) callback();};
		else callback_f = function(){};
		
		skupina.bubble.setPosition(stars_coordinates[i][0],stars_coordinates[i][1] + moveY,callback_f,immediate);
		i++;
	});
}