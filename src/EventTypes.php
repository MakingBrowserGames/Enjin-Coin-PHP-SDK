<?php
namespace EnjinCoin;

class EventTypes
{
	const UNKNOWN_EVENT = 0;
	const TXR_PENDING = 1;
	const TXR_CANCELED_USER = 2;
	const TXR_CANCELLED_PLATFORM = 3;
	const TXR_ACCEPTED = 4;
	const TX_BROADCASTED = 5;
	const TX_EXECUTED = 6;
	const TX_CONFIRMED = 7;
	const IDENTITY_CREATED = 8;
	const IDENTITY_LINKED = 9;
	const IDENTITY_UPDATED = 10;
	const IDENTITY_DELETED = 11;

	public $event_types = [
		self::UNKNOWN_EVENT => 'unknown',
		self::TXR_PENDING => 'txr_pending',
		self::TXR_CANCELED_USER => 'txr_cancelled_user',
		self::TXR_CANCELLED_PLATFORM => 'txr_cancelled_platform',
		self::TXR_ACCEPTED => 'txr_accepted',
		self::TX_BROADCASTED => 'tx_broadcasted',
		self::TX_EXECUTED => 'tx_executed',
		self::TX_CONFIRMED => 'tx_confirmed',
		self::IDENTITY_CREATED => 'identity_created',
		self::IDENTITY_LINKED => 'identity_linked',
		self::IDENTITY_UPDATED => 'identity_updated',
		self::IDENTITY_DELETED => 'identity_deleted',
	];
}
