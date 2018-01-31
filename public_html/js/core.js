var Core = {
	jsonrpc: function (className, method, params, callback) {
		var body = {
			'jsonrpc': '2.0',
			'method': method,
			'params': {},
			'id': Core.getRandomNumber(1, 999999)
		};

		if (typeof params !== 'undefined' && params !== null) {
			body.params = params;
		}

		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				var json = JSON.parse(xhr.responseText);
				if (typeof callback !== 'undefined') {
					callback(json.result);
				}
			}
		};

		xhr.open('POST', '/api.php?class=' + className, true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		//xhr.setRequestHeader('X-Auth-Token', token);

		xhr.send(JSON.stringify(body));
	},

	getRandomNumber: function (min, max) {
		return Math.floor(Math.random() * max) + min;
	},

	getRandomEthAddress: function () {
		return '0x0000000000000000000000000000000' + Core.getRandomNumber(100000000, 999999999);
	}
};
