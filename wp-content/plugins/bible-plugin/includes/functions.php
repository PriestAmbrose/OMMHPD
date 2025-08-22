<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Get a DateTimeZone object that matches the site timezone.
 */
if ( ! function_exists( 'bible_plugin_get_site_timezone' ) ) {
  function bible_plugin_get_site_timezone() {
    $tz_string = get_option( 'timezone_string' );
    if ( $tz_string ) {
      try {
        return new DateTimeZone( $tz_string );
      } catch ( Exception $e ) {
        // fall through to offset handling
      }
    }

    // Fallback: use GMT offset (e.g. +03:00)
    $offset = (float) get_option( 'gmt_offset', 0 );
    $hours  = (int) $offset;
    $minutes = (int) round( abs( ( $offset - $hours ) * 60 ) );
    $sign = $offset < 0 ? '-' : '+';
    $tz_formatted = sprintf( '%s%02d:%02d', $sign, abs( $hours ), $minutes );

    return new DateTimeZone( $tz_formatted );
  }
}



/**
 * Calculate Orthodox Pascha (Easter) date for a given year.
 * Uses the Meeus/Julian algorithm then converts to Gregorian by adding 13 days
 * (the 13-day difference is valid for years 1900-2099). See sources on dates.
 */
if ( ! function_exists( 'bible_plugin_orthodox_pascha' ) ) {
  function bible_plugin_orthodox_pascha( $year, DateTimeZone $tz = null ) {
    if ( ! $tz ) {
      $tz = bible_plugin_get_site_timezone();
    }

    $a = $year % 4;
    $b = $year % 7;
    $c = $year % 19;
    $d = ( 19 * $c + 15 ) % 30;
    $e = ( 2 * $a + 4 * $b - $d + 34 ) % 7;
    $month = (int) floor( ( $d + $e + 114 ) / 31 );
    $day = ( ( $d + $e + 114 ) % 31 ) + 1;

    // Julian calendar Easter date (year-month-day)
    $julian = new DateTimeImmutable( sprintf( '%04d-%02d-%02d', $year, $month, $day ), new DateTimeZone( 'UTC' ) );

    // Convert to Gregorian by adding 13 days (1900-2099)
    $gregorian = $julian->modify( '+13 days' );

    // Make into DateTime in site timezone and normalize to midnight
    $dt = new DateTime( $gregorian->format( 'Y-m-d' ), $tz );
    $dt->setTime( 0, 0, 0 );

    return $dt;
  }
}

/**
 * Simple English ordinal suffix helper (1st, 2nd, 3rd, 4th)
 */
if ( ! function_exists( 'bible_plugin_ordinal' ) ) {
  function bible_plugin_ordinal( $n ) {
    $n = (int) $n;
    if ( in_array( $n % 100, array( 11, 12, 13 ), true ) ) {
      return $n . 'th';
    }
    switch ( $n % 10 ) {
      case 1: return $n . 'st';
      case 2: return $n . 'nd';
      case 3: return $n . 'rd';
      default: return $n . 'th';
    }
  }
}

/**
 * Return liturgical label for "which day/week after Pentecost" for a given DateTime (or now).
 *
 * Examples:
 * - "Sunday of Pentecost"
 * - "Monday of the 1st Week after Pentecost"
 * - "Friday of the 5th Week after Pentecost"
 */
if ( ! function_exists( 'bible_plugin_after_pentecost_label' ) ) {
  function bible_plugin_after_pentecost_label(string $date = null ) {
    $matthew = include __DIR__ . '/bible-readings/matthew.php';
    $mark    = include __DIR__ . '/bible-readings/mark.php';
    $luke    = include __DIR__ . '/bible-readings/luke.php';
    $john    = include __DIR__ . '/bible-readings/john.php';
    $apostle = include __DIR__ . '/bible-readings/apostle.php';
    $slavonic_dates = include __DIR__ . '/bible-readings/dates.php';
    $tz = bible_plugin_get_site_timezone();

    if ( ! $date ) {
      $date = new DateTime( 'now', $tz );
    } else {
      $date = new DateTime($date);
      // ensure timezone and normalize to midnight
      $date->setTimezone( $tz );
    }
    $date->setTime( 0, 0, 0 );

    $year = (int) $date->format( 'Y' );
    $pascha = bible_plugin_orthodox_pascha( $year, $tz );
    $pentecost = clone $pascha;
    $pentecost->modify( '+49 days' ); // Pentecost = Pascha + 49 days (50th day counting)

    // If today is before this year's Pentecost, consider previous year's Pentecost
    if ( $date < $pentecost ) {
      $pascha = bible_plugin_orthodox_pascha( $year - 1, $tz );
      $pentecost = clone $pascha;
      $pentecost->modify( '+49 days' );
    }

    $days_after = (int) floor( ( $date->getTimestamp() - $pentecost->getTimestamp() ) / DAY_IN_SECONDS );
    $days_after_pascha = (int) floor( ( $date->getTimestamp() - $pascha->getTimestamp() ) / DAY_IN_SECONDS );

    if ( $days_after < 0 ) {
      return __( 'Before Pentecost this year', 'bible-plugin' );
    }

    if ( 0 === $days_after ) {
      return __( 'Sunday of Pentecost', 'bible-plugin' );
    }

    // Determine week number: Monday after Pentecost is day 1 -> week 1
    $week_number = intdiv( $days_after - 1, 7 ) + 1;

    // Determine weekday. Use site's weekday names; mapping Monday=0 ... Sunday=6
    $weekday_names = array(
      __( 'Monday', 'bible-plugin' ),
      __( 'Tuesday', 'bible-plugin' ),
      __( 'Wednesday', 'bible-plugin' ),
      __( 'Thursday', 'bible-plugin' ),
      __( 'Friday', 'bible-plugin' ),
      __( 'Saturday', 'bible-plugin' ),
      __( 'Sunday', 'bible-plugin' ),
    );

    // PHP's N returns 1 (Mon) .. 7 (Sun)
    $weekday_index = ( (int) $date->format( 'N' ) + 6 ) % 7; // shift so Monday=0

    $weekday = $weekday_names[ $weekday_index ];

    /* translators: 1: weekday (Monday), 2: ordinal week (3rd) */
    return sprintf( /* translators: "Monday of the 3rd Week after Pentecost" */
      __( '%1$s of the %2$s Week after Pentecost ' . $slavonic_dates[$days_after_pascha], 'bible-plugin' ),
      $weekday,
      bible_plugin_ordinal( $week_number )
    );
  }
}
