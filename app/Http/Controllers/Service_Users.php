<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DateTime;

class Service_Users extends Controller
{
    public function exportUsers(Request $request)
    {
        $idDepartemen = $request->id_departemen;
        $idSubDepartemen = $request->id_sub_departemen;

        $usersController = new UsersController();
        return $usersController->exportUsers($request);
    }
}
