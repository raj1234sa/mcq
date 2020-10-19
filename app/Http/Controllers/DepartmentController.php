<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index()
    {
        $action_button = array();
        $action_button['Add Department'] = array(
            'icon' => 'zmdi zmdi-plus',
            'route' => '/department-add',
            'class' => 'btn-success'
        );
        $data = array(
            'route' => "/department-list",
            'actions' => $action_button,
        );
        return view('departments.index', $data);
    }

    public function create()
    {
        $data = array(
            'route' => '/department-list',
            'form_buttons' => true,
        );
        return view('departments.add', $data);
    }

    public function save(Request $req)
    {
        $department_name = $req->input('department_name');
        $this_action = $req->input('this_action');
        $submit_btn = $req->input('submit_btn');
        if(!empty($this_action)) {
            $department = Department::where('id', '=', $this_action)->get();
            $department = $department[0];
        } else {
            $department = new Department();
        }
        $department->department_name = $department_name;
        $department->status = '1';
        if ($department->save()) {
            if (isset($this_action) && !empty($this_action)) {
                if ($submit_btn == 'save')
                    return redirect("/department-add/" . $department->id)->with('success', 'Department is update successfully.');
                if ($submit_btn == 'save_back')
                    return redirect("/department-list")->with('success', 'Department is update successfully.');
            } else {
                if ($submit_btn == 'save')
                    return redirect("/department-add/" . $department->id)->with('success', 'Department is added successfully.');
                if ($submit_btn == 'save_back')
                    return redirect("/department-list")->with('success', 'Department is added successfully.');
            }
        }
        if ($this_action == "edit") {
            return redirect()->back()->with('fail', 'Error updating department.');
        } else {
            return redirect()->back()->with('fail', 'Error adding department.');
        }
    }

    public function edit($id)
    {
        $department = Department::where('id', '=', $id)->get();
        $department = $department[0];
        $data = array(
            'route' => '/department-list',
            'form_buttons' => true,
            'post_data' => $department,
            'this_action' => 'edit',
        );
        return view('departments.add', $data);
    }

    public function getDepartments(Request $req)
    {
        $fieldArr = array(
            '',
            'id',
            'name',
            'price',
            'status',
        );
        $totalRec = count(Department::get());
        $start = $req->input('start');
        $length = $req->input('length');
        $departments = Department::offset($start)->limit($length);
        $departments = $departments->get();
        $sr = $start+1;
        foreach ($departments as $key => $dept) {
            $rec = array();
            $rec['DT_RowId'] = 'serv:' . $dept['id'];
            $rec[] = $sr;
            $rec[] = $dept['department_name'];
            $action_links = array();
            $action_links['Edit'] = array(
                'icon' => 'far fa-edit',
                'link' => 'department-add/' . $dept['id'],
            );
            $action_links['Delete'] = array(
                'class' => 'label-danger ajax delete',
                'icon' => 'far fa-trash-alt',
                'link' => 'department-delete/' . $dept['id'],
            );
            $rec[] = draw_action_menu($action_links);
            $htmlArray[] = $rec;
            $sr++;
        }
        return array(
            'data' => $htmlArray,
            'recordsTotal' => $totalRec,
            'recordsFiltered' => $totalRec,
            'draw' => $req->input('draw'),
        );
    }
}
