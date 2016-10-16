<?php

   # ========================================================================#
   #
   #  Author:    Jarrod Oberto
   #  Version:	 1.0
   #  Date:      17-Jan-10
   #  Purpose:   Resizes and saves image
   #  Requires : Requires PHP5, GD library.
   #  Usage Example:
   #                     include("../../../../../Documents and Settings/peto/Desktop/classes/resize_class.php");
   #                     $resizeObj = new resize('images/cars/large/input.jpg');
   #                     $resizeObj -> resizeImage(150, 100, 0);
   #                     $resizeObj -> saveImage('images/cars/large/output.jpg', 100);
   #
   #
   # ========================================================================#


		Class Image
		{
			// *** Class variables
			private $image;
		    private $width;
		    private $height;
			private $imageResized;
		 	private $mime;
			private $type;
			private $newWidth;
			private $newHeight;
			
			function __construct($fileName)
			{
				// *** Open up the file
				$this->image = $this->openImage($fileName);
			}

			public function GetImageSize(){
				$array['width'] 	= $this->width;
				$array['height']	= $this->height;
				return $array;
			}
			
			## --------------------------------------------------------

			private function openImage($image)
				{
				// Image info
				$info 		  = getimagesize($image);
				$this->mime   = image_type_to_mime_type($info[2]);
				$this->width  = $info[0];
			    $this->height = $info[1];
				$this->type   = $info[2];
				
				if ($info) {
					switch ($info[2]) {
					case IMAGETYPE_PNG:
					return imagecreatefrompng($image);
					case IMAGETYPE_JPEG:
					return imagecreatefromjpeg($image);
					case IMAGETYPE_GIF:
					return imagecreatefromgif($image);
					}
				}
				
				return false;
				}

			## --------------------------------------------------------

			public function resizeImage($newWidth, $newHeight, $option="auto", $filter = false, $display = false)
			{
				$this->newWidth = $newWidth;
				$this->newHeight = $newHeight;
				// *** Get optimal width and height - based on $option
				$optionArray = $this->getDimensions($newWidth, $newHeight, $option);

				$optimalWidth  = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];


				// *** Resample - create image canvas of x, y size
				$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
				
				if (imagetypes() & IMG_PNG) {
					imagesavealpha($this->imageResized, true);
					imagealphablending($this->imageResized, false);
				}
				// echo $optimalWidth . " / " . $optimalHeight ;
				imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);
				
				
				 
				// *** if option is 'crop', then crop too
				if ($option == 'crop') {
					$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
				}
				// filter
				if($filter) { 
					$this->applyFilter($filter); 
				}
				
				if($display){
					header("Content-type:  $this->mime"); 
					$this->finalizeImage(); 
				}
			}

			## --------------------------------------------------------
			private function applyFilter($type){
				if($type === "gray") { 
					imagefilter($this->imageResized, IMG_FILTER_GRAYSCALE);
				} 
			}
			
			
			private function getDimensions($newWidth, $newHeight, $option)
			{

			   switch ($option)
				{
					case 'exact':
						$optimalWidth = $newWidth;
						$optimalHeight= $newHeight;
						break;
					case 'portrait':
						$optimalWidth = $this->getSizeByFixedHeight($newHeight);
						$optimalHeight= $newHeight;
						break;
					case 'landscape':
						$optimalWidth = $newWidth;
						$optimalHeight= $this->getSizeByFixedWidth($newWidth);
						break;
					case 'auto':
						$optionArray = $this->getSizeByAuto($newWidth, $newHeight);
						$optimalWidth = $optionArray['optimalWidth'];
						$optimalHeight = $optionArray['optimalHeight'];
						break;
					case 'crop':
						$optionArray = $this->getOptimalCrop($newWidth, $newHeight);
						$optimalWidth = $optionArray['optimalWidth'];
						$optimalHeight = $optionArray['optimalHeight'];
						break;
				}
				return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
			}

			## --------------------------------------------------------

			private function getSizeByFixedHeight($newHeight)
			{
				$ratio = $this->width / $this->height;
				$newWidth = $newHeight * $ratio;
				return $newWidth;
			}

			private function getSizeByFixedWidth($newWidth)
			{
				$ratio = $this->height / $this->width;
				$newHeight = $newWidth * $ratio;
				return $newHeight;
			}

			private function getSizeByAuto($newWidth, $newHeight)
			{
				if ($this->height < $this->width)
				// *** Image to be resized is wider (landscape)
				{
					$optimalWidth = $newWidth;
					$optimalHeight= $this->getSizeByFixedWidth($newWidth);
				}
				elseif ($this->height > $this->width)
				// *** Image to be resized is taller (portrait)
				{
					$optimalWidth = $this->getSizeByFixedHeight($newHeight);
					$optimalHeight= $newHeight;
				}
				else
				// *** Image to be resizerd is a square
				{
					if ($newHeight < $newWidth) {
						$optimalWidth = $newWidth;
						$optimalHeight= $this->getSizeByFixedWidth($newWidth);
					} else if ($newHeight > $newWidth) {
						$optimalWidth = $this->getSizeByFixedHeight($newHeight);
						$optimalHeight= $newHeight;
					} else {
						// *** Sqaure being resized to a square
						$optimalWidth = $newWidth;
						$optimalHeight= $newHeight;
					}
				}

				return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
			}

			## --------------------------------------------------------

			private function getOptimalCrop($newWidth, $newHeight)
			{

				$heightRatio = $this->height / $newHeight;
				$widthRatio  = $this->width /  $newWidth;

				if ($heightRatio < $widthRatio) {
					$optimalRatio = $heightRatio;
				} else {
					$optimalRatio = $widthRatio;
				}

				$optimalHeight = $this->height / $optimalRatio;
				$optimalWidth  = $this->width  / $optimalRatio;

				return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
			}

			## --------------------------------------------------------

			private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
			{
				// *** Find center - this will be used for the crop
				$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 ); // ( $optimalWidth / 2) - ( $newWidth /2 )
				$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 ); //

				$crop = $this->imageResized;
				//imagedestroy($this->imageResized);

				// *** Now crop from center to exact requested size
				$this->imageResized = imagecreatetruecolor($newWidth , $newHeight);
				if (imagetypes() & IMG_PNG) {
					imagesavealpha($this->imageResized, true);
					imagealphablending($this->imageResized, false);
				}
				imagecopyresampled($this->imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
			}

			## --------------------------------------------------------

			public function saveImage($savePath, $imageQuality="80")
			{
				// *** Get extension
        		$extension = strrchr($savePath, '.');
       			$extension = strtolower($extension);
				
				switch($extension)
				{
					case '.jpg':  
					case '.jpeg':  
						if (imagetypes() & IMG_JPG) {  
							imagejpeg($this->imageResized, $savePath, $imageQuality);  
						}  
						if (imagetypes() & IMG_JPG) {
							/*
							$wm = imagecreatefrompng(dirname(__FILE__)."/wm.png");
							imagealphablending($this->imageResized, true);
							//list($markwidth, $markheight, $type1, $attr1)=getimagesize(dirname(__FILE__)."/wm.png");
							//imagecopymerge($this->imageResized, $wm, ($this->newWidth-$markwidth)>>1, ($this->newHeight-$markheight+70)>>1, 0, 0, $markwidth, $markheight, 80);
							imagecopy($this->imageResized, 
									$wm,
									floor(($this->newWidth > $this->width ? $this->width : $this->newWidth)  / 2) - 120 , 
									floor(($this->newHeight > $this->height ? $this->height : $this->newHeight) / 2)  - 30 , 
									0, 
									0, 
									250, 
									30);
							*/	
							imagejpeg($this->imageResized, $savePath, $imageQuality);
						}
						break; 

					case '.gif':
						if (imagetypes() & IMG_GIF) {
							imagegif($this->imageResized, $savePath);
						}
						break;

					case '.png':
						// *** Scale quality from 0-100 to 0-9
						//$scaleQuality = round(($imageQuality/100) * 9);

						// *** Invert quality setting as 0 is best, not 9
						$invertScaleQuality = 0;

						if (imagetypes() & IMG_PNG) {
							 imagepng($this->imageResized, $savePath, $invertScaleQuality);
						}
						break;

					// ... etc

					default:
						// *** No extension - No save.
						break;
				}

				imagedestroy($this->imageResized);
			}


			## --------------------------------------------------------
			private function finalizeImage($output = ""){
				switch($this->type){
					  case IMAGETYPE_JPEG :
							imagejpeg($this->imageResized, $output, 80);
					  break;
					  case IMAGETYPE_GIF :
							imagegif($this->imageResized);
					  break;
					  case IMAGETYPE_PNG :
							imagepng($this->imageResized, $output, 0);
					  break;
					  default:
						   die();          
					 }
			 
			}
			## --------------------------------------------------------
			public function __destruct(){
				if(is_resource($this->imageResized))  {imagedestroy($this->imageResized); }
				if(is_resource($this->image)) 		{imagedestroy($this->image); }
			}

} //class

?>
