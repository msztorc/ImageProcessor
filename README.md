## ImageProcessor Class

ImageProcessor is a wrapper class for graphics libraries like GD2, ImageMagick and epeg. Implements often used methods of graphics manipulation.

### Requirements
- GD2 (php extension)
- ImageMagick (PECL library)
- EPEG (cli)

#### Library support for object
- GD2
- ImageMagick (PECL library)

#### Library support for static methods
- ImageMagick (some functionality like resize, crop, effects)
- EPEG (cli, only for epeg_resize method)


### Usage Instructions

Objected
```PHP

$obj = new ImageProcessor('imagick', 'file.jpg'); // or 'gd' (default)
$obj->image_resize(100, 50, true, false); //width, height, aspect_ratio, enlarge
$obj->image_clear(); //free memory

```

Staticly image resize using ImageMagick (PECL extension)
```PHP
ImageProcessor::imagick_resize('input-file.jpg', 'output-file.jpg', 800, 800); //infile, outfile, width, height, quality = 100, aspect_ratio = true, filter = imagick::FILTER_LANCZOS

```

Staticly image resize using EPEG (cli)
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
$obj3->image_open('infile.jpg'); //open image

```
#### Base methods
Grayscale
```PHP
$obj->image_grayscale();
```
Negative
```PHP
$obj->image_negative();
```
Brightness
```PHP
$obj->image_brightness($threshold); // +/- 100
```
Colorize
```PHP
$obj->image_colorize(80,90,60); //rgb
```
Sepia
```PHP
$obj->image_sepia();
```
Custom Sepia
```PHP
$obj->image_grayscale();
$obj->image_colorize(90,60,40);
```
Display image
```PHP
$obj->image_display();
```
Save image
```PHP
$obj->image_save('outfile.jpg', 99); //filename, JPEG quality
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

$obj->image_save($outfile, 75);
$obj->image_display();
```

### Resize Performance
#### ImageMagick

JPEG image resize time from 5906x5906 to 1181x1181.

	FILTER_POINT: 0.334532976151 seconds
	FILTER_BOX: 0.777871131897 seconds
	FILTER_TRIANGLE: 1.3695909977 seconds
	FILTER_HERMITE: 1.35866093636 seconds
	FILTER_HANNING: 4.88722896576 seconds
	FILTER_HAMMING: 4.88665103912 seconds
	FILTER_BLACKMAN: 4.89026689529 seconds
	FILTER_GAUSSIAN: 1.93553304672 seconds
	FILTER_QUADRATIC: 1.93322920799 seconds
	FILTER_CUBIC: 2.58396601677 seconds
	FILTER_CATROM: 2.58508896828 seconds
	FILTER_MITCHELL: 2.58368492126 seconds
	FILTER_LANCZOS: 3.74232912064 seconds
	FILTER_BESSEL: 4.03305602074 seconds
	FILTER_SINC: 4.90098690987 seconds 

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
$obj->image_open($file);

$obj->image()->resizeImage(300,300, imagick::FILTER_LANCZOS, 1, true);

$obj->image_save($outfile, 99);

//$time_end = microtime(true);
//$time = $time_end - $time_start;
//echo $time ."\n\n";
```

