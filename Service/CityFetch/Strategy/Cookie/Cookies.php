<?php

namespace Scue\Geo\Service\CityFetch\Strategy\Cookie;

enum Cookies: string
{
    case SELECTED_CITY = 'selected_city';
    case SELECTED_CITY_NAME = 'selected_city_name';
    case STORE = 'store';
}