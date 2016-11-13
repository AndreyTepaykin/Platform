var Q = require('Q');
var fs = require('fs');
/*

 fs.readFile('/projects/qbix/Q/platform/plugins/Places/tests/Places/countries/en.js', 'utf8', function (err, data) {

 if (err) {
 console.log('Error: ' + err);
 return;
 }
 var obj = JSON.parse(data);

 console.dir(obj);
 //console.log('obj');
 //console.log(obj);
 });*/


var server = require("./server");
server.start();

function parseJsData(filename) {
	var json = fs.readFileSync(filename, 'utf8')
			.replace(/\s*\/\/.+/g, '')
			.replace(/,(\s*\})/g, '}')
		;
	return JSON.parse(json);
}
function parseJSON(countries) {
	//var cbc = Places.countriesByCode;
	var cbc = ["AF", "AX", "AL", "DZ", "AS", "AD", "AO", "AI", "AQ", "AG", "AR", "AM", "AW", "AU", "AT", "AZ", "BS",
		"BH", "BD", "BB", "BY", "BE", "BZ", "BJ", "BM", "BT", "BO", "BQ", "BA", "BW", "BV", "BR", "IO", "BN", "BG", "BF", "BI", "KH", "CM", "CA", "CV", "KY", "CF", "TD", "CL", "CN",
		"CX", "CC", "CO", "KM", "CG", "CD", "CK", "CR", "CI", "HR", "CU", "CW", "CY", "CZ", "DK", "DJ", "DM", "DO", "EC", "EG", "SV", "GQ", "ER", "EE", "ET", "FK", "FO", "FJ", "FI",
		"FR", "GF", "PF", "TF", "GA", "GM", "GE", "DE", "GH", "GI", "GR", "GL", "GD", "GP", "GU", "GT", "GG", "GN", "GW", "GY", "HT", "HM", "HN", "HK", "HU", "IS", "IN", "ID", "IR",
		"IQ", "IE", "IM", "IL", "IT", "JM", "JP", "JE", "JO", "KZ", "KE", "KI", "KP", "KR", "KW", "KG", "LA", "LV", "LB", "LS", "LR", "LY", "LI", "LT", "LU", "MO", "MK", "MG", "MW",
		"MY", "MV", "ML", "MT", "MH", "MQ", "MR", "MU", "YT", "MX", "FM", "MD", "MC", "MN", "ME", "MS", "MA", "MZ", "MM", "NA", "NR", "NP", "NL", "NC", "NZ", "NI", "NE", "NG", "NU",
		"NF", "MP", "NO", "OM", "PK", "PW", "PS", "PA", "PG", "PY", "PE", "PH", "PN", "PL", "PT", "PR", "QA", "RE", "RO", "RU", "RW", "BL", "SH", "KN", "LC", "MF", "PM", "VC", "WS",
		"SM", "ST", "SA", "SN", "RS", "SC", "SL", "SG", "SX", "SK", "SI", "SB", "SO", "ZA", "GS", "SS", "ES", "LK", "SD", "SR", "SJ", "SZ", "SE", "CH", "SY", "TW", "TJ", "TZ", "TH",
		"TL", "TG", "TK", "TO", "TT", "TN", "TR", "TM", "TC", "TV", "UG", "UA", "AE", "GB", "US", "UM", "UY", "UZ", "VU", "VA", "VE", "VN", "VG", "VI", "WF", "EH", "YE", "ZM", "ZW"];

	// test
	var diffResult;
	var new_array = [];

	Array.prototype.diff = function (a) {
		return this.filter(function (i) {
			return a.indexOf(i) < 0;
		});
	};

	for (i = 0; i < countries.length; i++) {

		if (countries[i][1]) {
			var pci = countries[i];
			new_array[i] = pci[1];


			if (i == countries.length - 1) {
				diffResult = cbc.diff(new_array); // comparing to arrays
				console.log('Differences: ', diffResult);
			}
		}
	}
}


//I use this file for test purposes
//var countries = parseJsData('/projects/qbix/Q/platform/plugins/Places/tests/Places/countries/en.js');
//parseJSON(countries);


var express = require('express');
var exec = require("child_process").exec;

exec('php /projects/qbix/Q/platform/plugins/Places/tests/Places/countries.php', function(error, stdout, stderr) {
	console.log('stdout: ', stdout);
	if (error !== null) {
		console.log('exec error: ', error);
	}
});
