<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
        $departments = Department::get();
        foreach ($departments as $value) {
            $value->department_name .= draw_disabled_dropdown($value['status']);
        }
        $categories = Category::get();
        foreach ($categories as $value) {
            $value->category_name .= draw_disabled_dropdown($value['status']);
        }
        $data = array(
            'route' => "/subject-list",
            'actions' => $action_button,
            'departments' => $departments,
            'categories' => $categories,
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
            'department_name,category_name',
            'status',
        );
        $order = $req->input('order')[0]['column'];
        $dir = $req->input('order')[0]['dir'];
        $totalRec = count(Subject::get());
        $search = extract_search_field($req->input('data'));

        $start = $req->input('start');
        $length = $req->input('length');
        $subjects = Subject::offset($start)->limit($length);
        $sortFields = explode(',', $fieldArr[$order]);
        foreach ($sortFields as $value) {
            $subjects->orderBy($value, $dir);
        }
        if(!empty($search['keywords'])) {
            $subjects->where("subject_name", "LIKE", "%{$search['keywords']}%");
        }
        if(!empty($search['department_id'])) {
            $subjects->where("department_id", "=", $search['department_id']);
        }
        if(!empty($search['category_id'])) {
            $subjects->where("category_id", "=", $search['category_id']);
        }
        $subjects = $subjects->get();
        
        $sr = $start + 1;
        $htmlArray = array();
        foreach ($subjects as $subj) {
            $subj->department;
            $subj->category;

            $rec = array();
            $rec['DT_RowId'] = 'sub:' . $subj['id'];
            $rec[] = $sr;
            $rec[] = $subj['subject_name'];
            $details = array(
                'department_name' => $subj['department']['department_name'],
                'category_name' => $subj['category']['category_name']
            );
            $rec[] = getDetails($details, array(
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
        return draw_options($categories, 'id', 'category_name', $req->input('selected'),"<option value=''>Select Category</option>");
    }

    public function export(Request $req) {
        $fieldArr = array(
            'id',
            'subject_name',
            'department_name,category_name',
            'status',
        );

        $order = $req->input('column');
        $dir = $req->input('dir');

        $search = extract_search_field($req->input('data'));

        $subjects = Subject::orderBy($fieldArr[$order], $dir);
        $subjects->select("subjects.*", "departments.department_name", "categories.category_name", DB::raw("IF(subjects.status = '1', 'Enabled', 'Disabled') as status"));

        if(!empty($search['keywords'])) {
            $subjects->where("subject_name", "LIKE", "%{$search['keywords']}%");
        }
        if(!empty($search['department_id'])) {
            $subjects->where("department_id", "=", $search['department_id']);
        }
        if(!empty($search['category_id'])) {
            $subjects->where("category_id", "=", $search['category_id']);
        }

        $subjects->leftjoin("departments", "departments.id", "=", "subjects.department_id");
        $subjects->leftjoin("categories", "categories.id", "=", "subjects.category_id");

        $subjects = $subjects->get();

        $export_structure = array();
        $export_structure[] = array('id'=>array('name'=>'id', 'title'=>'Subject ID'));
        $export_structure[] = array('subject_name'=>array('name'=>'subject_name', 'title'=>'Subject Name'));
        $export_structure[] = array('department_name'=>array('name'=>'department_name', 'title'=>'Deparment Name'));
        $export_structure[] = array('category_name'=>array('name'=>'category_name', 'title'=>'Category Name'));
        $export_structure[] = array('status'=>array('name'=>'status', 'title'=>'Status'));

        $spreadsheet = export_file_generate($export_structure, $subjects, array(
            'headerDate' => 'All',
            'sheetTitle' => 'Subject Report',
        ));

        return export_report($spreadsheet, 'export_subjects.xlsx');
    }
}
