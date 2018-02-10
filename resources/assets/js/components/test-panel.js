import Core from '../core';
import xhr from 'xhr';

Vue.component('test-panel', {
	data: function () {
		return {
			sections: {
				console: {
					title: 'Console',
					open: true,
					value: '',
					methods: {}
				},
				identities: {
					title: 'Identities',
					open: true,
					methods: {
						createIdentity: {
							title: 'Create Identity',
							type: 'post',
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
							type: 'post',
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
							type: 'post',
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
							type: 'post',
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
					methods: {}
				},
				events: {
					title: 'Events',
					open: true,
					methods: {}
				}
			}
		}
	},
	methods: {
		toggleSection: function (section) {
			if (section == 'console') {
				return false;
			}

			this.sections[section].open = this.sections[section].open == false;
		},

		toggleMethod: function (section, method) {
			this.sections[section].methods[method].open = this.sections[section].methods[method].open == false;
		},

		execute: function (section, method) {
			const that = this;
			const btn = event.target;
			btn.innerText = 'Working..';

			let params = {};
			for (let paramKey in this.sections[section].methods[method].params) {
				params[paramKey] = this.sections[section].methods[method].params[paramKey].value;
			}

			xhr({
				method: this.sections[section].methods[method].type,
				body: JSON.stringify(params),
				uri: "/api/v1/testPanel/" + method,
				headers: {
					"Content-Type": "application/json"
				}
			}, function (err, resp, body) {
				that.sections.console.value = body;
			});
		}
	},
	mounted: function () {

	}
});