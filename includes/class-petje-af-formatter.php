<?php

/**
 * Format data from Petje.af API.
 *
 * Formatters for some specific data from the 
 * Petje.af API.
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * Format data from Petje.af API.
 *
 * @since      2.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */

class Petje_Af_Formatter
{
    public static function interval($plan)
    {
        if (!$plan) return '';

        if ($plan->interval === 'month') {
            return __('per month', 'petje-af');
        } elseif ($plan->interval === 'year') {
            return __('per year', 'petje-af');
        } elseif ($plan->intervalLabel) {
            return __('per ', 'petje-af') . $plan->intervalLabel;
        }
        
        return '';
    }

    public static function amount($amount) {
        $currency = apply_filters('petje_af_currency', '€ %s');
        $number = $amount;
        if (is_numeric( $number ) && floor( $number ) != $number) {
            return sprintf($currency, number_format_i18n( $number, 2 ));
        }
        return sprintf($currency, $number);
    }
}