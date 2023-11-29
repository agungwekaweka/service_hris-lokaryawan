<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use DateTime;
use App\Http\Controllers\GenerateIDController;

class ClassUploadImageClass extends Controller
{
    public function processImageLampiran(UploadedFile $image,$idKaryawan,$idMst,$idTrn)
    {
        $tahun = Carbon::now()->format('Y');
        $c_GenerateIDController = new GenerateIDController();
        $idCutiLampiran = $c_GenerateIDController->getIDCutiLampiran();
     
        $imageName = 'IMG_'.$tahun.'_'.$idCutiLampiran.'_'.$idKaryawan.'_'.$idMst.'_'.$idTrn. '.' . $image->getClientOriginalExtension();
        $image = Image::make($image)->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        
        $path = 'lampiran' . '/' . $imageName;
        $image->save(public_path('storage' . DIRECTORY_SEPARATOR . $path));
        return  $path;
    }
}
