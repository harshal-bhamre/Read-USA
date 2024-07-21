<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class CommonController extends Controller
{
    public function delete(Request $request)
    {
        // dd("hello");
        if ($request->ajax()) {
            $resp = $this->deleteUser($request->id, $request->model);
            return $resp;
        }
    }

    protected function deleteUser($id, $model)
    {
        $model = "App\\Models\\" . $model;
        $data = $model::find(decrypt($id));
        if (!$data) {
            return response($model . " not found", 404);
        }

        DB::beginTransaction();
        try {
            $data->delete();
            if ($data->user_id) {
                User::where("id", $data->user_id)->delete();
            }
            DB::commit();
            return response("user deleted successfully", 200);
        } catch (\Exception $e) {
            return response("Failed to delete user", 500);
        }
    }

 
}
