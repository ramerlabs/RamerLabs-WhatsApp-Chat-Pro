<?php
defined( 'ABSPATH' ) || exit;

class RLWC_Hours {

	public static function timezone() {
		$settings = RLWC_Settings::get();
		if ( ! empty( $settings['timezone'] ) ) {
			return $settings['timezone'];
		}
		return wp_timezone_string();
	}

	public static function now() {
		try {
			return new DateTimeImmutable( 'now', new DateTimeZone( self::timezone() ) );
		} catch ( Exception $e ) {
			return new DateTimeImmutable( 'now', wp_timezone() );
		}
	}

	public static function is_open() {
		$settings = RLWC_Settings::get();
		$now      = self::now();
		$day_key  = strtolower( $now->format( 'D' ) );
		$map      = array(
			'mon' => 'mon',
			'tue' => 'tue',
			'wed' => 'wed',
			'thu' => 'thu',
			'fri' => 'fri',
			'sat' => 'sat',
			'sun' => 'sun',
		);
		$day      = $map[ $day_key ] ?? 'mon';
		$hours    = $settings['business_hours'][ $day ] ?? null;

		if ( empty( $hours ) || empty( $hours['enabled'] ) ) {
			return false;
		}

		$current = (int) $now->format( 'Hi' );
		$start   = (int) str_replace( ':', '', $hours['start'] );
		$end     = (int) str_replace( ':', '', $hours['end'] );

		return $current >= $start && $current <= $end;
	}

	public static function hours_until_open() {
		$settings = RLWC_Settings::get();
		$now      = self::now();
		$days     = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );

		for ( $offset = 0; $offset < 8; $offset++ ) {
			$check = $now->modify( '+' . $offset . ' days' );
			$day_key = strtolower( $check->format( 'D' ) );
			$map = array(
				'mon' => 'mon', 'tue' => 'tue', 'wed' => 'wed', 'thu' => 'thu',
				'fri' => 'fri', 'sat' => 'sat', 'sun' => 'sun',
			);
			$day = $map[ $day_key ] ?? 'mon';
			$hours = $settings['business_hours'][ $day ] ?? null;

			if ( empty( $hours['enabled'] ) ) {
				continue;
			}

			$start = DateTimeImmutable::createFromFormat(
				'Y-m-d H:i',
				$check->format( 'Y-m-d' ) . ' ' . $hours['start'],
				new DateTimeZone( self::timezone() )
			);

			if ( ! $start ) {
				continue;
			}

			if ( 0 === $offset && self::is_open() ) {
				return 0;
			}

			if ( $start > $now ) {
				$diff = $now->diff( $start );
				return max( 1, (int) ceil( ( $diff->days * 24 ) + $diff->h + ( $diff->i / 60 ) ) );
			}
		}

		return 24;
	}

	public static function status_message() {
		$settings = RLWC_Settings::get();
		if ( self::is_open() ) {
			return $settings['online_message'];
		}
		$hours = self::hours_until_open();
		return str_replace( '{hours}', (string) $hours, $settings['offline_message'] );
	}
}
