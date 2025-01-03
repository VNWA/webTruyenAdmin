<?php


$config = [];

$config['loadRoutes'] = true;

$config['authentication'] = '\App\Http\Middleware\CustomCKFinderAuth';

/*============================ License Key ============================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_licenseKey

$config['licenseName'] = env('APP_URL');
$config['licenseKey'] = '*5?7-*1**-U**V-*U**-*M**-N*Z*-3**H';

/*============================ CKFinder Internal Directory ============================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_privateDir

$config['privateDir'] = [
    'backend' => 'laravel_cache',
    'tags' => 'ckfinder/tags',
    'cache' => 'ckfinder/cache',
    'thumbs' => 'ckfinder/cache/thumbs',
    'logs' => [
        'backend' => 'laravel_logs',
        'path' => 'ckfinder/logs',
    ],
];

/*============================ Images and Thumbnails ==================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_images

$config['images'] = [
    'maxWidth' => 1600,
    'maxHeight' => 1200,
    'quality' => 80,
    'sizes' => [
        'small' => ['width' => 480, 'height' => 320, 'quality' => 80],
        'medium' => ['width' => 600, 'height' => 480, 'quality' => 80],
        'large' => ['width' => 800, 'height' => 600, 'quality' => 80],
    ],
];

/*=================================== Backends ========================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_backends

// The two backends defined below are internal CKFinder backends for cache and logs.
// Plase do not change these, unless you really want it.
$config['backends']['laravel_cache'] = [
    'name' => 'laravel_cache',
    'adapter' => 'local',
    'root' => storage_path('framework/cache'),
];

$config['backends']['laravel_logs'] = [
    'name' => 'laravel_logs',
    'adapter' => 'local',
    'root' => storage_path('logs'),
];

// Backends

$config['backends']['default'] = [
    'name' => 'default',
    'adapter' => 'local',
    'baseUrl' => env('APP_URL') . '/storage/',
    'root' => storage_path('app/public'),
    'chmodFiles' => 0777,
    'chmodFolders' => 0755,
    'filesystemEncoding' => 'UTF-8',
];

/*================================ Resource Types =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_resourceTypes

$config['defaultResourceTypes'] = '';

$config['resourceTypes'][] = [
    'name' => 'Files', // Single quotes not allowed.
    'directory' => 'files',
    'maxSize' => 0,
    'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xlsx,zip',
    'deniedExtensions' => '',
    'backend' => 'default',
];

$config['resourceTypes'][] = [
    'name' => 'Images',
    'directory' => 'images',
    'maxSize' => 0,
    'allowedExtensions' => 'bmp,gif,jpeg,jpg,png',
    'deniedExtensions' => '',
    'backend' => 'default',
];

/*================================ Access Control =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_roleSessionVar

$config['roleSessionVar'] = 'CKFinder_UserRole';

$config['accessControl'][] = [
    'role' => '*',
    'resourceType' => '*',
    'folder' => '/',

    'FOLDER_VIEW' => true,
    'FOLDER_CREATE' => true,
    'FOLDER_RENAME' => true,
    'FOLDER_DELETE' => true,

    'FILE_VIEW' => true,
    'FILE_UPLOAD' => true,
    'FILE_RENAME' => true,
    'FILE_DELETE' => true,

    'IMAGE_RESIZE' => true,
    'IMAGE_RESIZE_CUSTOM' => true,
];

/*================================ Other Settings =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html

$config['overwriteOnUpload'] = false;
$config['checkDoubleExtension'] = true;
$config['disallowUnsafeCharacters'] = false;
$config['secureImageUploads'] = true;
$config['checkSizeAfterScaling'] = true;
$config['htmlExtensions'] = ['html', 'htm', 'xml', 'js'];
$config['hideFolders'] = ['.*', 'CVS', '__thumbs'];
$config['hideFiles'] = ['.*'];
$config['forceAscii'] = false;
$config['xSendfile'] = false;

// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_debug
$config['debug'] = false;

/*==================================== Plugins ========================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_plugins

$config['plugins'] = [];

/*================================ Cache settings =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_cache

$config['cache'] = [
    'imagePreview' => 24 * 3600,
    'thumbnails' => 24 * 3600 * 365,
];

/*============================ Temp Directory settings ================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_tempDirectory

$config['tempDirectory'] = sys_get_temp_dir();

/*============================ Session Cause Performance Issues =======================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_sessionWriteClose

$config['sessionWriteClose'] = true;

/*================================= CSRF protection ===================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_csrfProtection

$config['csrfProtection'] = true;

/*============================== End of Configuration =================================*/

/**
 * Config must be returned - do not change it.
 */
return $config;
