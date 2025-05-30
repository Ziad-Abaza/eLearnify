<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateGeneratorService
{
    // /**
    //  * Add user data to a certificate template and return generated image path
    //  *
    //  * @param string $templatePath Path to the base64 or uploaded image
    //  * @param array $userData [name, course_name, issue_date]
    //  * @return string New image path (public or storage)
    //  */
    // public function generateCertificateImage(string $templatePath, array $userData): string
    // {
    //     // Load the image from path or base64
    //     $img = Image::make($templatePath);

    //     // Define font style
    //     $font = public_path('fonts/arial.ttf'); 

    //     // Add Name
    //     $img->text($userData['name'], 500, 300, function ($font) use ($font) {
    //         $font->file($font);
    //         $font->size(48);
    //         $font->color('#000000');
    //         $font->align('center');
    //     });

    //     // Add Course Name
    //     $img->text($userData['course_name'], 500, 400, function ($font) use ($font) {
    //         $font->file($font);
    //         $font->size(36);
    //         $font->color('#000000');
    //         $font->align('center');
    //     });

    //     // Add Issue Date
    //     $img->text($userData['issue_date'], 500, 500, function ($font) use ($font) {
    //         $font->file($font);
    //         $font->size(28);
    //         $font->color('#000000');
    //         $font->align('center');
    //     });

    //     // Save the image
    //     $filename = Str::uuid() . '.png';
    //     $path = 'certificates/' . $filename;
    //     $img->save(storage_path('app/public/' . $path));

    //     return $path;
    // }
}
