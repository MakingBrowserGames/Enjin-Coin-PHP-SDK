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
					get: {
						title: 'Identities.get',
						open: false,
						params: {
							identities: {
								label: 'Identities',
								type: 'text',
								value: ''
							},
							linked: {
								label: 'Linked',
								type: 'select',
								options: {
									'1': 'True',
									'0': 'False'
								},
								value: 0
							},
							afterIdentityId: {
								label: 'After Identity ID',
								type: 'number',
								value: null
							},
							limit: {
								label: 'Limit',
								type: 'number',
								value: 50
							},
							extraFields: {
								label: 'Extra Fields',
								type: 'text',
								value: ''
							}
						}
					},
					create: {
						title: 'Identities.create',
						open: false,
						params: {
							identity: {
								label: 'Identity',
								type: 'text',
								value: ''
							}
						}
					},
					field: {
						title: 'Identities.field',
						open: false,
						params: {
							key: {
								label: 'Key',
								type: 'text',
								value: ''
							},
							searchable: {
								label: 'Searchable',
								type: 'number',
								value: ''
							},
							displayable: {
								label: 'Displayable',
								type: 'number',
								value: ''
							},
							unique: {
								label: 'Unique',
								type: 'number',
								value: ''
							}
						}
					},
					delete: {
						title: 'Identities.delete',
						open: false,
						params: {
							identity: {
								label: 'Identity',
								type: 'text',
								value: ''
							}
						}
					},
					update: {
						title: 'Identities.update',
						open: false,
						params: {
							identity: {
								label: 'Identity',
								type: 'text',
								value: ''
							},
							update: {
								label: 'Update',
								type: 'text',
								value: ''
							},
							emitEvent: {
								label: 'Emit Event',
								type: 'select',
								options: {
									'1': 'True',
									'0': 'False'
								},
								value: '1'
							}
						}
					},
					link: {
						title: 'Identities.link',
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
								value: ''
							},
							signature: {
								label: 'Signature',
								type: 'text',
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
					get: {
						title: 'Tokens.get',
						open: false,
						params: {
							appId: {
								label: 'App ID',
								type: 'number',
								value: ''
							},
							afterTokenId: {
								label: 'After Token ID',
								type: 'number',
								value: ''
							},
							limit: {
								label: 'Limit',
								type: 'number',
								value: ''
							},
							tokenId: {
								label: 'Token ID',
								type: 'number',
								value: ''
							}
						}
					},
					addToken: {
						title: 'Tokens.addToken',
						open: false,
						params: {
							tokenId: {
								label: 'Token ID',
								type: 'number',
								value: ''
							}
						}
					},
					removeToken: {
						title: 'Tokens.removeToken',
						open: false,
						params: {
							tokenId: {
								label: 'Token ID',
								type: 'number',
								value: ''
							}
						}
					},
					getBalance: {
						title: 'Tokens.getBalance',
						open: false,
						params: {
							identity: {
								label: 'Identity',
								type: 'text',
								value: ''
							},
							tokenId: {
								label: 'Token ID',
								type: 'number',
								value: ''
							}
						}
					}
				}
			},
			events: {
				title: 'Events',
				open: true,
				methods: {
					get: {
						title: 'Events.get',
						open: false,
						params: {
							eventId: {
								label: 'Event ID',
								type: 'number',
								value: ''
							},
							appId: {
								label: 'App ID',
								type: 'number',
								value: ''
							},
							identity: {
								label: 'Identity',
								type: 'text',
								value: ''
							},
							afterEventId: {
								label: 'After Event ID',
								type: 'number',
								value: ''
							},
							beforeEventId: {
								label: 'Before Event ID',
								type: 'number',
								value: ''
							},
							limit: {
								label: 'Limit',
								type: 'number',
								value: ''
							}
						}
					},
					create: {
						title: 'Events.create',
						open: false,
						params: {
							appId: {
								label: 'App ID',
								type: 'number',
								value: ''
							},
							eventType: {
								label: 'Event Type',
								type: 'text',
								value: ''
							},
							data: {
								label: 'Data',
								type: 'text',
								value: ''
							}
						}
					}
				}
			}
		}
	},
	methods: {
		toggleSection: function(section) {
			this.sections[section].open = this.sections[section].open == false;
		},

		toggleMethod: function(section, method) {
			this.sections[section].methods[method].open = this.sections[section].methods[method].open == false;
		}
	},
	mounted: function() {

	}
});