<?php

if (!function_exists('active_class')) {
  function active_class($path, $active = 'active') {
    return call_user_func_array('Request::is', (array)$path) ? $active : '';
  }
}

if (!function_exists('active_primary_class')) {
  function active_primary_class($path, $active = 'active text-primary') {
    return call_user_func_array('Request::is', (array)$path) ? $active : 'text-body';
  }
}

if (!function_exists('is_active_route')) {
  function is_active_route($path) {
    return call_user_func_array('Request::is', (array)$path) ? 'true' : 'false';
  }
}

if (!function_exists('show_class')) {
  function show_class($path) {
    return call_user_func_array('Request::is', (array)$path) ? 'show' : '';
  }
}

if (!function_exists('camelToSnake')) {
    function camelToSnake($input) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}

if (!function_exists('snakeToCamel')) {
    function snakeToCamel($input) {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
    }
}

if (!function_exists('spaceBeforeCapital')) {
    function spaceBeforeCapital($input) {
        return trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $input));
    }
}

if (!function_exists('convertPrice')) {
    function convertPrice($price) {
      if ($price / 1000000000000 > 1) {
        return "Rp " . $price / 1000000000000 . " Trilion";
      } else if ($price / 1000000000 > 1) {
        return "Rp " . $price / 1000000000 . " Billion";
      } else if ($price / 1000000 > 1) {
        return "Rp " . $price / 1000000 . " Million";
      } else {
        return "Rp" . number_format($price, 0, ',', '.');
      }
    }
}

if (!function_exists('limitText')) {
    function limitText($text, $limit) {
      if (strlen($text) > ($limit - 3)) {
        return substr($text, 0, $limit) . "...";
      } else {
        return $text;
      }
    }
}