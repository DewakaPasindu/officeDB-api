<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
   
    public function index()
    {
        $employees = DB::select("CALL get_all_employees()");
        return response()->json($employees);
    }

   
    public function show($id)
    {
        $employee = DB::select("CALL get_employee_by_id(?)", [$id]);

        if (empty($employee)) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee[0]);
    }

    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'emp_name' => 'required|string',
                'email' => 'required|email|unique:employees,email',
                'dept_id' => 'required|integer',
                'salary' => 'required|numeric'
            ]);

            DB::statement(
                "CALL add_employee(?, ?, ?, ?)",
                [
                    $request->emp_name,
                    $request->email,
                    $request->dept_id,
                    $request->salary
                ]
            );

            return response()->json(['message' => 'Employee added successfully'], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'emp_name' => 'required|string',
                'dept_id' => 'required|integer',
                'salary' => 'required|numeric'
            ]);

            $updated = DB::table('employees')
                ->where('emp_id', $id)
                ->update([
                    'emp_name' => $request->emp_name,
                    'dept_id' => $request->dept_id,
                    'salary' => $request->salary
                ]);

            if (!$updated) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            return response()->json(['message' => 'Employee updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        $deleted = DB::table('employees')->where('emp_id', $id)->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
