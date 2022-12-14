# Laravel Upload Filepond Vue


## Installation

Via Composer

``` bash
$ composer require acitjazz/uploadendpoint
```

## Usage
Change Cache driver to array/redis
``` bash
CACHE_DRIVER=array
```

Change Cache driver to array/redis
``` bash
CACHE_DRIVER=array
```
Publish configuration with:

``` bash
php artisan vendor:publish --tag=acitjazz-media-migrations
php artisan vendor:publish --tag=acitjazz-upload-config
php artisan vendor:publish --tag=acitjazz-vue-component
```

migration:

``` bash
php artisan migrate
```

Example usage component:

``` bash
npm install

npm i filepond-plugin-image-preview --save
npm i filepond-plugin-file-validate-type --save
npm i filepond-plugin-image-exif-orientation --save

```

``` bash
<template>
    <div id="component">
        <acit-jazz-upload 
        title="New Title" 
        folder="bucketname"
        name="banners"
        value="[]"
        >
        </acit-jazz-upload>
    </div>
  </template>
  
<script>
    // Import the Component
    import AcitJazzUpload from '@/Components/AcitJazzUpload.vue';

    export default {
    components: {
        AcitJazzUpload,
    },
    };
</script>
```
Property List:

| Property Name  |  Value  | Description|
|---|---|---|
| title  | String  | Image Title
| name  | String  | Name of input
|  folder | String   | Folder/Bucket Name
|  filetype | Array   | ['image/*']
|  value | Json String   | JSON.stringify Array of Object Media
|  placeholder | String   | String or html
| settings  | Object  | see https://pqina.nl/filepond/docs/api/instance/properties/ for more property

## Filepond Doc

https://pqina.nl/filepond/docs/api/

## Credits

- [Acit Jazz][https://github.com/acitjazz]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/acitjazz/uploadendpoint.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/acitjazz/uploadendpoint.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/acitjazz/uploadendpoint/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/acitjazz/uploadendpoint
[link-downloads]: https://packagist.org/packages/acitjazz/uploadendpoint
[link-travis]: https://travis-ci.org/acitjazz/uploadendpoint
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/acitjazz
[link-contributors]: ../../contributors
