<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


class AjaxController extends Controller
{
    /**
     * @param $ajax_method
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($ajax_method, Request $request)
    {

        $method = $this->getMethod($ajax_method);

        if ($method) {
            return $this->{$method}($request);
        } else {
            return response()->json(['success' => false, 'message' => "invalid method name '$ajax_method'"]);
        }
    }

    /**
     * @param $table
     * @param $field
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function make($table, $field, Request $request)
    {
        $primaryKey = $request->input('primaryKey');
        $model = $request->get('model');
        $value = $request->get('value');
        $status = 0;
        $result = [
            'model' => $model,
            'value' => $value,
            'table' => $table,
            'field' => $field,
            'primaryKey' => $primaryKey,
            'status' => $status,
        ];

        if ($model) {
            $model = Str::studly($model);
        }


        //print_r($result); die;
        // Check parameters
        /*if (!auth()->check() || !auth()->user()->can(Permission::getStaffPermissions())) {
            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
        }*/
        if (!Schema::hasTable($table)) {
            $result['error'] = 'Table not found.';
            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
        }
        if (!Schema::hasColumn($table, $field)) {
            $result['error'] = 'Column not found.';
            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
        }

        $sql = 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.DB::getDatabaseName().'" AND TABLE_NAME = "' . DB::getTablePrefix() . $table . '" AND COLUMN_NAME = "' . $field . '"';
        $info = DB::select(dbRaw($sql));

        if (empty($info)) {
            $result['error'] = 'Info not found.';
            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            if (isset($info[0]) && isset($info[0]->DATA_TYPE)) {
                $result['error'] = $info;
                if ($info[0]->DATA_TYPE != 'tinyint' && $table != 'settings') {
                    return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
                }
                if ($info[0]->DATA_TYPE != 'text' && $table == 'settings' && $field == 'value') {
                    return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
            }
        }

        // Get model namespace
        $_namespace = '\\App\\Models\\';
        $_model = null;

        if (class_exists($_namespace . $model)) {
            $_model = $_namespace . $model;
        } else {
            // Get model name
            $modelsPath = app_path('Models');
            $modelFiles = array_filter(File::glob($modelsPath . '/' . '*.php'), 'is_file');
            if (count($modelFiles) > 0) {
                foreach ($modelFiles as $filePath) {
                    $filename = last(explode('/', $filePath));
                    $modelName = head(explode('.', $filename));

                    if (
                        !Str::contains(strtolower($filename), '.php')
                        || Str::contains(strtolower($modelName), 'base')
                    ) {
                        continue;
                    }

                    eval('$modelChecker = new ' . $_namespace . $modelName . '();');
                    if (Schema::hasTable($modelChecker->getTable())) {
                        if ($modelChecker->getTable() == $table) {
                            $_model = $_namespace . $modelName;
                            break;
                        }
                    }
                }
            }
        }

        // Get table data
        $item = null;
        if (!empty($_model)) {
            $item = $_model::find($primaryKey);
        }

        // Check item
        if (empty($item)) {
            if (method_exists($_model, 'trashed')) {
                $result['message'] = "You cannot modify deleted items.";
            }
            return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
        }

        // UPDATE - the tinyint field

        // Save data
        $item->{$field} = ($item->{$field} != 1) ? 1 : 0;
        $item->save();


        // JS data
        $result = [
            'table' => $table,
            'field' => $field,
            'primaryKey' => $primaryKey,
            'status' => 1,
            'fieldValue' => $item->{$field},
        ];

        if (isset($isDefaultCountry)) {
            $result['isDefaultCountry'] = $isDefaultCountry;
        }
        if (isset($resImport)) {
            $result['resImport'] = $resImport;
        }


        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function checkUsername(Request $request)
    {
        $username = $request->get('username');
        $id = $request->get('id');

        $query = User::where('username', $username);
        // Check post url
        if ($id) {
            $query->where('id', '<>', $id);
        }

        $slug = $query->count();
        if ($slug) {
            return response()->json(['valid' => false, 'msg' => 'The username has already been taken.']);
        }
        return response()->json(['valid' => true, 'msg' => 'Username is available.']);
    }

    public function checkEmail(Request $request)
    {
        $username = $request->get('email');
        $id = $request->get('id');

        $query = User::where('email', $username);
        // Check post url
        if ($id) {
            $query->where('id', '<>', $id);
        }

        $slug = $query->count();
        if ($slug) {
            return response()->json(['valid' => false, 'msg' => 'The email has already been taken.']);
        }
        return response()->json(['valid' => true, 'msg' => 'Email is available.']);
    }

    public function usersList(Request $request)
    {
        $q = $request->get('q');
        $majlis = $request->get('mid');
        $limit = $request->get('limit', 10);
        $words_tab = preg_split('/[\s,\+]+/', $q);


        $_select[] = " (CASE WHEN first_name LIKE '" . addslashes($q) . "%' THEN 300 ELSE 0 END) ";
        $_select[] = " (CASE WHEN first_name LIKE '% " . trim(addslashes($q)) . "%' THEN 200 ELSE 0 END) ";

        $_select[] = " (CASE WHEN last_name LIKE '" . addslashes($q) . "%' THEN 300 ELSE 0 END) ";
        $_select[] = " (CASE WHEN last_name LIKE '% " . trim(addslashes($q)) . "%' THEN 200 ELSE 0 END) ";

        $_tmp = [];
        foreach ($words_tab as $word) {
            if (strlen($word) > 1) {
                //$_tmp[] = table($table) . ".name LIKE '%$word%'";
                $_select[] = " (CASE WHEN first_name LIKE '%" . addslashes($word) . "%' THEN 20 ELSE 0 END) ";
                $_select[] = " (CASE WHEN last_name LIKE '%" . addslashes($word) . "%' THEN 10 ELSE 0 END) ";
            }
        }
        if (count($_tmp) > 0) {
            $_select[] = " (CASE WHEN " . implode(' || ', $_tmp) . " THEN 10 ELSE 0 END) ";
        }

        $clients = User::selectRaw("id, first_name,last_name,avatar, (" . dbRaw(implode("+", $_select) . ") as relevance"))
            ->active()
            ->when($majlis, function ($query, $majlis) use ($q) {
                $query->where('majlis_id', $majlis);
            })
            ->having('relevance', '>=', 10)->orderBy('relevance', 'DESC')
            ->limit($limit)
            ->get();

        return response()->json(['success' => true, 'data' => $clients->toArray()]);
    }

    private function getMethod($str)
    {
        if (Str::startsWith($str, 'get')) {
            $str = substr($str, 3);
        }

        $str = str_replace(['-', '_', '.'], ' ', $str);
        $str = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1 ', $str));
        $str = preg_replace('/\s+/u', '', ucwords($str));

        if (method_exists($this, $str)) {
            return $str;
        }

        if (method_exists($this, 'get' . $str)) {
            return 'get' . $str;
        }

        if (method_exists($this, 'set' . $str)) {
            return 'set' . $str;
        }

        return false;
    }
}
