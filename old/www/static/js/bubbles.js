
var Bubble = function(paper,x,y,radius,color) {

	this.paper = paper;
	this.x = 0;
	this.y = 0;
	this.color = color;
	this.set = paper.set();
	this.parts = {};
	this.isDocked = false;
	this.clickCallback = function(){};
	
	this.startX = x;
	this.startY = y;

	/* create structure of the bubble */
	var circle = paper.circle(this.startX, this.startY, 0).attr({
		"stroke-width": 2,
		"stroke":"#fff",
		"fill": color
	});
	circle.data("initialRadius",radius);
	
	this.set.push(circle);
	this.parts.circle = circle;
	
	var label = paper.text(this.startX, this.startY, this.labelText).attr({
		"font-size": 10,
		"fill": "#fff",
		"opacity": 0,
		"font-family":"Open Sans"
	});
	this.set.push(label);
	this.parts.label = label;
	
	var title = this.paper.text(this.startX, this.startY, "").attr({
		"font-size": 20,
		"opacity": 1,
		"font-family":"Open Sans",
		"font-weight":"300",
		"fill":"#999"
	});
	this.titlePosition = 0;
	this.titleAnchor = "middle";
	this.parts.title = title;
	this.set.push(title);
	
	/* set hover animation */
	circle.mouseover(function() {
		this.animate({
			r: this.data("initialRadius") + 2
		}, 250, "<>");
	});
	circle.mouseout(function() {
		this.animate({
			r: this.data("initialRadius")
		}, 250, "<>");
	});
	
	this.setSize(radius);
	
	this.parts.stripes = this.paper.set();
	this.set.push(this.parts.stripes);
	
};

Bubble.prototype.setPosition = function(x,y,callback,immediate){

	this.x = x;
	this.y = y;
	
	var tX = x - this.startX;
	var tY = y - this.startY;
	
	if(!callback) callback = function(){};
	if(immediate){
		this.set.attr({"transform":"t" + tX + "," + tY});
		callback();
	}
	else this.set.animate({"transform":"t" + tX + "," + tY},200,"<>",callback);
};

Bubble.prototype.setSize = function(radius){
	
	this.radius = radius;
	
	var label = this.parts.label;
	label.attr({"font-size": this.radius * 0.3});
	
	this.parts.circle.animate({"r": this.radius}, 200, "backOut", function() {
		label.attr("opacity", 1);
	});
	
	this.parts.circle.data("initialRadius",radius);
	
	this.positionTitle();
	
};	

Bubble.prototype.setLabel = function(labelText){
	this.parts.label.attr({"text": labelText});
};

Bubble.prototype.setTitle = function(text,position,anchor){
	this.parts.title.attr("text",text);
	
	this.positionTitle(position,anchor);
};

Bubble.prototype.positionTitle = function(position,anchor){
	
	if(anchor) this.titleAnchor = anchor;
	if(position) this.titlePosition = position;
	
	var titleX = this.startX + Math.cos(this.titlePosition / 360 * 2 * Math.PI) * (this.radius + 20);
	var titleY = this.startY - Math.sin(this.titlePosition / 360 * 2 * Math.PI) * (this.radius + 20);
	
	this.parts.title.attr({"x":titleX,"y":titleY,"text-anchor":anchor});
	this.parts.title.data("x",titleX);
	this.parts.title.data("y",titleY);
	this.parts.title.data("anchor",this.titleAnchor);
};

Bubble.prototype.open = function(callback){	
	if(!this.parts.stripes.length) {		
		callback.apply(this);
		return true;
	}
	
	this.set.toFront();
	
	this.parts.stripes.show();
	
	var bubble = this;
	var numberofStripes = this.parts.stripes.length;
	var stripeNumber = 0;
	this.parts.stripes.forEach(function(stripe){
		stripe.animate({donut:stripe.data("initialAttrs"),opacity:0.8},250,"<>",function(){
			stripeNumber++;
			if(callback && numberofStripes === stripeNumber) callback.apply(bubble);
		});
	});
};

Bubble.prototype.close = function(callback){
	if(!this.parts.stripes.length) {
    callback.apply(this);
		return true;
	}
	
	var bubble = this;
	var numberofStripes = this.parts.stripes.length;
	var stripeNumber = 0;
	this.parts.stripes.forEach(function(stripe){
		var initialAttrs = stripe.data("initialAttrs").slice(0);
		initialAttrs[5] = 0;
		
		stripe.animate({donut:initialAttrs,opacity:0},250,"<>",function(){
			stripeNumber++;
			if(callback && numberofStripes === stripeNumber){
				callback.apply(bubble);
				bubble.parts.stripes.hide();
			}
		});
	});
	
	
};

Bubble.prototype.dock = function(dockX,dockY,callback){
	
	if(this.isDocked === true) return true;
	
	this.set.animate({transform:"t" + (dockX - this.startX) + "," + (dockY - this.startY)},250,"backOut");
	
	var bubble = this;
	this.parts.title.attr({"text-anchor":"middle"});
	this.parts.title.animate({"x":this.startX,"y":this.startY + 170},250,"backOut",function(){ //transform:"t" + (dockX - this.startX) + "," + (50 + dockY - this.startY), 
		bubble.isDocked = true;
		if(callback) callback.apply(bubble);
	});
	
};

Bubble.prototype.undock = function(callback){
	if(this.isDocked === false) return true;

	var bubble = this;
	bubble.set.animate({transform:"t" + (this.x - this.startX) + "," + (this.y - this.startY)},250,"backOut");
	this.parts.title.attr({"text-anchor":this.parts.title.data("anchor")});
	this.parts.title.animate({"x":this.parts.title.data("x"),"y":this.parts.title.data("y")},250,"backOut",function(){
    if(callback) callback.apply(bubble);
  });
	
	bubble.isDocked = false;
	
};

Bubble.prototype.hide =function(){
	this.set.animate({opacity:0},200);
};

Bubble.prototype.show =function(){
	this.set.animate({opacity:1},200);
};

Bubble.prototype.click = function(callback){

	if(!callback) return this.clickCallback();

	this.clickCallback = callback;

	var bubble = this;
	this.parts.circle.attr("cursor","pointer");
	this.parts.circle.click(function(){
		bubble.clickCallback.apply(bubble);
	});
	this.parts.label.attr("cursor","pointer");
	this.parts.label.click(function(){
		callback.apply(bubble);
	});
	this.parts.title.attr("cursor","pointer");
	this.parts.title.click(function(){
		callback.apply(bubble);
	});
	
};

Bubble.prototype.setStripes = function(data,minWidth,maxWidth,limit){

	var numberOfStripes =data.length;
	if(limit) numberOfStripes = Math.min(numberOfStripes,limit);
		
	var stripes = this.parts.stripes;
	
	/* smazat stary stripes */
	this.parts.stripes.forEach(function(){this.remove();});
	this.parts.stripes.clear();
	
	/* vytvorit nove tripes */
	var innerRadius = this.radius;
	var arcStart = 0;
	var cumulativeValue2 = 0;
	var sumValue1 = 0;
	var maxValue2 = 0;
	var minValue2 = 0;
	var sumValue2 = 0;
	
	for (i = 0; i < data.length; i++) {
		sumValue1 = sumValue1 + data[i].value1;
		sumValue2 = sumValue2 + data[i].value2;
		maxValue2 = Math.max(maxValue2,data[i].value2);
		minValue2 = minValue2 === 0 ? data[i].value2 : Math.min(minValue2,data[i].value2);
	}
		
		
	for (i = 0; i < data.length; i++) {
		
		var value1, value2, arcSize, arcWidthCoeff, arcWidth, arcColor,arcColorNew, arcColorHSL;
		
		if(limit && i >= limit - 1){
			value2 = sumValue2 - cumulativeValue2;
			arcSize = 1 - arcStart;
			
			arcWidthCoeff = maxValue2 === minValue2 ? 0.5 : (value2 - minValue2) / (maxValue2 - minValue2);
			arcWidth = arcWidthCoeff * maxWidth + (1 - arcWidthCoeff) * minWidth;
			
			stripes.push(this.createStripe(this.x, this.y, innerRadius, innerRadius + arcWidth, arcStart, arcSize,"#999","Ostatní"));
			
			break;
		}
			
		value1 = data[i].value1;
		value2 = data[i].value2;
		arcSize = value1 / sumValue1;
		arcWidthCoeff = maxValue2 === minValue2 ? 0.5 : (value2 - minValue2) / (maxValue2 - minValue2);
		arcWidth = arcWidthCoeff * maxWidth + (1 - arcWidthCoeff) * minWidth;
		arcColor = Raphael.rgb2hsl(Raphael.getRGB(this.color));
		arcColorNew = [arcColor.h, arcColor.s, arcColor.l + (1 - arcColor.l) * (i / (numberOfStripes))];
		arcColorHSL = Raphael.hsl(arcColorNew[0], arcColorNew[1], arcColorNew[2]);
		
		stripes.push(this.createStripe(this.startX, this.startY, innerRadius, innerRadius + arcWidth, arcStart, arcSize,arcColorHSL,data[i].tooltip));
		arcStart = arcStart + arcSize;
		
		cumulativeValue2 = cumulativeValue2 + data[i].value2;
	}
	
	stripes.hide();
	
};

Bubble.prototype.createStripe = function(x,y,innerWidth,outerWidth,arcStart,arcSize,arcColor,tooltip){
	var stripe = this.paper.path().attr({
		"stroke-width":3,
		"fill":arcColor,
		"stroke":"#fff",//#eee
		opacity:0,
		donut:[x,y,innerWidth+3,outerWidth,arcStart,0],
		title:tooltip
	});

	stripe.data("initialAttrs",[x,y,innerWidth+3,outerWidth,arcStart,arcSize]);

	stripe.mouseover(function(){this.attr("opacity","1");});
	stripe.mouseout(function(){this.attr("opacity",".8");});

	return stripe;
};

Bubble.prototype.labelText = "???";