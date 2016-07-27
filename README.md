[![Build Status](https://travis-ci.org/msztorc/ImageProcessor.svg?branch=master)](https://travis-ci.org/msztorc/ImageProcessor)

## ImageProcessor Class

ImageProcessor is a wrapper class for graphics libraries like GD2, ImageMagick and epeg. Implements often used methods of graphics manipulation.

### Requirements
- GD2 (php extension)
- ImageMagick (PECL library)
- epeg (cli)

#### Library support for object
- GD2
- ImageMagick (PECL library)

#### Library support for static methods
- ImageMagick (some functionality like resize, crop, effects)
- epeg (cli, only for epeg_resize method)


### Usage Instructions

Objected
```PHP

$obj = new ImageProcessor('imagick', 'file.jpg'); // or 'gd' (default)
$obj->resize(100, 50, true, false); //width, height, aspect_ratio, enlarge
$obj->clear(); //free memory

```

Staticly image resize using ImageMagick (PECL extension)
```PHP
ImageProcessor::imagick_resize('input-file.jpg', 'output-file.jpg', 800, 800); //infile, outfile, width, height, quality = 100, aspect_ratio = true, filter = imagick::FILTER_LANCZOS

```

Staticly image resize using epeg (cli)
```PHP
ImageProcessor::epeg_resize('input-file.jpg', 'output-file.jpg', 800, 800); //infile, outfile, width, height, quality = 100, aspect_ratio = true

```

Load file
```PHP

$obj = new ImageProcessor('imagick', 'infile.jpg');
// or
$obj2 = new ImageProcessor('gd', 'infile.jpg');

// clean constructor
$obj3 = new ImageProcessor(); //gd is default argument in constructor
$obj3->open('infile.jpg'); //open image

```
#### Base methods
Grayscale
```PHP
$obj->grayscale();
```
Negative
```PHP
$obj->negative();
```
Brightness
```PHP
$obj->brightness($threshold); // +/- 100
```
Colorize
```PHP
$obj->colorize(80,90,60); //rgb
```
Sepia
```PHP
$obj->sepia();
```
Custom Sepia
```PHP
$obj->grayscale();
$obj->colorize(90,60,40);
```
Display image
```PHP
$obj->display();
```
Save image
```PHP
$obj->save('outfile.jpg', 99); //filename, JPEG quality
```
You can also work on image resource
```PHP
$obj->image()->resizeImage(1200,1200, imagick::FILTER_LANCZOS, 1, true); //resize using Imagick object; object must be init with second argument 'imagick'
```
```PHP
$obj = new ImageProcessor();
$obj->open_image('imagick', $file);
//$obj->image()->setOption('jpeg:size', '300x300'); //uncomment this if you want increase speed of resize
$obj->image()->resizeImage(300,300, imagick::FILTER_LANCZOS, 1, true);

$obj->save($outfile, 75);
$obj->display();
```

### Resize Performance
#### ImageMagick

JPEG image resize time from 5906x5906 to 1181x1181.

	FILTER_POINT: 0.334532976151 sec
	FILTER_BOX: 0.777871131897 sec
	FILTER_TRIANGLE: 1.3695909977 sec
	FILTER_HERMITE: 1.35866093636 sec
	FILTER_HANNING: 4.88722896576 sec
	FILTER_HAMMING: 4.88665103912 sec
	FILTER_BLACKMAN: 4.89026689529 sec
	FILTER_GAUSSIAN: 1.93553304672 sec
	FILTER_QUADRATIC: 1.93322920799 sec
	FILTER_CUBIC: 2.58396601677 sec
	FILTER_CATROM: 2.58508896828 sec
	FILTER_MITCHELL: 2.58368492126 sec
	FILTER_LANCZOS: 3.74232912064 sec
	FILTER_BESSEL: 4.03305602074 sec
	FILTER_SINC: 4.90098690987 sec

CATROM has a very similar result to LANCZOS, but is significantly faster.
! Note! Above results are only demonstrative. Execution time depends upon the system configuration (processor speed, size of memory, etc...)

You can significantly speed up the processing bigger files using Imagick extension class by setting up `jpeg:size` option before open file:
```PHP
$image = new Imagick();
$image->setOption('jpeg:size', '500x500');
$image->readImage('file.jpg');
```
or using ImageProcessor class
```PHP
//$time_start = microtime(true);

$obj = new ImageProcessor('imagick');
$obj->image()->setOption('jpeg:size', '300x300');
$obj->open($file);

$obj->image()->resizeImage(300,300, imagick::FILTER_LANCZOS, 1, true);

$obj->save($outfile, 99);

//$time_end = microtime(true);
//$time = $time_end - $time_start;
//echo $time ."\n\n";
```

### Links
- epeg (https://github.com/mattes/epeg)

### License
MIT
