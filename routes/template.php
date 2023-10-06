<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Template Routes
|--------------------------------------------------------------------------
|
| Noble UI Template
|
*/

Route::get('/', function () {
    return view('noble-ui-template.dashboard');
});

Route::group(['prefix' => 'email'], function () {
    Route::get('inbox', function () {
        return view('noble-ui-template.pages.email.inbox');
    });
    Route::get('read', function () {
        return view('noble-ui-template.pages.email.read');
    });
    Route::get('compose', function () {
        return view('noble-ui-template.pages.email.compose');
    });
});

Route::group(['prefix' => 'apps'], function () {
    Route::get('chat', function () {
        return view('noble-ui-template.pages.apps.chat');
    });
    Route::get('calendar', function () {
        return view('noble-ui-template.pages.apps.calendar');
    });
});

Route::group(['prefix' => 'ui-components'], function () {
    Route::get('accordion', function () {
        return view('noble-ui-template.pages.ui-components.accordion');
    });
    Route::get('alerts', function () {
        return view('noble-ui-template.pages.ui-components.alerts');
    });
    Route::get('badges', function () {
        return view('noble-ui-template.pages.ui-components.badges');
    });
    Route::get('breadcrumbs', function () {
        return view('noble-ui-template.pages.ui-components.breadcrumbs');
    });
    Route::get('buttons', function () {
        return view('noble-ui-template.pages.ui-components.buttons');
    });
    Route::get('button-group', function () {
        return view('noble-ui-template.pages.ui-components.button-group');
    });
    Route::get('cards', function () {
        return view('noble-ui-template.pages.ui-components.cards');
    });
    Route::get('carousel', function () {
        return view('noble-ui-template.pages.ui-components.carousel');
    });
    Route::get('collapse', function () {
        return view('noble-ui-template.pages.ui-components.collapse');
    });
    Route::get('dropdowns', function () {
        return view('noble-ui-template.pages.ui-components.dropdowns');
    });
    Route::get('list-group', function () {
        return view('noble-ui-template.pages.ui-components.list-group');
    });
    Route::get('media-object', function () {
        return view('noble-ui-template.pages.ui-components.media-object');
    });
    Route::get('modal', function () {
        return view('noble-ui-template.pages.ui-components.modal');
    });
    Route::get('navs', function () {
        return view('noble-ui-template.pages.ui-components.navs');
    });
    Route::get('navbar', function () {
        return view('noble-ui-template.pages.ui-components.navbar');
    });
    Route::get('pagination', function () {
        return view('noble-ui-template.pages.ui-components.pagination');
    });
    Route::get('popovers', function () {
        return view('noble-ui-template.pages.ui-components.popovers');
    });
    Route::get('progress', function () {
        return view('noble-ui-template.pages.ui-components.progress');
    });
    Route::get('scrollbar', function () {
        return view('noble-ui-template.pages.ui-components.scrollbar');
    });
    Route::get('scrollspy', function () {
        return view('noble-ui-template.pages.ui-components.scrollspy');
    });
    Route::get('spinners', function () {
        return view('noble-ui-template.pages.ui-components.spinners');
    });
    Route::get('tabs', function () {
        return view('noble-ui-template.pages.ui-components.tabs');
    });
    Route::get('tooltips', function () {
        return view('noble-ui-template.pages.ui-components.tooltips');
    });
});

Route::group(['prefix' => 'advanced-ui'], function () {
    Route::get('cropper', function () {
        return view('noble-ui-template.pages.advanced-ui.cropper');
    });
    Route::get('owl-carousel', function () {
        return view('noble-ui-template.pages.advanced-ui.owl-carousel');
    });
    Route::get('sweet-alert', function () {
        return view('noble-ui-template.pages.advanced-ui.sweet-alert');
    });
});

Route::group(['prefix' => 'forms'], function () {
    Route::get('basic-elements', function () {
        return view('noble-ui-template.pages.forms.basic-elements');
    });
    Route::get('advanced-elements', function () {
        return view('noble-ui-template.pages.forms.advanced-elements');
    });
    Route::get('editors', function () {
        return view('noble-ui-template.pages.forms.editors');
    });
    Route::get('wizard', function () {
        return view('noble-ui-template.pages.forms.wizard');
    });
});

Route::group(['prefix' => 'charts'], function () {
    Route::get('apex', function () {
        return view('noble-ui-template.pages.charts.apex');
    });
    Route::get('chartjs', function () {
        return view('noble-ui-template.pages.charts.chartjs');
    });
    Route::get('flot', function () {
        return view('noble-ui-template.pages.charts.flot');
    });
    Route::get('morrisjs', function () {
        return view('noble-ui-template.pages.charts.morrisjs');
    });
    Route::get('peity', function () {
        return view('noble-ui-template.pages.charts.peity');
    });
    Route::get('sparkline', function () {
        return view('noble-ui-template.pages.charts.sparkline');
    });
});

Route::group(['prefix' => 'tables'], function () {
    Route::get('basic-tables', function () {
        return view('noble-ui-template.pages.tables.basic-tables');
    });
    Route::get('data-table', function () {
        return view('noble-ui-template.pages.tables.data-table');
    });
});

Route::group(['prefix' => 'icons'], function () {
    Route::get('feather-icons', function () {
        return view('noble-ui-template.pages.icons.feather-icons');
    });
    Route::get('flag-icons', function () {
        return view('noble-ui-template.pages.icons.flag-icons');
    });
    Route::get('mdi-icons', function () {
        return view('noble-ui-template.pages.icons.mdi-icons');
    });
});

Route::group(['prefix' => 'general'], function () {
    Route::get('blank-page', function () {
        return view('noble-ui-template.pages.general.blank-page');
    });
    Route::get('faq', function () {
        return view('noble-ui-template.pages.general.faq');
    });
    Route::get('invoice', function () {
        return view('noble-ui-template.pages.general.invoice');
    });
    Route::get('profile', function () {
        return view('noble-ui-template.pages.general.profile');
    });
    Route::get('pricing', function () {
        return view('noble-ui-template.pages.general.pricing');
    });
    Route::get('timeline', function () {
        return view('noble-ui-template.pages.general.timeline');
    });
});

Route::group(['prefix' => 'auth'], function () {
    Route::get('login', function () {
        return view('noble-ui-template.pages.auth.login');
    });
    Route::get('register', function () {
        return view('noble-ui-template.pages.auth.register');
    });
});

Route::group(['prefix' => 'error'], function () {
    Route::get('404', function () {
        return view('noble-ui-template.pages.error.404');
    });
    Route::get('500', function () {
        return view('noble-ui-template.pages.error.500');
    });
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});
