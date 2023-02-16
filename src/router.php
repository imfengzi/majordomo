<?php

use Chaos\Majordomo\Majordomo;

if (config('majordomo.auto_register_routers')) {
    Majordomo::routes();
}


