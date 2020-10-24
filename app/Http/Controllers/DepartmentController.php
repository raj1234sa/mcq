<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            'backlink' => '/department-list',
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
            'backlink' => '/department-list',
        );
        return view('departments.add', $data);
    }

    public function getDepartments(Request $req)
    {
        $fieldArr = array(
            'id',
            'department_name',
            'status',
        );
        $order = $req->input('order')[0]['column'];
        $dir = $req->input('order')[0]['dir'];
        $totalRec = count(Department::get());
        $start = $req->input('start');
        $length = $req->input('length');
        $departments = Department::offset($start)->limit($length);
        $departments->orderBy($fieldArr[$order], $dir);
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
                'link' => '/department-add/' . $dept['id'],
            );
            $action_links['Delete'] = array(
                'class' => 'label-danger ajax delete',
                'icon' => 'far fa-trash-alt',
                'link' => '/department-delete/' . $dept['id'],
            );
            $currentStatus = $dept['status'] == '1' ? '<span class="text-success">ACTIVE</span>' : '<span class="text-danger">INACTIVE</span>';
            $statusButton = $dept['status'] == '1' ? "<button class='btn btn-dark btn--icon ml-2 ajax change_status' data-status='0' data-url='/department-status' data-id='{$dept['id']}' title='Deactivate'><i class='zmdi zmdi-close'></i></button>" : "<button class='btn btn-dark btn--icon ml-2 ajax change_status' data-status='1' data-url='/department-status' data-id='{$dept['id']}' title='Activate'><i class='zmdi zmdi-check'></i></button>";
            $rec[] = $currentStatus.$statusButton;
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

    public function deleteDepartment($id)
    {
        if(!empty($id)) {
            $department = Department::where("id", '=', $id)->get();
            $department = $department[0];
            if($department->delete()) {
                $response = new Response('success');
            } else {
                $response = new Response('Cannot delete department');
            }
            return $response;
        }
    }

    public function changeStatus(Request $req)
    {
        $id = $req->input('id');
        $status = $req->input('status');

        $department = Department::where("id", '=', $id)->get();
        $department = $department[0];
        $department->status = $status;
        if($department->save()) {
            $response = new Response('success');
        } else {
            $response = new Response('Cannot delete department');
        }
        return $response;
    }
}
