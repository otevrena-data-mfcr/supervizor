var List = function (selector) {
	this.dom = $(selector);
	this.isLoading = false;
	this.isOpen = false;
	this.options = {};

	this.dom.find(".or").click(function (e) {
		e.stopPropagation();
		e.preventDefault();
		$.fancybox.open({
			"content": "<iframe src=\"" + $(this).attr("href") + "\" sandbox seamless style=\"width:1170px;height:800px;\"></iframe>",
			"width": "1170",
			"height": "800",
			"autoSize": false
		});
	});

	this.dom.find(".filtr .datum").prepend("<div class=\"dateSlider\"></div>");
	this.dom.find(".datum .dateSlider").dateRangeSlider();
	this.dom.find(".datum .dateSlider").bind("userValuesChanged", function (e, data) {
		$(this).closest(".datum").find("input.min").val(data.values.min);
		$(this).closest(".datum").find("input.max").val(data.values.max);
	});

	var list = this;
	this.dom.find(".filtr .submit button").click(function (e) {

		e.preventDefault();

		var options = {};

		options.polozka = [];
		$(this).closest(".filtr").find(".polozky .polozka input:checked").each(function () {
			options.polozka.push($(this).attr("value"));
		});
		var dr = list.dom.find(".datum .dateSlider").dateRangeSlider("values");
		options.datum = { "min": dr.min.getTime() / 1000, "max": dr.max.getTime() / 1000 };
		list.load(options);

		list.updateFilterStatus();

		$(this).closest(".filtr").find(".filtr_form").toggleClass("open");
		$(this).closest(".filtr").find(".filtr_form .caret").toggleClass("caret-up");
	});

	$(".filtr a.link_filtr").click(function () {
		$(this).parent().find(".filtr_form").toggleClass("open");
		$(this).find(".caret").toggleClass("caret-up");
	});
};
List.prototype.show = function () {
	this.dom.show();
	this.isOpen = true;
};

List.prototype.hide = function () {
	this.dom.hide();
	this.isOpen = false;
};

List.prototype.clear = function () {
	this.dom.find(".dodavatele .dodavatel").not(".template").remove();
	this.dom.find(".detail .faktura").not(".template").remove();
	this.dom.find(".detail .dodavatel").hide();
	this.dom.find(".detail .dodavatel .pole").text("-");
	this.options = {};
};

List.prototype.setLoading = function (state) {
	if (state === true && this.isLoading === false) {
		this.dom.addClass("loading");
		this.isLoading = true;
	}
	else if (state === false && this.isLoading === true) {
		this.dom.removeClass("loading");
		this.isLoading = false;
	}
};

List.prototype.load = function (options, filter) {

	var list = this;

	this.setLoading(true);
	this.clear();

	this.options = options;

	// if (filter) this.setFilter(filter);

	$.getJSON(API_ROOT + "/dodavatele", options, function (dataDodavatele) {
		$.getJSON(API_ROOT + "/faktury", options, function (dataFaktury) {

			var list_dom = list.dom;
			var detail_dom = list.dom.children(".detail");

			// list.redrawPager(data.result.pager);

			$.each(dataDodavatele, function (i, dodavatel) {
				/* casti zaznamu faktury */
				var dodavatel_dom = list_dom.find(".dodavatel.template").clone(true).removeClass("template").appendTo(list_dom.find(".dodavatel.template").parent());

				dodavatel_dom.find(".faktury").hide();

				dodavatel_dom.data("id", dodavatel.id);

				dodavatel.faktury = dataFaktury.filter(function (item) { return item.dodavatel_id = dodavatel.id });

				$.each(dodavatel.faktury, function (j, faktura) {
					var faktura_dom = dodavatel_dom.find(".faktura.template").clone(true).removeClass("template").appendTo(dodavatel_dom.find(".faktura.template").parent());

					faktura_dom.data("faktura_id", faktura.id);

					faktura_dom.attr("title", "ID faktury: " + faktura.id);
					/* naplneni dat faktury */
					var datum_uhrazeno = new Date(faktura.datum_uhrazeno);

					var fakturaData = {
						"faktura_id": faktura.id,
						"castka_celkem": (faktura.castka_am ? faktura.castka_am : 0).toLocaleString("cs-cz", { style: "currency", currency: "CZK", minimumFractionDigits: 2 }),
						"castka_detail": (faktura.detail_castka_am ? faktura.detail_castka_am : 0).toLocaleString("cs-cz", { style: "currency", currency: "CZK", minimumFractionDigits: 2 }),
						"ucel": faktura.ucel_tx,
						"datum_uhrazeno": datum_uhrazeno.getDate() + ". " + (datum_uhrazeno.getMonth() + 1) + ". " + datum_uhrazeno.getFullYear()
					};

					$.each(fakturaData, function (key, value) {
						faktura_dom.find("." + key).text(value);
					});

					faktura_dom.addClass("clickable").click(function () {
						$.fancybox.open({
							type: "iframe",
							href: WEB_ROOT + "/faktura.html?popup=1&faktura=" + faktura.id,
							"width": "600",
							"height": "900",
							"autoSize": false,
							"padding": 0
						});
					});

					if (faktura.ucel_tx == "FKSP") faktura_dom.find(".ucel").append(" <a class=\"fksp\" title=\"Faktury označené FKSP (Fond kulturních a společenských potřeb) jsou faktury, kde nenese celý náklad Ministerstvo financí. MF pouze přidává příspěvek daný kolektivní smlouvou, zbylou částku doplácí zaměstnanci ze své mzdy. V rámci ochrany osobních údajů a soukromí zaměstnanců nejsou detaily faktur zveřejněny.\">?</a>");

				});

				var ico = dodavatel.ico ? ("00000000" + dodavatel.ico).slice(-8) : null;
				var dodavatel_id = dodavatel.id;

				dodavatel_dom.data("ico", ico);

				var dodavatelData = {
					"nazev": dodavatel.nazev,
					"ico": ico,
					"pocet": dodavatel.pocet_celkem_no,
					"castka": (dodavatel.castka_celkem_am ? dodavatel.castka_celkem_am : 0).toLocaleString("cs-cz", { style: "currency", currency: "CZK", minimumFractionDigits: 2 })
				};

				$.each(dodavatelData, function (key, value) {
					dodavatel_dom.find("." + key).text(value);
					dodavatel_dom.find("." + key).text(value);
				});

				if (!ico) dodavatel_dom.find(".radek .info").hide();

				dodavatel_dom.find(".radek").addClass("clickable").click(function () {
					if (!$(this).closest(".dodavatel").hasClass("open")) {

						detail_dom.find(".dodavatel").show();
						list.loadFirm(dodavatel_id);

						detail_dom.find(".faktury").html("").append(dodavatel_dom.find(".faktury").children().clone(true));
						list_dom.find(".dodavatel").removeClass("open");
						dodavatel_dom.addClass("open");

					}
				});

			});

			list.setLoading(false);
		});
	});


};

List.prototype.changeHistory = function (data) {

	var state = History.getState().data;
	if (!state) state = {};
	$.extend(state, data);

	var url = WEB_ROOT + "/" + state.view + "/" + state.skupina + "/" + (state.page ? state.page : 1);
	if (state.dodavatel) url = url + "/dodavatel/" + state.dodavatel;

	History.pushState(state, "", url);
};

List.prototype.turnPage = function (pageNum) {
	if (!pageNum) pageNum = 1;

	var options = this.options;
	options.page = pageNum;
	this.load(options);

	//this.changeHistory({page:pageNum,dodavatel:null});

};

List.prototype.redrawPager = function (data) {
	var pager = this.dom.find(".dodavatele .strankovac");

	pager.children().remove();

	var link = $("<a/>").addClass("stranka");
	var list = this;

	link.addClass("clickable").click(function () { list.turnPage($(this).data("page")); });

	var visible_limit = 5;
	var newlink;

	for (i = 1; i <= data.pages; i++) {
		if (
			i === 1 || // prvni
			i === data.pages || // posledni
			Math.abs(i - data.current) <= (visible_limit - 1) / 2 || // okoli aktualniho
			(data.current <= visible_limit && i <= visible_limit + 1) || // posunute okoli v pripade prvnich par
			(data.pages - data.current < visible_limit && data.pages - i < visible_limit) // posunute okoli v pripade poslednich par
		) {
			newlink = link.clone(true).text(i).data("page", i).appendTo(pager);
			if (i === data.current) newlink.addClass("current");
		}

		if ((i === 1 && data.current > 3 + (visible_limit - 1) / 2) || (i === data.pages - 1 && data.current < data.pages - (visible_limit - 1) / 2)) {
			pager.append("<span class=\"tecky\">&hellip;</span>");
		}
	}

	if (data.next) link.clone(true).html("&raquo;").data("page", data.next).addClass("next").appendTo(pager);
	if (data.previous) link.clone(true).html("&laquo;").data("page", data.previous).addClass("prev").appendTo(pager);
};

List.prototype.loadFirm = function (dodavatel_id, callback) {

	var target = this.dom.find(".detail .dodavatel").addClass("loading");
	var list = this;

	$.getJSON(API_ROOT + "/dodavatele", { limit: 1, instituce: "eq." + INSTITUTION, rok: "eq." + YEAR, skupina_id: "eq." + skupina.id, "id": "eq." + dodavatel_id }, function (data) {

		var target = list.dom.find(".detail .dodavatel");

		var kamos = undefined;
		var db = data[0];

		var ico = db.ico ? ("00000000" + db.ico).slice(-8) : null;

		var dodavatel_data = {
			"nazev": kamos && kamos.company_name ? kamos.company_name : db.nazev,
			"ico": ico,
			"adresa": kamos && kamos.street && kamos.psc && kamos.city ? kamos.street + ", " + kamos.psc + " " + kamos.city : null,
			"zeme": kamos ? kamos.dominant_owner_country : "-",
			"dph": kamos ? (kamos.paysVAT === "true" ? "plátce DPH" : (kamos.paysVAT === "false" ? "neplátce DPH" : null)) : "-"
		};

		$.each(dodavatel_data, function (key, value) {
			target.find("." + key).text(value ? value : "-");
		});



		if (ico && kamos && kamos.law_form_code !== 101 && kamos.law_form_code !== 107 && kamos.law_form_code !== 105) {
			target.find(".or").attr("href", "https://or.justice.cz/ias/ui/rejstrik-%24firma?jenPlatne=VSECHNY&ico=" + ico);
			//target.find("a.or").show().attr("href",WEB_ROOT + "/or-vypis.php?ico=" + ico);
		}
		else {
			target.find("a.or").hide();
		}

		$(target).removeClass("loading");

		if (callback) callback.apply(target);
	});

};

List.prototype.setFilter = function (options) {


	var filtr = this.dom.find(".filtr");

	filtr.find(".polozky .polozka").not(".template").remove();
	filtr.find(".polozky").hide();
	//filtr.find(".datum").hide();

	if (options.datum) {
		filtr.find(".datum").show();
		filtr.find(".datum .dateSlider").dateRangeSlider("option", { bounds: { min: options.datum.min, max: options.datum.max } });
		filtr.find(".datum .dateSlider").dateRangeSlider("resize");
		filtr.find(".datum .dateSlider").dateRangeSlider("values", options.datum.min, options.datum.max);
	}



	if (options.polozky) {
		var polozky = filtr.find(".polozky").show();
		$.each(options.polozky, function (i, polozka) {

			var polozka_dom = polozky.find(".polozka.template").clone(true).removeClass("template");
			var polozka_dom_id = "filtr_polozka_" + polozka.id;

			polozka_dom.data("id", polozka.id);
			polozka_dom.find("input").attr({ "id": polozka_dom_id, "value": polozka.id }).prop("checked", true);
			polozka_dom.find("label").attr("for", polozka_dom_id).text(polozka.nazev);
			polozky.append(polozka_dom);
		});
	}

	this.updateFilterStatus();

};

List.prototype.setFilterValues = function (options) {
	var list = this;

	if (options.polozka) {

		options.polozka = [].concat(options.polozka);

		list.dom.find(".filtr .polozky .polozka").not(".template").each(function () {
			if (options.polozka.indexOf($(this).data("id")) !== -1) $(this).children("input").prop("checked", true);
			else $(this).children("input").prop("checked", false);

		});
		this.options.polozka = options.polozka;
		this.options.page = 1;
	}

	if (options.datum) {
		this.dom.find(".filtr .datum .dateSlider").dateRangeSlider("option", { bounds: { min: options.datum.min, max: options.datum.max } });
		this.options.datum = { "min": options.datum.min.getTime() / 1000, "max": options.datum.max.getTime() / 1000 };
	}

	this.updateFilterStatus();
	this.load(this.options);
};

List.prototype.updateFilterStatus = function () {
	var dr = this.dom.find(".filtr .datum .dateSlider").dateRangeSlider("values");
	var pocet_polozek = this.dom.find(".filtr .polozky input:checked").length;

	var status_string = (dr.min.getMonth() + 1) + "/" + dr.min.getFullYear() + " ~ " + (dr.max.getMonth() + 1) + "/" + dr.max.getFullYear();
	status_string = status_string + ", " + pocet_polozek;

	if (pocet_polozek === 1) status_string = status_string + " položka";
	else if (pocet_polozek > 1 && pocet_polozek < 5) status_string = status_string + " položky";
	else status_string = status_string + " položek";

	this.dom.find(".filtr span.status").text("(" + status_string + ")");
};












