function scaleXY(data, scaleX, scaleY, centerX, centerY) {

	var scaledData = [];

	if (typeof centerX === "undefined") centerX = 0;
	if (typeof centerY === "undefined") centerY = 0;
	if (typeof scaleX === "undefined") scaleX = 1;
	if (typeof scaleY === "undefined") scaleY = scaleX;

	for (var i = 0; i < data.length; i++) {
		scaledData[i] = [centerX + (data[i][0] - centerX) * scaleX, centerY + (data[i][1] - centerY) * scaleY];
	}

	return scaledData;
}

function format_number(n) {
	if (n == undefined) return 0;
	return n.toFixed(0).replace(/(\d)(?=(\d{3})+$)/g, '$1 ').replace(/\./, ",");
}

function format_money(n) {
	if (n == undefined) return 0;
	return n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1 ').replace(/\./, ",") + " Kč";
}

var currentState = { "view": "index" };
var skupiny = {};
var list;
var skupina;
var souhvezdi;

function updateState() {

	var newState = History.getState().data;
	if (!newState.view) newState = { view: "index" };
	if (!currentState.view) currentState = { view: "index" };

	if (newState.view === "index" && currentState.view === "skupina") {

		document.title = "Úvod" + " - " + TITLE;

		skupina = skupiny[currentState.skupina];

		skupiny[currentState.skupina].bubble.close(function () {
			this.undock(function () {
				$.each(skupiny, function (j, skupina2) {
					if (skupina !== skupina2) skupina2.bubble.show();
				});
				souhvezdi.show();
			});
		});
		$("#widget").removeClass("open");
		skupina.widget.hide();
		list.hide();
	}
	else if (newState.view === "skupina" && currentState.view === "skupina" && newState.skupina === currentState.skupina) {

		list.setLoading(true);

		skupina = skupiny[newState.skupina];
		list.load({ limit:15, instituce: "eq." + INSTITUTION, rok: "eq." + YEAR, skupina: "eq." + skupina.id/*, page: History.getState().data.page ? History.getState().data.page : 1 */}, { polozky: skupina.polozky });

		document.title = skupina.nazev + " - " + TITLE;
	}
	else if (newState.view === "skupina" && currentState.view === "index") {

		document.title = "Úvod" + " - " + TITLE;

		souhvezdi.hide();
		souhvezdi_ikony.hide();

		skupina = skupiny[newState.skupina];

		$.each(skupiny, function (j, skupina2) {
			if (skupina !== skupina2) skupina2.bubble.hide();
		});
		skupina.bubble.dock(150, 150, function () {

			list.setLoading(true);
			list.clear();
			list.show();

			this.open(function () {
				console.log(skupina);
				list.load({ limit:15, instituce: "eq." + INSTITUTION, rok: "eq." + YEAR, skupina_id: "eq." + skupina.id/*, page: History.getState().data.page ? History.getState().data.page : 1*/ }, { polozky: skupina.polozky, datum: { min: new Date(skupina.min_uhrazeno_udt * 1000), max: new Date(skupina.max_uhrazeno_udt * 1000) } });
			});
			$("#widget").addClass("open");
			skupina.widget.show();
		});

		document.title = skupina.nazev + " - " + TITLE;
	}

	currentState = History.getState().data;
}
$(document).ready(function () {

	var paperX = 1140;
	var paperY = 800;
	var paper = new Raphael("bubliny", paperX, paperY);

	$("#widget .back").click(function () {
		History.pushState({ view: "index" }, "Úvod", WEB_ROOT + "/" + "?instituce=" + INSTITUTION + "&rok=" + YEAR);
		updateState();
	});

	paper.customAttributes.donut = function (x, y, innerRadius, outerRadius, start, size) {

		if (size >= 1) size = 0.9999;

		var startAngle = 2 * Math.PI * start;
		var angle = 2 * Math.PI * size;

		var startX1 = x + Math.sin(startAngle) * outerRadius;
		var startY1 = y - Math.cos(startAngle) * outerRadius;
		var endX1 = x + Math.sin(startAngle + angle) * outerRadius;
		var endY1 = y - Math.cos(startAngle + angle) * outerRadius;

		var startX2 = x + Math.sin(startAngle + angle) * innerRadius;
		var startY2 = y - Math.cos(startAngle + angle) * innerRadius;
		var endX2 = x + Math.sin(startAngle) * innerRadius;
		var endY2 = y - Math.cos(startAngle) * innerRadius;

		var outerArc = (size > 0.5 ? 1 : 0);

		var properties = [];
		properties.push("M" + startX1 + "," + startY1);
		properties.push("A" + outerRadius + "," + outerRadius + " 0 " + outerArc + ",1 " + endX1 + "," + endY1);
		properties.push("L" + startX2 + "," + startY2);
		properties.push("A" + innerRadius + "," + innerRadius + " 0 " + outerArc + ",0 " + endX2 + "," + endY2);
		properties.push("Z");

		return { path: properties.join(" ") };
	};

	list = new List("#seznam");

	souhvezdi = paper.path().attr({ "stroke": "#fff", "stroke-width": 2 });
	createStarIcons(paper);

	/* LOAD DATA */
	$.getJSON(API_ROOT + "/skupiny_stats", { instituce: "eq." + INSTITUTION, rok: "eq." + YEAR }, function (dataSkupiny) {
		$.getJSON(API_ROOT + "/polozky_stats", { instituce: "eq." + INSTITUTION, rok: "eq." + YEAR }, function (dataPolozky) {

			souhvezdi.show();

			skupiny = dataSkupiny.reduce(function (acc, cur) { acc[cur.id] = cur; return acc; }, {});
			var maxSkupiny = dataSkupiny.reduce(function (acc, cur) { return Math.max(acc, cur.objem); }, 0);
			var minSkupiny = dataSkupiny.reduce(function (acc, cur) { return Math.min(acc, cur.objem); }, Infinity);

			var i = 0;
			$.each(skupiny, function (skupina_id, skupina) {

				/* CREATE BUBBLES AND WIDGETS */
				/* create bubble */
				var bublina = new Bubble(paper, paperX / 2, paperY / 2, 0, skupina.barva);
				skupina.bubble = bublina;

				/* set bubble */
				var value = skupina.objem;
				var sizeCoeff = maxSkupiny != minSkupiny ? (value - minSkupiny) / (maxSkupiny - minSkupiny) : 0;
				var bubbleSize = sizeCoeff * 80 + (1 - sizeCoeff) * 40;

				var labelText = Math.round(value / 1000) + " tis. Kč";
				if (value > 1000000) labelText = Math.round(value / 1000000) + " mil. Kč";
				if (value > 1000000000) labelText = Math.round(value / 1000000000) + " mld. Kč";

				bublina.setLabel(labelText);
				bublina.setTitle(skupina.nazev, 0, "start");
				bublina.setSize(bubbleSize);

				skupina.polozky = dataPolozky.filter(function (item) { item.skupina_id = skupina_id });

				var stripeData = skupina.polozky.map(function (item) {
					return {
						tooltip: item.nazev + "\nObjem: " + format_number(item.objem) + "\nPočet faktur: " + format_money(item.pocet),
						value1: item.objem,
						value2: 100
					}
				});

				var stripeLimit = 10;
				bublina.setStripes(stripeData, 30, 80, stripeLimit);

				/* create widget */
				var widget = $("#widget .polozky.template").clone(true).appendTo("#widget").removeClass("template").attr("id", "widget_" + skupina.id);
				skupina.widget = widget;

				$.each(skupina.polozky, function (j, polozka) {
					var polozka_div = widget.find(".polozka.template").clone(true).removeClass("template");
					var vyplnit = {
						"nazev": polozka.id + " - " + polozka.nazev,
						//"objem": polozka.objem.toLocaleString("cs-cz",{style:"currency",currency:"CZK",minimumFractionDigits:2}),
						"objem": format_money(polozka.objem),
						"pocet": format_number(polozka.pocet)
					};
					$.each(vyplnit, function (co, cim) { polozka_div.find("." + co).text(cim); });
					if (bublina.parts.stripes[j] && j < stripeLimit) polozka_div.css("border-color", bublina.parts.stripes[j].attr("fill"));

					polozka_div.click(function () {
						list.setFilterValues({ "polozka": [polozka.id] });
						$(this).parent().children().each(function () { $(this).removeClass("selected"); });
						$(this).addClass("selected");
					});

					widget.append(polozka_div);
				});
				widget.hide();

				i++;
			});


			setStars("velky_vuz", false, true);

			$.each(skupiny, function (i, skupina) {

				skupina.bubble.click(function () {
					if (this.isDocked) {
						History.pushState({ view: "index" }, null, WEB_ROOT + "/?instituce=" + INSTITUTION + "&rok=" + YEAR);
						//updateState();
					}
					else {

						History.pushState({ view: "skupina", skupina: skupina.id, "page": 1 }, null, WEB_ROOT + "/skupina/" + skupina.id + "?instituce=" + INSTITUTION + "&rok=" + YEAR);
						//updateState();
					}
				});

			});

			updateState();

			History.Adapter.bind(window, 'statechange', function (e) { updateState(); });

		});
	});

});