<?php
namespace EnjinCoin\Util;

/**
 * Class Object
 * @package EnjinCoin\Util
 */
class Object {
	public $data = array();
	/* @var Descriptor[] */
	public $dscr = array();
	public $proto = null;
	public $className = "Object";

	static $protoObject = null;
	static $classMethods = null;
	static $protoMethods = null;
	static $null = null;

	/**
	 * holds the "global" object (on which all global variables exist as properties)
	 * @var GlobalObject
	 */
	static $global = null;

	/**
	 * Object constructor.
	 */
	function __construct() {
		$this->proto = self::$protoObject;
		$args = func_get_args();
		if (count($args) > 0) {
			$this->init($args);
		}
	}

	/**
	 * Sets properties from an array (arguments) similar to:
	 * `array('key1', $value1, 'key2', $value2)`
	 * @param array $arr
	 */
	function init($arr) {
		$len = count($arr);
		for ($i = 0; $i < $len; $i += 2) {
			$this->set($arr[$i], $arr[$i + 1]);
		}
	}

	/**
	 * @param $key
	 * @return mixed|null
	 */
	function get($key) {
		$key = (string) $key;
		if (method_exists($this, 'get_' . $key)) {
			return $this->{'get_' . $key}();
		}
		$obj = $this;
		while ($obj !== Object::$null) {
			if (array_key_exists($key, $obj->data)) {
				return $obj->data[$key];
			}
			$obj = $obj->proto;
		}
		return null;
	}

	/**
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	function set($key, $value) {
		$key = (string) $key;
		if (method_exists($this, 'set_' . $key)) {
			return $this->{'set_' . $key}($value);
		}
		if (!array_key_exists($key, $this->dscr) || $this->dscr[$key]->writable) {
			$this->data[$key] = $value;
		}
		return $value;
	}

	/**
	 * @param $key
	 * @return bool
	 */
	function remove($key) {
		$key = (string) $key;
		if (array_key_exists($key, $this->dscr)) {
			if (!$this->dscr[$key]->configurable) {
				return false;
			}
			unset($this->dscr[$key]);
		}
		unset($this->data[$key]);
		return true;
	}

	/**
	 * determine if the given property exists (don't walk proto)
	 * @param $key
	 * @return bool
	 */
	function hasOwnProperty($key) {
		$key = (string) $key;
		if (method_exists($this, 'get_' . $key)) {
			return true;
		}
		return array_key_exists($key, $this->data);
	}

	/**
	 * determine if the given property exists (walk proto)
	 * @param $key
	 * @return bool
	 */
	function hasProperty($key) {
		$key = (string) $key;
		if ($this->hasOwnProperty($key)) {
			return true;
		}
		$proto = $this->proto;
		if ($proto instanceof Object) {
			return $proto->hasProperty($key);
		}
		return false;
	}

	/**
	 * produce the list of keys (optionally get only enumerable keys)
	 * @param $onlyEnumerable
	 * @return array
	 */
	function getOwnKeys($onlyEnumerable) {
		$arr = array();
		foreach ($this->data as $key => $value) {
			$key = (string) $key;
			if ($onlyEnumerable) {
				$dscr = isset($this->dscr[$key]) ? $this->dscr[$key] : null;
				if (!$dscr || $dscr->enumerable) {
					$arr[] = $key;
				}
			} else {
				$arr[] = $key;
			}
		}
		return $arr;
	}

	/**
	 * produce the list of keys that are considered to be enumerable (walk proto)
	 * @param array $arr
	 * @return array
	 */
	function getKeys(&$arr = array()) {
		foreach ($this->data as $key => $val) {
			$key = (string) $key;
			$dscr = isset($this->dscr[$key]) ? $this->dscr[$key] : null;
			if (!$dscr || $dscr->enumerable) {
				$arr[] = $key;
			}
		}
		$proto = $this->proto;
		if ($proto instanceof Object) {
			$proto->getKeys($arr);
		}
		return $arr;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param bool $writable
	 * @param bool $enumerable
	 * @param bool $configurable
	 * @return mixed
	 */
	function setProp($key, $value, $writable = null, $enumerable = null, $configurable = null) {
		$key = (string) $key;
		if (array_key_exists($key, $this->dscr)) {
			$result = $this->dscr[$key];
			unset($this->dscr[$key]);
		} else {
			$result = new Descriptor(true, true, true);
		}
		//here we do NOT check if it is configurable (this method is only used internally)
		if ($writable !== null) {
			$result->writable = !!$writable;
		}
		if ($enumerable !== null) {
			$result->enumerable = !!$enumerable;
		}
		if ($configurable !== null) {
			$result->configurable = !!$configurable;
		}
		//if all are true don't bother creating a descriptor
		if (!$result->writable || !$result->enumerable || !$result->configurable) {
			$this->dscr[$key] = $result;
		}
		//here we do NOT check for a setter (this method is only used internally)
		$this->data[$key] = $value;
		return $value;
	}

	/**
	 * @param array $props
	 * @param bool|null $writable
	 * @param bool|null $enumerable
	 * @param bool|null $configurable
	 */
	function setProps($props, $writable = null, $enumerable = null, $configurable = null) {
		foreach ($props as $key => $value) {
			$this->setProp($key, $value, $writable, $enumerable, $configurable);
		}
	}

	/**
	 * @param array $methods
	 * @param bool|null $writable
	 * @param bool|null $enumerable
	 * @param bool|null $configurable
	 */
	function setMethods($methods, $writable = null, $enumerable = null, $configurable = null) {
		foreach ($methods as $name => $fn) {
			$func = new Func((string) $name, $fn);
			$func->strict = true;
			$this->setProp($name, $func, $writable, $enumerable, $configurable);
		}
	}

	/**
	 * Get a native associative array containing enumerable own properties
	 * @return array
	 */
	function toArray() {
		$keys = $this->getOwnKeys(true);
		$results = array();
		foreach ($keys as $key) {
			$results[$key] = $this->get($key);
		}
		return $results;
	}

	/**
	 * @param $name
	 * @throws Exception if invalid method called
	 * @return mixed
	 */
	function callMethod($name) {
		/** @var Func $fn */
		$fn = $this->get($name);
		if (!($fn instanceof Func)) {
			Debug::log($this, $name, $fn);
			throw new Ex(Error::create('Invalid method called'));
		}
		$args = array_slice(func_get_args(), 1);
		return $fn->apply($this, $args);
	}

	/**
	 * Similar to callMethod, we can call "internal" methods (dynamically-attached
	 * user functions) which are available in PHP land but not from JS
	 *
	 * @param string $name - method name
	 * @param array $args - arguments with which method was called
	 * @return mixed
	 * @throws Ex
	 */
	function __call($name, $args) {
		if (isset($this->{$name})) {
			return call_user_func_array($this->{$name}, $args);
		} else {
			throw new Ex(Error::create('Internal method `' . $name . '` not found on ' . gettype($this)));
		}
	}

	/**
	 * Creates the global constructor used in user-land
	 * @return Func
	 */
	static function getGlobalConstructor() {
		$object = new Func(function ($value = null) {
			if ($value === null || $value === Object::$null) {
				return new Object();
			} else {
				return objectify($value);
			}
		});
		$object->set('prototype', Object::$protoObject);
		$object->setMethods(Object::$classMethods, true, false, true);
		return $object;
	}
}

/**
 * Class Descriptor
 * @package EnjinCoin\Util
 */
class Descriptor {
	public $writable = true;
	public $enumerable = true;
	public $configurable = true;

	/**
	 * Descriptor constructor.
	 * @param null $writable
	 * @param null $enumerable
	 * @param null $configurable
	 */
	function __construct($writable = null, $enumerable = null, $configurable = null) {
		$this->writable = ($writable === null) ? true : !!$writable;
		$this->enumerable = ($enumerable === null) ? true : !!$enumerable;
		$this->configurable = ($configurable === null) ? true : !!$configurable;
	}

	/**
	 * @param null $value
	 * @return Object
	 */
	function toObject($value = null) {
		$result = new Object();
		$result->set('value', $value);
		$result->set('writable', $this->writable);
		$result->set('enumerable', $this->enumerable);
		$result->set('configurable', $this->configurable);
		return $result;
	}

	/**
	 * @param null $value
	 * @return Object
	 */
	static function getDefault($value = null) {
		return new Object('value', $value, 'writable', true, 'enumerable', true, 'configurable', true);
	}
}

/**
 *
 */
Object::$classMethods = array(
	//todo: getPrototypeOf, seal, freeze, preventExtensions, isSealed, isFrozen, isExtensible
	'create' => function ($proto) {
		if (!($proto instanceof Object) && $proto !== Object::$null) {
			throw new Ex(Error::create('Object prototype may only be an Object or null'));
		}
		$obj = new Object();
		$obj->proto = $proto;
		return $obj;
	},
	'keys' => function ($obj) {
		if (!($obj instanceof Object)) {
			throw new Ex(Error::create('Object.keys called on non-object'));
		}
		return Arr::fromArray($obj->getOwnKeys(true));
	},
	'getOwnPropertyNames' => function ($obj) {
		if (!($obj instanceof Object)) {
			throw new Ex(Error::create('Object.getOwnPropertyNames called on non-object'));
		}
		return Arr::fromArray($obj->getOwnKeys(false));
	},
	'getOwnPropertyDescriptor' => function ($obj, $key) {
		if (!($obj instanceof Object)) {
			throw new Ex(Error::create('Object.getOwnPropertyDescriptor called on non-object'));
		}
		$key = (string) $key;
		if (method_exists($obj, 'get_' . $key)) {
			$hasProperty = true;
			$value = $obj->{'get_' . $key}();
		} else {
			$hasProperty = array_key_exists($key, $obj->data);
			$value = $hasProperty ? $obj->data[$key] : null;
		}
		if (array_key_exists($key, $obj->dscr)) {
			return $obj->dscr[$key]->toObject($value);
		} else if ($hasProperty) {
			return Descriptor::getDefault($value);
		} else {
			return null;
		}
	},
	'defineProperty' => function ($obj, $key, $desc) {
		$key = (string) $key;
		if (!($obj instanceof Object)) {
			throw new Ex(Error::create('Object.defineProperty called on non-object'));
		}
		$writable = $desc->get('writable');
		$enumerable = $desc->get('enumerable');
		$configurable = $desc->get('configurable');
		$updateValue = false;
		if (array_key_exists($key, $obj->data)) {
			if (array_key_exists($key, $obj->dscr)) {
				$result = $obj->dscr[$key];
			} else {
				$result = $obj->dscr[$key] = new Descriptor(true, true, true);
			}
			if (!$result->configurable) {
				throw new Ex(TypeError::create('Cannot redefine property: ' . $key));
			}
			if ($writable !== null) {
				$result->writable = !!$writable;
			}
			if ($enumerable !== null) {
				$result->enumerable = !!$enumerable;
			}
			if ($configurable !== null) {
				$result->configurable = !!$configurable;
			}
			//if all are true don't bother creating a descriptor
			if ($result->writable && $result->enumerable && $result->configurable) {
				unset($obj->dscr[$key]);
			}
			if ($desc->hasProperty('value')) {
				$value = $desc->get('value');
				$updateValue = true;
			}
		} else {
			$writable = ($writable === null) ? false : !!$writable;
			$enumerable = ($enumerable === null) ? false : !!$enumerable;
			$configurable = ($configurable === null) ? false : !!$configurable;
			//if all are true don't bother creating a descriptor
			if (!$writable || !$enumerable || !$configurable) {
				$result = new Descriptor($writable, $enumerable, $configurable);
				$obj->dscr[$key] = $result;
			}
			$value = $desc->get('value');
			$updateValue = true;
		}
		if ($updateValue) {
			if (method_exists($obj, 'set_' . $key)) {
				$obj->{'set_' . $key}($value);
			} else {
				$obj->data[$key] = $value;
			}
		}
	},
	'defineProperties' => function ($obj, $items) {
		if (!($obj instanceof Object)) {
			throw new Ex(Error::create('Object.defineProperties called on non-object'));
		}
		if (!($items instanceof Object)) {
			throw new Ex(Error::create('Object.defineProperties called with invalid list of properties'));
		}
		$defineProperty = Object::$classMethods['defineProperty'];
		foreach ($items->data as $key => $value) {
			$dscr = isset($items->dscr[$key]) ? $items->dscr[$key] : null;
			if (!$dscr || $dscr->enumerable) {
				$defineProperty($obj, $key, $value);
			}
		}
	},
	'getPrototypeOf' => function () {
		throw new Ex(Error::create('Object.getPrototypeOf not implemented'));
	},
	'setPrototypeOf' => function () {
		throw new Ex(Error::create('Object.getPrototypeOf not implemented'));
	},
	'preventExtensions' => function () {
		//throw new Ex(Error::create('Object.preventExtensions not implemented'));
	},
	'isExtensible' => function () {
		//throw new Ex(Error::create('Object.isExtensible not implemented'));
		return false;
	},
	'seal' => function () {
		//throw new Ex(Error::create('Object.seal not implemented'));
	},
	'isSealed' => function () {
		//throw new Ex(Error::create('Object.isSealed not implemented'));
		return false;
	},
	'freeze' => function () {
		//throw new Ex(Error::create('Object.freeze not implemented'));
	},
	'isFrozen' => function () {
		//throw new Ex(Error::create('Object.isFrozen not implemented'));
		return false;
	}
);

/**
 *
 */
Object::$protoMethods = array(
	'hasOwnProperty' => function ($key) {
		$self = Func::getContext();
		//this should implicitly ensure $key is a string
		return array_key_exists($key, $self->data);
	},
	'toString' => function () {
		$self = Func::getContext();
		if ($self === null) {
			$className = 'Undefined';
		} else if ($self === Object::$null) {
			$className = 'Null';
		} else {
			$obj = objectify($self);
			$className = $obj->className;
		}
		return '[object ' . $className . ']';
	},
	'valueOf' => function () {
		return Func::getContext();
	}
);

/**
 * Class NullClass
 * @package EnjinCoin\Util
 */
class NullClass {
}

Object::$null = new NullClass();
//the methods are not set on Object.prototype until *after* Func class is defined
Object::$protoObject = new Object();
Object::$protoObject->proto = Object::$null;