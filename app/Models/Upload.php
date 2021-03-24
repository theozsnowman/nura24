<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;
use File;

class Upload extends Model
{
     /**
     * Upload file in 'public/uploads/YYYYMM folder
     * YYYYMM - current year and month
     * use original file name and add a random string (10 chars) in front of filename
     * return new filename (including YYYYMM subfolder)
     * @return string
     */

    public static function upload_image($request, $filename)
    {
        
        if($request->hasFile($filename)) {

            $width = '1200';
            $height = '600';

            $file = $request->file($filename);
            $originalname = $file->getClientOriginalName();
            $extension = strtolower($file->extension());
            
            if($extension=='jpg' or $extension=='jpeg' or $extension=='png' or $extension=='gif' or $extension=='webp' or $extension=='bmp') {           
                
                $new_filename = Str::random(12).'-'.$originalname;                
                
                $subfolder = date("Ym");

                if(!File::isDirectory('uploads'.DIRECTORY_SEPARATOR.$subfolder)){
                    File::makeDirectory('uploads'.DIRECTORY_SEPARATOR.$subfolder, 0777, true, true);        
                }

                $path_large = 'uploads'.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.$new_filename;

                $path_thumb = 'uploads'.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.'thumb_'.$new_filename;
                $path_thumb_square = 'uploads'.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.'thumb_square_'.$new_filename;
                                             
                Image::make($file)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path_large); // large image

                Image::make($file)->resize(350, 350, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path_thumb);  // thumb 
            
                Image::make($file)->fit(350, 350)->save($path_thumb_square);  // thumb square
                       
                return $subfolder.DIRECTORY_SEPARATOR.$new_filename;
            }
            else return null; // invalid extension
        }
    }


    // create avatar image
    public static function avatar($request, $filename)
    {
        if($request->hasFile($filename)) {
            $file = $request->file($filename);
            $originalname = $file->getClientOriginalName();

            $extension = strtolower($file->extension());
            
            if($extension=='jpg' or $extension=='jpeg' or $extension=='png' or $extension=='gif' or $extension=='webp' or $extension=='bmp') {           
                
                $new_filename = Str::random(12).'-'.$originalname;
                
                $subfolder = date("Ym");
                if(!File::isDirectory('uploads'.DIRECTORY_SEPARATOR.$subfolder)){
                    File::makeDirectory('uploads'.DIRECTORY_SEPARATOR.$subfolder, 0777, true, true);        
                }

                $path_large = 'uploads'.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.$new_filename;
                $path_thumb = 'uploads'.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.'thumb_'.$new_filename;

                // create images with fit (resize and crop)
                Image::make($file)->fit(350)->save($path_large); // large image 350x350
                Image::make($file)->fit(120)->save($path_thumb); // thumb image (120x120)

                return $subfolder.DIRECTORY_SEPARATOR.$new_filename;
            }
            else return null; // invalid extension
        }
    }


     //  upload file (any type)
     public static function upload_file(Request $request, $filename)
     {        
        $file = $request->file($filename);
        $originalname = $file->getClientOriginalName();     
        $new_filename = Str::random(12).'-'.$originalname;          
             
        $subfolder = date("Ym");
        if(!File::isDirectory('uploads'.DIRECTORY_SEPARATOR.$subfolder)){
             File::makeDirectory('uploads'.DIRECTORY_SEPARATOR.$subfolder, 0777, true, true);        
        }
 
        $path = 'uploads'.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.$new_filename;
        move_uploaded_file($file, $path);
       
        return $subfolder.DIRECTORY_SEPARATOR.$new_filename;
     }

}