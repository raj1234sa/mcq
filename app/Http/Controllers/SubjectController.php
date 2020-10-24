<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubjectController extends Controller
{
    public function index()
    {
        $action_button = array();
        $action_button['Add Subject'] = array(
            'icon' => 'zmdi zmdi-plus',
            'route' => '/subject-add',
            'class' => 'btn-success'
        );
        $data = array(
            'route' => "/subject-list",
            'actions' => $action_button,
        );
        return view('subjects.index', $data);
    }

    public function create()
    {
        $departments = Department::where("status", "=", "1")->get();
        $categories = Category::where("status", "=", "1")->get();
        $data = array(
            'route' => '/subject-list',
            'form_buttons' => true,
            'departments' => $departments,
            'categories' => $categories,
            'backlink' => '/subject-list',
        );
        return view('subjects.add', $data);
    }

    public function save(Request $req)
    {
        $subject_name = $req->input('subject_name');
        $category_id = $req->input('category_id');
        $department_id = $req->input('department_id');
        $this_action = $req->input('this_action');
        $submit_btn = $req->input('submit_btn');
        if (!empty($this_action)) {
            $subject = Subject::where('id', '=', $this_action)->get();
            $subject = $subject[0];
        } else {
            $subject = new Subject();
        }
        $subject->subject_name = $subject_name;
        $subject->category_id = $category_id;
        $subject->department_id = $department_id;
        $subject->status = '1';
        if ($subject->save()) {
            if (isset($this_action) && !empty($this_action)) {
                if ($submit_btn == 'save')
                    return redirect("/subject-add/" . $subject->id)->with('success', 'Subject is update successfully.');
                if ($submit_btn == 'save_back')
                    return redirect("/subject-list")->with('success', 'Subject is update successfully.');
            } else {
                if ($submit_btn == 'save')
                    return redirect("/subject-add/" . $subject->id)->with('success', 'Subject is added successfully.');
                if ($submit_btn == 'save_back')
                    return redirect("/subject-list")->with('success', 'Subject is added successfully.');
            }
        }
        if ($this_action == "edit") {
            return redirect()->back()->with('fail', 'Error updating subject.');
        } else {
            return redirect()->back()->with('fail', 'Error adding subject.');
        }
    }

    public function edit($id)
    {
        $subject = Subject::where('id', '=', $id)->get();
        $subject = $subject[0];
        $departments = Department::where("status", "=", "1")->get();
        $categories = Category::where("status", "=", "1")->get();
        $data = array(
            'route' => '/subject-list',
            'form_buttons' => true,
            'post_data' => $subject,
            'this_action' => 'edit',
            'departments' => $departments,
            'categories' => $categories,
            'backlink' => '/subject-list',
        );
        return view('subjects.add', $data);
    }

    public function getSubjects(Request $req)
    {
        $fieldArr = array(
            'id',
            'subject_name',
            'status',
        );
        $order = $req->input('order')[0]['column'];
        $dir = $req->input('order')[0]['dir'];
        $totalRec = count(Subject::get());
        $start = $req->input('start');
        $length = $req->input('length');
        $subjects = Subject::offset($start)->limit($length);
        $subjects->orderBy($fieldArr[$order], $dir);
        $subjects->select('subjects.*', 'departments.department_name', 'categories.category_name');
        $subjects->join('departments', 'departments.id', '=', 'subjects.department_id');
        $subjects->join('categories', 'categories.id', '=', 'subjects.category_id');
        $subjects = $subjects->get();
        $sr = $start + 1;
        $htmlArray = array();
        foreach ($subjects as $key => $subj) {
            $rec = array();
            $rec['DT_RowId'] = 'sub:' . $subj['id'];
            $rec[] = $sr;
            $rec[] = $subj['subject_name'];
            $rec[] = getDetails($subj, array(
                'department_name' => array('title'=>'Department Name'),
                'category_name' => array('title'=>'Category Name'),
            ));
            $action_links = array();
            $action_links['Edit'] = array(
                'icon' => 'far fa-edit',
                'link' => '/subject-add/' . $subj['id'],
            );
            $action_links['Delete'] = array(
                'class' => 'label-danger ajax delete',
                'icon' => 'far fa-trash-alt',
                'link' => '/subject-delete/' . $subj['id'],
            );
            $currentStatus = $subj['status'] == '1' ? '<span class="text-success">ACTIVE</span>' : '<span class="text-danger">INACTIVE</span>';
            $statusButton = $subj['status'] == '1' ? "<button class='btn btn-dark btn--icon ml-2 ajax change_status' data-status='0' data-url='/subject-status' data-id='{$subj['id']}' title='Deactivate'><i class='zmdi zmdi-close'></i></button>" : "<button class='btn btn-dark btn--icon ml-2 ajax change_status' data-status='1' data-url='/subject-status' data-id='{$subj['id']}' title='Activate'><i class='zmdi zmdi-check'></i></button>";
            $rec[] = $currentStatus . $statusButton;
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

    public function deleteSubject($id)
    {
        if (!empty($id)) {
            $subject = Subject::where("id", '=', $id)->get();
            $subject = $subject[0];
            if ($subject->delete()) {
                $response = new Response('success');
            } else {
                $response = new Response('Cannot delete subject');
            }
            return $response;
        }
    }

    public function changeStatus(Request $req)
    {
        $id = $req->input('id');
        $status = $req->input('status');

        $subject = Subject::where("id", '=', $id)->get();
        $subject = $subject[0];
        $subject->status = $status;
        if ($subject->save()) {
            $response = new Response('success');
        } else {
            $response = new Response('Cannot change status of subject');
        }
        return $response;
    }

    public function getDepartmentCategory(Request $req)
    {
        $dept_id = $req->input('dept_id');
        $categories = Category::where('department_id', '=', $dept_id)->get();
        return draw_options($categories, 'id', 'category_name', '');
    }
}
