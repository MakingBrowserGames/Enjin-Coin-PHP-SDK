import xhr from 'xhr';

class Core {
	static getRandomNumber(min, max) {
		return Math.floor(Math.random() * max) + min;
	}

	static getRandomEthAddress() {
		return '0x0000000000000000000000000000000' + Core.getRandomNumber(100000000, 999999999);
	}
}

export default Core;
