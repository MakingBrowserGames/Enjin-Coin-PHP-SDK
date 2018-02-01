var app = new Vue({
	el: '#app',
	data: {
		sections: {
			console: {
				title: 'Console',
				open: true,
				value: '',
				methods: {

				}
			},
			identities: {
				title: 'Identities',
				open: true,
				methods: {
					createIdentity: {
						title: 'Create Identity',
						open: false,
						params: {
							identity: {
								label: 'Identity Fields',
								type: 'text',
								help: 'Format: player_name|Josh816, uuid|123456789, some_other_key|some_other_value',
								value: ''
							}
						}
					},
					linkIdentity: {
						title: 'Link Identity',
						open: false,
						params: {
							identityCode: {
								label: 'Identity Code',
								type: 'text',
								value: ''
							},
							ethereumAddress: {
								label: 'Ethereum Address',
								type: 'text',
								value: Core.getRandomEthAddress()
							},
							signature: {
								label: 'Signature (optional)',
								type: 'text',
								value: ''
							}
						}
					},
					deleteIdentity: {
						title: 'Delete Identity',
						open: false,
						params: {
							identityCode: {
								label: 'Identity Code',
								type: 'text',
								value: ''
							}
						}
					},
					updateIdentity: {
						title: 'Update Identity',
						open: false,
						params: {
							identityCode: {
								label: 'Identity Code',
								type: 'text',
								value: ''
							},
							identity: {
								label: 'New Identity Fields',
								type: 'text',
								help: 'Format: player_name|Josh816, uuid|123456789, some_other_key|some_other_value',
								value: ''
							}
						}
					}
				}
			},
			tokens: {
				title: 'Tokens',
				open: true,
				methods: {

				}
			},
			events: {
				title: 'Events',
				open: true,
				methods: {}
			}
		}
	},
	methods: {
		toggleSection: function(section) {
			this.sections[section].open = this.sections[section].open == false;
		},

		toggleMethod: function(section, method) {
			this.sections[section].methods[method].open = this.sections[section].methods[method].open == false;
		},

		execute: function (section, method) {
			var btn = event.target;
			btn.innerText = 'Working..';

			var params = {};
			for (var paramKey in this.sections[section].methods[method].params) {
				params[paramKey] = this.sections[section].methods[method].params[paramKey].value;
			}

			var that = this;
			Core.jsonrpc('TestPanel', method, params, function (resp) {
				that.sections.console.value = JSON.stringify(resp);
				btn.innerText = 'Execute';
			});
		}
	},
	mounted: function() {

	}
});