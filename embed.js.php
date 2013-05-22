<?php

header('Content-Type: text/javascript');
header_remove('X-Powered-By');

?>
function scoutnetZeit(date, time) {
	var monate = ['', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
	date = date.substr(8) + '. ' + monate[parseInt(date.substr(5, 2), 10)] + ' ' + date.substr(0, 4);
	if (!time) {
		return date;
	}
	return date + ', ' + time.substr(0, 2) + '.' + time.substr(3, 2) + ' Uhr';
}
function scoutnetTermin(termin) {
	var trans = {};
	trans.zeit = scoutnetZeit(termin.start_date, termin.start_time);
	if (termin.start_date != termin.end_date || termin.end_time) {
		trans.zeit += '<br />bis ' + scoutnetZeit(termin.end_date, termin.end_time);
	 }
	trans.titel = termin.title;
	trans.ort = termin.location ? '(' + (termin.zip ? termin.zip + ' ' + termin.location : termin.location) + ')' : '';
	trans.woe = termin.keywords['16'] ? '<img alt="Wö" src="/fileadmin/templates/new/woelfis.png" title="Wölflinge" />' : '';
	trans.pfadi = termin.keywords['18'] ? '<img alt="Pfa" src="/fileadmin/templates/new/pfadis.png" title="Pfadfinderinnen und Pfadfinder" />' : '';
	trans.rr = termin.keywords['126'] ? '<img alt="R/R" src="/fileadmin/templates/new/oldies.png" title="Ranger/Rover" />' : '';
	switch (termin.group_id) {
	case '1937':
		trans.ebene = 'Land';
		break;
	case '2371':
		trans.ebene = 'Bund';
		break;
	default:
		trans.ebene = 'Ring';
	}
	if (termin.url) {
		if (!termin.url_text) {
			termin.url_text = termin.url;
		}
		trans.link = '<a href="' + termin.url + '" title="' + termin.url_text + '"><img alt="Link" src="/fileadmin/templates/new/style/silk/world_link.png" /></a>';
	} else {
		trans.link = '';
	}
	return trans;
}

$(function() {
	$('#kalender').replaceWith('<table class="termine"><thead><th>Datum, Uhrzeit</th><th>Termin, Ort</th><th colspan="3">Stufen</th><th>Ebene</th><th>Link</th></thead><tbody id="kalender" data-prototype="<?php

echo htmlspecialchars('<tr><td>{zeit}</td><td><strong>{titel}</strong><br />{ort}</td><td>{woe}</td><td>{pfadi}</td><td>{rr}</td><td>{ebene}</td><td>{link}</td></tr>');

?>"></tbody></table>');
	$.getJSON('http://api.hanseaten-bremen.de/lv-kalender/scoutnet.json.php', function(data) {
		if (data.kind != 'collection' || data.element_kind != 'event') {
			return; // Woha.
		}
		$.each(data.elements, function(key, value) {
			var zeile = $('#kalender').data('prototype');
			value = scoutnetTermin(value);
			$.each(value, function(index, variable) {
				zeile = zeile.replace('{' + index + '}', variable);
			});
			$('#kalender').append(zeile);
		});
	}).fail(function() {
		console.log('Fehlgeschlagen.');
	});
});
