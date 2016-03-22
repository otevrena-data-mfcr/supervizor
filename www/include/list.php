<div id="seznam">
	<div class="filtr">
		<a class="link_filtr">Filtr <span class="status"></span><span class="caret"></span></a>
		<div class="filtr_form">
			<form>
				<div class="col-xs-12 col-sm-12">
					<fieldset class="datum col-xs-12 col-sm-6">
						<legend>Časové rozpětí</legend>
						<input type="date" style="display:none;">
						<input type="date" style="display:none;">	
					</fieldset>
					<fieldset class="polozky col-xs-12 col-sm-6">
						<legend>Rozpočtové položky</legend>
						<span class="polozka template"><input type="checkbox" name="polozka"><label></label></span>
					</fieldset>
				</div>
				<span class="clear"></span>
				
				<fieldset class="submit">
					<button type="submit">Zobrazit</button>
				</fieldset>
			</form>
		</div>
	</div>
	
	<div class="dodavatele col-xs-12 col-sm-6">
		<span class="loader"><span></span></span>
		<div class="seznam">
			<div class="dodavatel template">
				<div class="radek">
					<h3><span class="nazev"></span></h3>
					
					<span class="castka"></span>
				</div>
				<div class="faktury">
					<div>
						<div class="faktura template">
							
							<span class="castka_detail"></span>
							<span class="datum_prijato" title="Datum přijetí faktury"></span>		
							<p class="info"><span class="ucel"></span></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="strankovac">
		</div>
	</div>
	
	<div class="detail col-xs-12 col-sm-6">
		<div>
			<p class="click_to_load"><span class="glyphicon glyphicon-arrow-left"></span> Pro zobrazení faktur klikněte na dodavatele vlevo.</p>
			<div class="dodavatel">
				<span class="loader"><span></span></span>
				<div>
					<h3 class="nazev pole"></h3>
					<p class="info">
						<strong>IČO:</strong> <span class="ico pole">-</span><!--, <span class="dph pole">-</span><br>
						<strong>Sídlo:</strong>	<span class="adresa pole">-</span><br>
						<strong>Země vlastníka:</strong>	<span class="zeme pole">-</span> 
					</p>
					<a class="or">Otevřít dodavatele v obchodním rejstříku</a>      -->
					
				</div>
			</div>
			<div class="faktury">
			</div>
		</div>
	</div>
</div>