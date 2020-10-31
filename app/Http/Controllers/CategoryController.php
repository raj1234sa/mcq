<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $action_button = array();
        $action_button['Add Category'] = array(
            'icon' => 'zmdi zmdi-plus',
            'route' => '/category-add',
            'class' => 'btn-success'
        );
        $departments = Department::get();
        foreach ($departments as $value) {
            $value->department_name .= draw_disabled_dropdown($value['status']);
        }
        $data = array(
            'route' => "/category-list",
            'actions' => $action_button,
            'departments' => $departments,
        );
        return view('categories.index', $data);
    }

    public function create()
    {
        $departments = Department::where("status", "=", "1")->get();
        $data = array(
            'route' => '/category-list',
            'form_buttons' => true,
            'departments' => $departments,
            'backlink' => '/category-list',
        );
        return view('categories.add', $data);
    }

    public function save(Request $req)
    {
        $category_name = $req->input('category_name');
        $department_id = $req->input('department_id');
        $this_action = $req->input('this_action');
        $submit_btn = $req->input('submit_btn');
        if (!empty($this_action)) {
            $category = Category::where('id', '=', $this_action)->get();
            $category = $category[0];
        } else {
            $category = new Category();
        }
        $category->category_name = $category_name;
        $category->department_id = $department_id;
        $category->status = '1';
        if ($category->save()) {
            if (isset($this_action) && !empty($this_action)) {
                if ($submit_btn == 'save')
                    return redirect("/category-add/" . $category->id)->with('success', 'Category is update successfully.');
                if ($submit_btn == 'save_back')
                    return redirect("/category-list")->with('success', 'Category is update successfully.');
            } else {
                if ($submit_btn == 'save')
                    return redirect("/category-add/" . $category->id)->with('success', 'Category is added successfully.');
                if ($submit_btn == 'save_back')
                    return redirect("/category-list")->with('success', 'Category is added successfully.');
            }
        }
        if ($this_action == "edit") {
            return redirect()->back()->with('fail', 'Error updating category.');
        } else {
            return redirect()->back()->with('fail', 'Error adding category.');
        }
    }

    public function edit($id)
    {
        $category = Category::where('id', '=', $id)->get();
        $category = $category[0];
        $departments = Department::where("status", "=", "1")->get();
        $data = array(
            'route' => '/category-list',
            'form_buttons' => true,
            'post_data' => $category,
            'this_action' => 'edit',
            'departments' => $departments,
            'backlink' => '/category-list',
        );
        return view('categories.add', $data);
    }

    public function getCategories(Request $req)
    {
        $fieldArr = array(
            'id',
            'category_name',
            'department_name',
            'status',
        );
        $order = $req->input('order')[0]['column'];
        $dir = $req->input('order')[0]['dir'];
        $totalRec = count(Category::get());

        $search = extract_search_field($req->input('data'));

        $start = $req->input('start');
        $length = $req->input('length');
        $categories = Category::offset($start)->limit($length);
        $categories->orderBy($fieldArr[$order], $dir);

        if(!empty($search['keywords'])) {
            $categories->where("category_name", "LIKE", "%{$search['keywords']}%");
        }
        if(!empty($search['department_id'])) {
            $categories->where("department_id", "=", $search['department_id']);
        }

        $categories = $categories->get();

        $sr = $start + 1;
        $htmlArray = array();
        foreach ($categories as $cate) {
            $cate->department;
            $cate->subject;

            $rec = array();
            $rec['DT_RowId'] = 'cat:' . $cate['id'];
            $rec[] = $sr;
            $rec[] = $cate['category_name'];
            $rec[] = $cate['department']['department_name'];
            $currentStatus = $cate['status'] == '1' ? '<span class="text-success">ACTIVE</span>' : '<span class="text-danger">INACTIVE</span>';
            $statusButton = $cate['status'] == '1' ? "<button class='btn btn-dark btn--icon ml-2 ajax change_status' data-status='0' data-url='/category-status' data-id='{$cate['id']}' title='Deactivate'><i class='zmdi zmdi-close'></i></button>" : "<button class='btn btn-dark btn--icon ml-2 ajax change_status' data-status='1' data-url='/category-status' data-id='{$cate['id']}' title='Activate'><i class='zmdi zmdi-check'></i></button>";
            $rec[] = $currentStatus . $statusButton;
            $action_links = array();
            $action_links['Edit'] = array(
                'icon' => 'far fa-edit',
                'link' => '/category-add/' . $cate['id'],
            );
            if(empty($cate['subject']) && $cate['department']) {
                $action_links['Delete'] = array(
                    'class' => 'label-danger ajax delete',
                    'icon' => 'far fa-trash-alt',
                    'link' => '/category-delete/' . $cate['id'],
                );
            }
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

    public function deleteCategory($id)
    {
        if (!empty($id)) {
            $category = Category::find($id);
            $category = $category[0];
            if ($category->delete()) {
                $response = new Response('success');
            } else {
                $response = new Response('Cannot delete category');
            }
            return $response;
        }
    }

    public function changeStatus(Request $req)
    {
        $id = $req->input('id');
        $status = $req->input('status');

        $category = Category::find($id);
        $category->status = $status;
        if ($category->save()) {
            $response = new Response('success');
        } else {
            $response = new Response('Cannot change status of category');
        }
        return $response;
    }
    
    public function export(Request $req) {
        $fieldArr = array(
            'id',
            'category_name',
            'department_name',
            'status',
        );

        $order = $req->input('column');
        $dir = $req->input('dir');

        $search = extract_search_field($req->input('data'));

        $categories = Category::orderBy($fieldArr[$order], $dir);
        $categories->select("categories.*", "departments.department_name", DB::raw("IF(categories.status = '1', 'Enabled', 'Disabled') as status"));

        if(!empty($search['keywords'])) {
            $categories->where("category_name", "LIKE", "%{$search['keywords']}%");
        }
        if(!empty($search['department_id'])) {
            $categories->where("department_id", "=", $search['department_id']);
        }
        $categories->leftjoin("departments", "departments.id", "=", "categories.department_id");

        $categories = $categories->get();

        $export_structure = array();
        $export_structure[] = array('id'=>array('name'=>'id', 'title'=>'Category ID'));
        $export_structure[] = array('category_name'=>array('name'=>'category_name', 'title'=>'Category Name'));
        $export_structure[] = array('department_name'=>array('name'=>'department_name', 'title'=>'Deparment Name'));
        $export_structure[] = array('status'=>array('name'=>'status', 'title'=>'Status'));

        $spreadsheet = export_file_generate($export_structure, $categories, array(
            'headerDate' => 'All',
            'sheetTitle' => 'Category Report',
        ));

        return export_report($spreadsheet, 'export_categories.xlsx');
    }
}
