<?php
 
use ImageProcessor\ImageProcessor;
 
class ImageProcessorTest extends PHPUnit_Framework_TestCase {
 
  public function testOpenFileWithDefaultConstructor()
  {
	$imgproc = new ImageProcessor();
	$this->assertEquals($imgproc->libType(), 'gd');
	$imgproc->open(__DIR__ . '/apple.jpg');
	$this->assertEquals($imgproc->type(), IMAGETYPE_JPEG);	

  } 


  public function testOpenFileWithImagick()
  {
	$imgproc = new ImageProcessor('imagick', __DIR__ . '/apple.jpg');
	$this->assertEquals($imgproc->libType(), 'imagick');
	$this->assertEquals($imgproc->type(), IMAGETYPE_JPEG);	

  }



  public function testOpenFileWithGd()
  {
	$imgproc = new ImageProcessor('gd', __DIR__ . '/apple.jpg');
	$this->assertEquals($imgproc->libType(), 'gd');
	$this->assertEquals($imgproc->type(), IMAGETYPE_JPEG);	

  }

  public function testResizeImageUsingGd()
  {
	$imgproc = new ImageProcessor('gd', __DIR__ . '/apple.jpg');
	
	$imgproc->resize(200,200);
	$this->assertEquals($imgproc->width(), 200);

	$this->assertEquals($imgproc->libType(), 'gd');

  }

  public function testResizeImageUsingImagick()
  {
	$imgproc = new ImageProcessor('imagick', __DIR__ . '/apple.jpg');
	
	$imgproc->resize(200,200);
	$this->assertEquals($imgproc->width(), 200);

	$this->assertEquals($imgproc->libType(), 'imagick');

  }

  public function testResizeImageUsingGdStrict()
  {
	$imgproc = new ImageProcessor('gd', __DIR__ . '/apple.jpg');
	
	$imgproc->resize(200,126, false);
	$this->assertEquals($imgproc->width(), 200);
	$this->assertEquals($imgproc->height(), 126);

	$this->assertEquals($imgproc->libType(), 'gd');

  }

  public function testResizeImageUsingImagickStrict()
  {
	$imgproc = new ImageProcessor('imagick', __DIR__ . '/apple.jpg');
	
	$imgproc->resize(200,126, false);
	$this->assertEquals($imgproc->width(), 200);
	$this->assertEquals($imgproc->height(), 126);

	$this->assertEquals($imgproc->libType(), 'imagick');

  }

  public function testRotateImageUsingGd()
  {
	$imgproc = new ImageProcessor('gd', __DIR__ . '/apple.jpg');
	
	$imgproc->resize(200,126, false);
	$this->assertEquals($imgproc->width(), 200);
	$this->assertEquals($imgproc->height(), 126);

	$imgproc->rotate(90);
	$this->assertEquals($imgproc->width(), 126);
	$this->assertEquals($imgproc->height(), 200);

	$this->assertEquals($imgproc->libType(), 'gd');

  }

  public function testRotateImageUsingImagick()
  {
	$imgproc = new ImageProcessor('imagick', __DIR__ . '/apple.jpg');
	
	$imgproc->resize(200,126, false);
	$this->assertEquals($imgproc->width(), 200);
	$this->assertEquals($imgproc->height(), 126);

	$imgproc->rotate(90);
	$this->assertEquals($imgproc->width(), 126);
	$this->assertEquals($imgproc->height(), 200);

	$this->assertEquals($imgproc->libType(), 'imagick');

  }

  public function testCropImageUsingGd()
  {
	$imgproc = new ImageProcessor('gd', __DIR__ . '/apple.jpg');
	
	$imgproc->crop(201,113, 35, 26);
	$this->assertEquals($imgproc->width(), 201);
	$this->assertEquals($imgproc->height(), 113);

	$this->assertEquals($imgproc->libType(), 'gd');

  }

  public function testCropImageUsingImagick()
  {
	$imgproc = new ImageProcessor('imagick', __DIR__ . '/apple.jpg');
	
	$imgproc->crop(201,113,35,26);
	$this->assertEquals($imgproc->width(), 201);
	$this->assertEquals($imgproc->height(), 113);

	$this->assertEquals($imgproc->libType(), 'imagick');

  }

 
}
